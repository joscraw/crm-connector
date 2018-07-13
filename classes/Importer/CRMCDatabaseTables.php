<?php


namespace CRMConnector\Importer;

use CRMConnector\Support\DatabaseTables;

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

        if(!DatabaseTables::exists($chapters_table))
        {
            $sql = "CREATE TABLE $chapters_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			chapter_name tinytext NOT NULL,
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
			FOREIGN KEY (group_id) REFERENCES $group_table(id),
			PRIMARY KEY  (id)
		) {$wpdb->get_charset_collate()}";

            DatabaseTables::create($sql);
        }


        $mapping = [
            'Student Prefix',
            'Student First Name',
            'Student Middle Name',
            'Student Last Name',
            'Student Suffix',
            'Campus Address One',
            'Campus Address Two',
            'Campus City',
            'Campus State',
            'Campus Zip Code',
            'Permanent Address One',
            'Permanent Address Two',
            'Permanent City',
            'Permanent State',
            'Permanent Zip Code',
            'Student Permanent Phone Number',
            'Student Email',
            'Student Mobile Phone',
            'GPA'
        ];

        update_option('student_import_file_mapping', $mapping);

    }



}