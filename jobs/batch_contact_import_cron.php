<?php

/** Loads the WordPress Environment and Template */
use CRMConnector\Crons\Initializers\BatchContactImportCronInitializer;
use CRMConnector\Service\CustomPostType\Contact\ContactTransformer;
use CRMConnector\Utils\Logger;

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
    $database_column_names= unserialize($result->database_column_names);
    $selected_file_columns = unserialize($result->selected_file_columns);
    $chapter_id = $result->chapter_id;
    $file_upload_path = $result->file_upload_path;
    $logger = new Logger();


    $logger->write(sprintf("Initializing Cron with id %s...", $cron_id));

    BatchContactImportCronInitializer::set_log_file($cron_id, $import_id, $logger);
    BatchContactImportCronInitializer::progress_cron($cron_id, $import_id);

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

    $logger->write(sprintf("Updating Contact Records In Database Table wp_posts..."));
    $contacts_updated = 0;
    $duplicate_contacts_found = 0;
    $pre_existing_emails = [];

        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1 ,
            'meta_query' => [
                [
                    'meta_key' => 'chapter',
                    'meta_value' => $chapter_id
                ]
            ]
        ];

        $query = new WP_Query($args);

        if ( $query->have_posts() )
        {
            while ( $query->have_posts() ) {
                $query->the_post();
                $pre_existing_emails[get_the_ID()] = get_post_meta( get_the_ID(), 'email', true);
            }
        }
        wp_reset_query();

    foreach($transformed_records as $key => $transformed_record)
    {
        if(in_array($transformed_record['email'], $pre_existing_emails))
        {
            $post_id = array_search($transformed_record['email'], $pre_existing_emails);

            $fields_updated_for_contact = 0;
            foreach($transformed_record as $field => $value)
            {
                if(update_field($field, $value, $post_id))
                {
                    $fields_updated_for_contact++;
                    $logger->write(sprintf("Updating Field: %s With Value: %s For Post: %s", $field, $value, $post_id));
                }
            }

            if($fields_updated_for_contact > 0)
            {
                $contacts_updated++;
            }

            $duplicate_contacts_found++;

            unset($transformed_records[$key]);
        }
    }

    $logger->write(sprintf("Inserting %s Contact Records Into Database Table wp_posts...", count($transformed_records)));
    $i = 0;
    foreach($transformed_records as $transformed_record)
    {
        // Insert the default data that Wordpress requires for a post
        $result = wp_insert_post([
            "post_title"    =>  $transformed_record['full_name'],
            "post_type"     =>  'contacts',
            "post_status"   =>  'publish',
        ], true);

        if($result instanceof WP_Error)
        {
            $error_messages = implode(',', $result->get_error_messages());
            $logger->write(sprintf("Error Creating Contact %s. WP Error Messages: %s", $transformed_record['full_name'], $error_messages));
            continue;
        }

        $post_id = $result;

        // Insert the Advanced Custom Fields data next
        foreach($transformed_record as $field => $value)
        {
            if(!update_field($field, $value, $post_id))
            {
                $logger->write(sprintf("Error Inserting Field: %s With Value: %s For Post: %s", $field, $value, $post_id));
            }
        }

        if(++$i % 20 === 0)
            $logger->write(sprintf("Inserted %s Contact Records Into Database Table wp_posts...", $i));

    }

    $logger->write(sprintf("Finished Updating %s Contact Records in Database Table wp_posts...", $contacts_updated));
    $logger->write(sprintf("Total Duplicate Contacts Found: %s...", $duplicate_contacts_found));
    $logger->write(sprintf("Finished Inserting %s Contact Records into Database Table wp_posts...", count($transformed_records)));
    $logger->write(sprintf("Finished Cron %s.", $cron_id));

    BatchContactImportCronInitializer::succeed_cron($cron_id, $import_id);

}
