<?php

namespace CRMConnector;

class DatabaseMigrationInitializer
{
    private static $initiated = false;

    private static $table_prefix;

    private static $charset_collate;

    private static $db_version;

    public static function init() {
        global $wpdb;

        if ( ! self::$initiated ) {
            self::$table_prefix = $wpdb->prefix;
            self::$charset_collate = $wpdb->get_charset_collate();
            self::$db_version = "1.0.0";
            add_option('db_version', self::$db_version);
            self::createTables();
        }

    }


    public static function createTables() {
        self::createChaptersTable();
        self::createGroupsTable();
        self::createGroupsMetaTable();
        self::createJobsTable();
        self::createDefaultImportMapping();
    }


    public static function createChaptersTable()
    {
        $chapters_table = self::$table_prefix.'chapters';

        $c = self::$charset_collate;

        $sql = "CREATE TABLE $chapters_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			chapter_name tinytext NOT NULL,
			PRIMARY KEY  (id)
		) $c";

        dbDelta(array($sql));
    }

    public static function createGroupsTable()
    {
        $group_table = self::$table_prefix.'groups';

        $c = self::$charset_collate;

        $sql = "CREATE TABLE $group_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			group_name longtext,
			PRIMARY KEY  (id)
		) $c";

        dbDelta(array($sql));

    }

    public static function createGroupsMetaTable()
    {
        $groups_meta_table = self::$table_prefix.'properties';
        $join_table = self::$table_prefix.'groups';

        $c = self::$charset_collate;

        $sql = "CREATE TABLE $groups_meta_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			group_id mediumint(9),
			property_name tinytext,
			property_value longtext,
			FOREIGN KEY (group_id) REFERENCES $join_table(id),
			PRIMARY KEY  (id)
		) $c";

        dbDelta(array($sql));

    }


    public static function createJobsTable()
    {

        $query = "CREATE TABLE %scrons (
                  id mediumint(9) NOT NULL AUTO_INCREMENT,
                  message tinytext,
                  chapter_id mediumint(9),
                  file_path varchar(255),
                  PRIMARY KEY  (id)
              ) %s";

        $sql= sprintf($query, self::$table_prefix, self::$charset_collate);

        dbDelta(array($sql));

    }


    /**
     * The column name mapping used for storing student import file data.
     * Changing these values is not recommended because you will have records in Algolia
     * with different names from other records.
     */
    public static function createDefaultImportMapping()
    {

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