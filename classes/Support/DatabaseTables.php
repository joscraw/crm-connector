<?php

namespace CRMConnector\Support;


/**
 * Class DatabaseTables
 * @package CRMConnector\Support
 */
class DatabaseTables
{

    /**
     * @var $this
     */
    protected static $instance;


    /**
     * @var array
     */
    protected $tables;

    protected function __construct()
    {
        if (isset(self::$instance))
            return;

        $this->setTables();

        self::$instance = $this;
    }

    /**
     * @return $this
     */
    public static function instance()
    {
        return self::$instance ? self::$instance : new self();
    }

    public static function reset()
    {
        self::$instance = null;
    }

    /**
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * @return $this
     */
    protected function setTables()
    {
        global $wpdb;

        $this->tables = $wpdb->get_col('SHOW TABLES');

        return $this;
    }

    /**
     * @param string $create_syntax
     * @return array
     */
    public static function create($create_syntax)
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        return dbDelta($create_syntax);
    }

    /**
     * @param $table
     * @return bool
     */
    public static function exists($table)
    {
        return in_array($table, self::instance()->getTables()) !== false;
    }



}