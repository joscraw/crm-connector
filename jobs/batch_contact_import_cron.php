<?php

/** Loads the WordPress Environment and Template */
use CRMConnector\ContactMapper;
use CRMConnector\Crons\Initializers\BatchContactImportCronInitializer;
use CRMConnector\ContactTransformer;
use CRMConnector\Database\ContactSearch;
use CRMConnector\ExcelMapper;
use CRMConnector\Models\Collection;
use CRMConnector\StudentImportMapper;
use CRMConnector\Utils\Logger;
use CRMConnector\DeDuplicate;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;

require( dirname( __FILE__ ) . '/../../../../wp-load.php' );

global $wpdb;

$results = $wpdb->get_results(sprintf("SELECT id, import_id, database_column_names, selected_file_columns, chapter_id, file_upload_path FROM %s%s WHERE status = '%s' AND failed_attempts <= 3 ORDER BY created_at DESC",
    $wpdb->prefix,
    'batch_import_contacts_cron',
    'IN_QUEUE'
));

foreach($results as $result) {
    // Let's set the default import failed status to false.
    // and assume that it will succeed
    $import_failed = false;
    $cron_id = $result->id;
    $import_id = $result->import_id;
    $database_column_names = unserialize($result->database_column_names);
    $selected_file_columns = unserialize($result->selected_file_columns);
    $chapter_id = $result->chapter_id;
    $file_upload_path = $result->file_upload_path;
    $logger = new Logger();


    $logger->write(sprintf("Initializing Cron with id %s...", $cron_id));

    BatchContactImportCronInitializer::set_log_file($cron_id, $import_id, $logger);
    /*BatchContactImportCronInitializer::progress_cron($cron_id, $import_id);*/

    try
    {
        $logger->write(sprintf("Loading Spreadsheet..."));
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_upload_path);
    }
    catch(\Exception $exception)
    {
        $logger->write(sprintf("Failed Loading Spreadsheet - %s...", $exception->getMessage()));
        $logger->write(sprintf("Terminating Process..."));
        BatchContactImportCronInitializer::fail_cron($cron_id, $import_id);
        continue;
    }

    $logger->write(sprintf("Converting Spreadsheet Into an Array..."));

    $records = [];

    $logger->write(sprintf("Reducing Array of Data into %s Columns...", count($selected_file_columns)));

    $contact_collection = [];

    $mapper = new StudentImportMapper(
        $spreadsheet->getActiveSheet()->getRowIterator(),
        $database_column_names,
        $selected_file_columns
        );

    $collection = new Collection();
    foreach($mapper as $index => $contact)
    {
        $contact->account_name  = isset($result->chapter_id) ? trim($result->chapter_id) : '';

        $collection->addItem($contact);
    }

    $potential_duplicates = new Collection();
    foreach($collection as $key => $contact)
    {
        if($contact->isDuplicate())
        {
            $collection->deleteItem($key);

            $potential_duplicates->addItem($contact);
        }
    }

    $logger->write(sprintf("Inserting %s Contact Records Into Database Table wp_posts...", $collection->length()));
    $i = 0;

    foreach($collection as $contact)
    {
        // Insert the default data that Wordpress requires for a post
        $result = wp_insert_post([
            "post_title"    =>  $contact->full_name,
            "post_type"     =>  'contacts',
            "post_status"   =>  'publish',
        ], true);

        if($result instanceof WP_Error)
        {
            $error_messages = implode(',', $result->get_error_messages());
            $logger->write(sprintf("Error Creating Contact %s. WP Error Messages: %s", $contact->full_name, $error_messages));
            continue;
        }

        $post_id = $result;

        foreach($contact as $field => $value)
        {
            if(!update_field($field, $value, $post_id))
            {
                $logger->write(sprintf("Error Inserting Field: %s With Value: %s For Post: %s", $field, $value, $post_id));
            }
        }

    }

    $logger->write(sprintf("Finished Cron %s.", $cron_id));

    BatchContactImportCronInitializer::succeed_cron($cron_id, $import_id);

}
