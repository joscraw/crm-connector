<?php

/** Loads the WordPress Environment and Template */
use CRMConnector\Api\MailChimp;
use CRMConnector\Api\Models\MailChimp\Creds;
use CRMConnector\Api\Models\MailChimp\EmailCollection;
use CRMConnector\Crons\Initializers\BatchContactImportCronInitializer;
use CRMConnector\Crons\Initializers\BatchListExportCronInitializer;
use CRMConnector\Models\CRMC_List;
use CRMConnector\Utils\Logger;

require( dirname( __FILE__ ) . '/../../../../wp-load.php' );

global $wpdb;

$results = $wpdb->get_results(sprintf("SELECT id, export_id, list_id FROM %s%s WHERE status = '%s' AND failed_attempts <= 3 ORDER BY created_at DESC",
    $wpdb->prefix,
    'batch_list_export_crons',
    'IN_QUEUE'
));

foreach($results as $result) {
    // Let's set the default import failed status to false.
    // and assume that it will succeed
    $import_failed = false;
    $cron_id = $result->id;
    $export_id = $result->export_id;
    $list_id = $result->list_id;
    $logger = new Logger();
    $email_collection = new EmailCollection();
    $creds = new Creds;
    $creds->username = get_field('username', 'option');
    $creds->api_key = get_field('api_key', 'option');
    $mailchimp_api = MailChimp::Instance();

    $logger->write(sprintf("Initializing Cron with id %s...", $cron_id));

    BatchListExportCronInitializer::set_log_file($cron_id, $export_id, $logger);
    BatchListExportCronInitializer::progress_cron($cron_id, $export_id);

    $list = get_post_meta($list_id);

    $list_model = new CRMC_List();
    $list_model->from_array($list);


    try
    {
        $response = $mailchimp_api->get_list($creds, $list_model->getMailchimpListId());
    }
    catch(\Exception $exception)
    {
        $logger->write(sprintf("Fatal Error: MailChimp List with ID %s Not Found Inside MailChimp Account... Exception: %s",
            $list_model->getMailchimpListId(),
            $exception->getMessage()
        ));

        BatchListExportCronInitializer::fail_cron($cron_id, $export_id);

        continue;
    }

    if($list_model->isExportFromChapter())
    {
        $logger->write(sprintf("Initializing Export for %s Chapter(s)...", count($list_model->getChapters())));

        foreach($list_model->getChapters() as $chapter)
        {
            $logger->write(sprintf("Initializing Export From Chapter with ID %s...", $chapter));

            $args = [
                'post_type' => 'contacts',
                'posts_per_page' => -1 ,
                'meta_query' => [
                    [
                        'key' => 'account_name',
                        'value' => $chapter
                    ]
                ]
            ];

            $logger->write(sprintf("Querying Database for contacts with specified args: %s...", json_encode($args)));

            $query = new WP_Query($args);

            if ( $query->have_posts() ) {

                $logger->write(sprintf("Found Contacts. Total Contacts Found: %s...", $query->post_count));

                while ( $query->have_posts() ) {
                    $query->the_post();
                    $email = get_post_meta( get_the_ID(), 'email', true);
                    $email_collection->addEmail($email);
                }
            }
            wp_reset_query();
        }
    }
    else
    {
        $logger->write(sprintf("Initializing Custom Export..."));

        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1 ,
            'meta_query' => [
                'relation' => 'AND',
                $list_model->getQueryArgs()[0]
                ]
        ];

        $logger->write(sprintf("Querying Database for contacts with specified args: %s...", json_encode($args)));

        $query = new WP_Query($args);

        if ( $query->have_posts() ) {

            $logger->write(sprintf("Found Contacts. Total Contacts Found: %s...", $query->post_count));

            while ( $query->have_posts() ) {
                $query->the_post();
                $email = get_post_meta( get_the_ID(), 'email', true);
                $email_collection->addEmail($email);
            }
        }
        wp_reset_query();
    }


    $logger->write(sprintf("De-duplicating Contacts..."));
    $logger->write(sprintf("Contact Count After De-duplicating: %s...", count($email_collection->de_duplicate())));

    $chunks = array_chunk($email_collection->getEmails(), 250);
    $successfully_subscribed_number = 0;
    foreach($chunks as $chunk)
    {
        // trying to not overwhelm the mailchimp api
        sleep(10);

        try
        {
            $logger->write(sprintf("Subscribing %s email addresses to MailChimp", count($chunk)));
            $memberCollection = new \stdClass();
            $memberCollection->update_existing = true;
            $memberCollection->members = $chunk;
            $mailchimp_api->batch_sub_unsub_members($creds, $memberCollection, $list_model->getMailchimpListId());
            $successfully_subscribed_number += count($chunk);
        }
        catch(\Exception $exception)
        {
            $logger->write(sprintf("Failed Subscribing %s email addresses to MailChimp. Terminating Import...", count($chunk)));
            $logger->write(sprintf("Exception: %s", $exception->getMessage()));
            $import_failed = true;
            $logger->write(sprintf("Import Terminated..."));
            BatchListExportCronInitializer::fail_cron($cron_id, $export_id);
            break;
        }
    }

    if(!$import_failed)
    {
        $logger->write(sprintf("Successfully Subscribed %s email addresses to MailChimp...", $successfully_subscribed_number));
        $logger->write(sprintf("Import Finished..."));
        BatchListExportCronInitializer::succeed_cron($cron_id, $export_id);
    }

}