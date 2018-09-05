<?php

namespace CRMConnector\Importer;

use CRMConnector\Support\DatabaseTables;
use CRMConnector\Utils\CRMCFunctions;

/**
 * Class CRMCDatabaseTables
 * @package CRMConnector\Concerns
 */
class CRMCDatabaseTables
{
    /**
     * Create database tables only if they don't exist
     */
    public static function verify()
    {

        global $wpdb;

        $batch_list_export_crons_table = $wpdb->prefix.'batch_list_export_crons';
        $batch_import_contacts_crons_table = $wpdb->prefix.'batch_import_contacts_cron';

        if(!DatabaseTables::exists($batch_list_export_crons_table))
        {
            $sql = "CREATE TABLE $batch_list_export_crons_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			export_id mediumint(9),
			list_id tinytext,
			status tinytext,
			log_file tinytext,
			failed_attempts mediumint(9) NOT NULL DEFAULT 0,
			created_at DATETIME,
			completed_at DATETIME,
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

        if(!DatabaseTables::exists($batch_import_contacts_crons_table))
        {
            $sql = "CREATE TABLE $batch_import_contacts_crons_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			database_column_names longtext,
			selected_file_columns longtext,
			chapter_id mediumint(9),
			import_id mediumint(9), 
			status tinytext,
			file_upload_path tinytext,
			log_file tinytext,
			failed_attempts mediumint(9) NOT NULL DEFAULT 0,
			created_at DATETIME,
			completed_at DATETIME,
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

        $mapping = explode(",", file_get_contents(CRMCFunctions::plugin_dir() . '/config/default_column_names.csv'));
        update_option('student_import_file_mapping', $mapping);

    }

}