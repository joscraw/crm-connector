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

        $chapters_table = $wpdb->prefix.'chapters';
        $group_table = $wpdb->prefix.'groups';
        $properties_table = $wpdb->prefix.'properties';
        $imports_table = $wpdb->prefix.'imports';
        $exports_table = $wpdb->prefix.'exports';
        $batch_list_export_crons_table = $wpdb->prefix.'batch_list_export_crons';
        $templates_table = $wpdb->prefix.'mailchimp_templates';
        $batch_import_contacts_crons_table = $wpdb->prefix.'batch_import_contacts_cron';

        if(!DatabaseTables::exists($chapters_table))
        {
            $sql = "CREATE TABLE $chapters_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			chapter_name tinytext NOT NULL,
			created_at DATETIME,
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

        if(!DatabaseTables::exists($group_table))
        {
            $sql = "CREATE TABLE $group_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name longtext,
			displayName longtext,
			displayOrder longtext,
			hubspotDefined boolean,
			created_at DATETIME,
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

        if(!DatabaseTables::exists($properties_table))
        {
            $sql = "CREATE TABLE $properties_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			group_id mediumint(9),
			name longtext,
			label longtext,
			description longtext,
			groupName longtext,
			type tinytext,
			fieldType tinytext,
			created_at DATETIME,
			FOREIGN KEY (group_id) REFERENCES $group_table(id),
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

        if(!DatabaseTables::exists($imports_table))
        {
            $sql = "CREATE TABLE $imports_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			algolia_object_ids longtext,
			chapter_id mediumint(9),
			in_mandrill boolean,
			created_at DATETIME,
			FOREIGN KEY (chapter_id) REFERENCES $chapters_table(id),
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

        if(!DatabaseTables::exists($exports_table))
        {
            $sql = "CREATE TABLE $exports_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			algolia_object_ids longtext,
			in_mandrill boolean,
			created_at DATETIME,
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

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

        if(!DatabaseTables::exists($templates_table))
        {
            $sql = "CREATE TABLE $templates_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			template_id tinytext,
			html tinytext,
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