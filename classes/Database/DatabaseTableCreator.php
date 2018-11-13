<?php

namespace CRMConnector\Database;

use CRMConnector\Support\DatabaseTables;
use CRMConnector\Utils\CRMCFunctions;

/**
 * Class DatabaseTableCreator
 * @package CRMConnector\Concerns
 */
class DatabaseTableCreator
{
    /**
     * Create database tables only if they don't exist
     */
    public static function create()
    {
        global $wpdb;

        $batch_list_export_crons_table = $wpdb->prefix.'batch_list_export_crons';
        $batch_import_contacts_crons_table = $wpdb->prefix.'batch_import_contacts_cron';
        $chapter_update_cron_table = $wpdb->prefix.'chapter_update_cron';
        $generate_report_cron_table = $wpdb->prefix.'generate_report_cron';

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

        if(!DatabaseTables::exists($generate_report_cron_table))
        {
            $sql = "CREATE TABLE $generate_report_cron_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			report_type tinytext, 
			report_id mediumint(9),
			status tinytext,
			log_file tinytext,
			failed_attempts mediumint(9) NOT NULL DEFAULT 0,
			created_at DATETIME,
			completed_at DATETIME,
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

        if(!DatabaseTables::exists($chapter_update_cron_table))
        {
            $sql = "CREATE TABLE $chapter_update_cron_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			chapter_update_id mediumint(9),
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
    }

}