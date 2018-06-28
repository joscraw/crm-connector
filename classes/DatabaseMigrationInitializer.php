<?php



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


}