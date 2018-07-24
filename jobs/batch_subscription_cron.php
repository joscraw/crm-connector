<?php

use CRMConnector\AlgoliaAdapter;
use CRMConnector\Api\MailChimp;
use CRMConnector\Api\Models\MailChimp\Creds;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Crons\Initializers\BatchSubscriptionCronInitializer;
use CRMConnector\Utils\Logger;

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/../../../../wp-load.php' );


global $wpdb;


$results = $wpdb->get_results(sprintf("SELECT id, import_id, export_id, list_id, failed_attempts, type FROM %s%s WHERE status = '%s' AND failed_attempts <= 3 ORDER BY created_at DESC",
    $wpdb->prefix,
    'batch_subscription_crons',
    'IN_PROGRESS'
    ));

foreach($results as $result)
{
    // Let's set the default import failed status to false.
    // and assume that it will succeed
    $import_failed = false;
    $cron_id = $result->id;
    $import_id = $result->import_id;
    $export_id = $result->export_id;
    $list_id = $result->list_id;
    $type = $result->type;
    $algolia_object_ids = null;
    $logger = new Logger();

    $logger->write(sprintf("Initializing Cron..."));
    BatchSubscriptionCronInitializer::set_log_file($cron_id, $logger);
    BatchSubscriptionCronInitializer::progress_cron($cron_id);

    if(!$import_id && !$export_id || !$list_id)
    {
        $logger->write("Cron failed. Insufficient data. Missing import id, export id, or list id.");
        BatchSubscriptionCronInitializer::fail_cron($cron_id);
        continue;
    }

    $logger->write(sprintf("Initializing %s for MailChimp List %s",($type === 'unsubscribed') ? 'Unsubscribe' : 'Subscribe',  $list_id));

    if($import_id)
    {
        $logger->write(sprintf("Initializing %s for import %s", ($type === 'unsubscribed') ? 'Unsubscribe' : 'Subscribe', $import_id));
        $algolia_object_ids = $wpdb->get_var(sprintf("SELECT algolia_object_ids FROM %s%s WHERE id = %s", $wpdb->prefix, 'imports', $import_id));
    }

    if($export_id)
    {
        $logger->write(sprintf("Initializing %s for export %s", ($type === 'unsubscribed') ? 'Unsubscribe' : 'Subscribe', $export_id));
        $logger->write("Fetching Algolia Object IDs from Database.");
        $algolia_object_ids = $wpdb->get_var(sprintf("SELECT algolia_object_ids FROM %s%s WHERE id = %s", $wpdb->prefix, 'exports', $export_id));
    }

    if(!$algolia_object_ids)
    {
        $logger->write("Cron failed. Couldn't fetch Algolia object ids.");
        BatchSubscriptionCronInitializer::fail_cron($cron_id);
        continue;
    }

    $object_ids = unserialize($algolia_object_ids);

    $logger->write(sprintf("Count of Requested Email Addresses to %s in MailChimp: %s", ($type === 'unsubscribed') ? 'Unsubscribe' : 'Subscribe', count($object_ids)));

    $algoliaAdapter = new AlgoliaAdapter(get_option('crmc_algolia_application_id'), get_option('crmc_algolia_api_key'), get_option('crmc_algolia_index'));

    $response = null;

    $emails =[];
    $chunks = array_chunk($object_ids, AlgoliaAdapter::MAX_FETCH_OBJECTS_PER_REQUEST);
    foreach($chunks as $chunk)
    {
        try
        {
            $logger->write("Fetching records from Algolia using object ids from database.");
            $response = $algoliaAdapter->getObjects($chunk);
        }
        catch(\Exception $exception)
        {
            $logger->write(sprintf("Not able to fetch records from Algolia using object ids from database. Error: %s", (string) $exception->getResponse()->getBody()->getContents()));
            $import_failed = true;
        }

        foreach($response['results'] as $result)
        {
            $emails[] = (object) ['email_address' => $result['Personal Email']];
        }
    }

    if($import_failed)
        continue;

    $logger->write("De-duplicating email addresses for MailChimp");
    $emails = array_unique($emails, SORT_REGULAR);

    $logger->write(sprintf("Count of Actual Email Addresses to %s into MailChimp after De-duping: %s", ($type === 'unsubscribed') ? 'Unsubscribe' : 'Subscribe', count($emails)));

    foreach($emails as $email)
    {
        $email->status = ($type === 'unsubscribed') ? 'unsubscribed' : 'subscribed';
    }

    $chunks = array_chunk($emails, 250);
    foreach($chunks as $chunk)
    {
        $creds = new Creds;
        $creds->api_key = get_option('crmc_mailchimp_api_key', null);
        $creds->username = get_option('crmc_mailchimp_username', null);

        $mailchimp_api = MailChimp::instance();
        try
        {
            $logger->write(sprintf("%s %s email addresses into MailChimp", ($type === 'unsubscribed') ? 'Unsubscribing' : 'Subscribing', count($chunk)));
            $memberCollection = new \stdClass();
            $memberCollection->update_existing = true;
            $memberCollection->members = $chunk;
            $mailchimp_api->batch_sub_unsub_members($creds, $memberCollection, $list_id);
        }
        catch(\Exception $exception)
        {
            $logger->write(sprintf("Failed to %s %s email addresses into MailChimp", ($type === 'unsubscribed') ? 'Unsubscribe' : 'Subscribe', count($chunk)));
            $import_failed = true;
        }
    }


    if($import_failed)
    {
        $logger->write(sprintf("Failed to %s all %s email addresses into MailChimp", ($type === 'unsubscribed') ? 'Unsubscribe' : 'Subscribe', count($emails)));
        BatchSubscriptionCronInitializer::fail_cron($cron_id);
    }
    else
    {
        $logger->write(sprintf("Successfully %s %s email addresses into MailChimp", ($type === 'unsubscribed') ? 'Unsbscribed' : 'Subscribed', count($emails)));
        BatchSubscriptionCronInitializer::succeed_cron($cron_id);
    }
}

