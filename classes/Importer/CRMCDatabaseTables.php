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
			created_at DATETIME,
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }

        $mapping = explode(",", file_get_contents(CRMCFunctions::plugin_dir() . '/config/default_column_names.csv'));

        update_option('student_import_file_mapping', $mapping);

    }

}