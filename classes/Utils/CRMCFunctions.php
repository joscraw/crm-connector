<?php

namespace CRMConnector\Utils;

class CRMCFunctions
{
    /**
     * Return path to the root directory
     * of the CRMC plugin
     * @return string
     */
    public static function plugin_dir()
    {
        return WP_PLUGIN_DIR . '/crm-connector';
    }

    /**
     * @return string
     */
    public static function get_environment()
    {
        return preg_match('/\.test/', $_SERVER['HTTP_HOST']) ? 'local' : 'production';
    }

    /**
     * @return bool
     */
    public static function is_local()
    {
        return self::get_environment() === 'local';
    }

    /**
     * @return bool
     */
    public static function is_production()
    {
        return self::get_environment() === 'production';
    }


    public function deduplicate_array($array)
    {

    }


}