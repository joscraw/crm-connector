<?php

/** Loads the WordPress Environment and Template */
use CRMConnector\Crons\Initializers\BatchContactImportCronInitializer;
use CRMConnector\Service\CustomPostType\Contact\ContactTransformer;
use CRMConnector\Utils\Logger;

require( dirname( __FILE__ ) . '/../../../../wp-load.php' );


global $wpdb;


$results = $wpdb->get_results(sprintf("SELECT id, export_id, list_id, FROM %s%s WHERE status = '%s' AND failed_attempts <= 3 ORDER BY created_at DESC",
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

    $logger->write(sprintf("Initializing Cron with id %s...", $cron_id));

}