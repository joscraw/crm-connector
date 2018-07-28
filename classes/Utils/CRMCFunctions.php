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
     * URL to the root directory of the CRMC plugin
     *
     * @return string
     */
    public static function plugin_url()
    {
        return plugins_url() . '/crm-connector';
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

    /**
     * Used to get the post id inside an ajax call
     * @return int
     */
    public static function get_post_id()
    {
        $url     = wp_get_referer();
        return url_to_postid( $url );
    }

    /**
     * @param $format
     * @return string
     * @internal param string $format_in Example: 'Ymd'
     * @internal param string $format_out Example: 'd-m-Y'
     */
    public static function current_date($format)
    {
        return (new \DateTime())->format($format);
    }

}