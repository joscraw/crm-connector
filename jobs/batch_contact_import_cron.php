<?php

/** Loads the WordPress Environment and Template */
use CRMConnector\Crons\Initializers\BatchContactImportCronInitializer;
use CRMConnector\Service\CustomPostType\Contact\ContactTransformer;
use CRMConnector\Utils\Logger;

require( dirname( __FILE__ ) . '/../../../../wp-load.php' );


global $wpdb;

$name = "Josh";

$results = $wpdb->get_results(sprintf("SELECT id, database_column_names, selected_file_columns, chapter_id, file_upload_path FROM %s%s WHERE status = '%s' AND failed_attempts <= 3 ORDER BY created_at DESC",
    $wpdb->prefix,
    'batch_import_contacts_cron',
    'IN_PROGRESS'
));


foreach($results as $result) {
    // Let's set the default import failed status to false.
    // and assume that it will succeed
    $import_failed = false;
    $cron_id = $result->id;
    $database_column_names= unserialize($result->database_column_names);
    $selected_file_columns = unserialize($result->selected_file_columns);
    $chapter_id = $result->chapter_id;
    $file_upload_path = $result->file_upload_path;
    $logger = new Logger();


    $logger->write(sprintf("Initializing Cron with id %s...", $cron_id));

    BatchContactImportCronInitializer::set_log_file($cron_id, $logger);
    BatchContactImportCronInitializer::progress_cron($cron_id);

    try
    {
        $logger->write(sprintf("Loading Spreadsheet..."));
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_upload_path);
    }
    catch(\Exception $exception)
    {
        $logger->write(sprintf("Failed Loading Spreadsheet - %s...", $exception->getMessage()));
        $logger->write(sprintf("Terminating Process..."));
        BatchContactImportCronInitializer::fail_cron($cron_id);
        continue;
    }

    $logger->write(sprintf("Converting Spreadsheet Into an Array..."));
    $rows = $spreadsheet->getActiveSheet()->toArray();

    $records = [];

    $logger->write(sprintf("Reducing Array of Data into %s Columns...", count($selected_file_columns)));
    array_shift($rows);
    foreach($rows as $row)
    {
        $record = [];
        $record['chapter_id'] = $chapter_id;
        foreach($selected_file_columns as $key => $selectedFileColumn)
        {
            $record[$database_column_names[$key]] = $row[$selectedFileColumn];
        }
        $records[] = $record;
    }

    $logger->write(sprintf("Transforming Data for Loading Into Database Table wp_posts..."));

    $transformed_records = [];
    foreach($records as $record)
    {
        $transformed_records[] = ContactTransformer::transform_record($record);
    }

    $logger->write(sprintf("Inserting %s Contact Records Into Database Table wp_posts...", count($transformed_records)));
    foreach($transformed_records as $transformed_record)
    {
        // Insert the default data that Wordpress requires for a post
        $post_id = wp_insert_post([
            "post_title"    =>  $transformed_record['full_name'],
            "post_type"     =>  'contacts',
            "post_status"   =>  'publish',
        ]);

        // Insert the Advanced Custom Fields data next
        foreach($transformed_record as $field => $value)
        {
            update_field($field, $value, $post_id);
        }

    }

    $logger->write(sprintf("Finished Inserting %s Contact Records into Database Table wp_posts...", count($transformed_records)));
    $logger->write(sprintf("Finished Cron %s.", $cron_id));

    BatchContactImportCronInitializer::succeed_cron($cron_id);

}
