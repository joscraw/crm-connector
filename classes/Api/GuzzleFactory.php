<?php

namespace CRMConnector\Api;

use GuzzleHttp\Client;
use CRMConnector\Utils\CRMCFunctions;

/**
 * Class GuzzleFactory
 * @package CRMConnector\Api
 */
class GuzzleFactory
{
    /**
     * @var Client
     */
    private static $hubspot_client_instance;

    /**
     * @var Client
     */
    private static $mailchimp_client_instance;

    /**
     * @var Client
     */
    private static $mandrill_client_instance;

    /**
     * Hidden constructor, only callable from within this class
     */
    private function __construct() { }

    /**
     * Create a new instance if one doesn't exist
     * Or if it was already created, return it.
     *
     * @param array $config
     * @return mixed
     */
    public static function get_hubspot_instance($config = [])
    {
        // Check if an instance exists with this key already
        if(null === self::$hubspot_client_instance)
        {
            $a = array_merge(
                [
                    'base_uri' => 'https://api.hubapi.com',
                    'timeout'  => 2.0,
                ], $config
            );

            // instance doesn't exist yet, so create it
            self::$hubspot_client_instance = new Client($a);
        }

        // Return the correct instance of this class
        return self::$hubspot_client_instance;
    }

    /**
     * Create a new instance if one doesn't exist
     * Or if it was already created, return it.
     *
     * @param array $config
     * @return mixed
     */
    public static function get_mailchimp_instance($config = [])
    {
        // Check if an instance exists with this key already
        if(null === self::$mailchimp_client_instance)
        {
            $a = array_merge(
                [
                    'base_uri' => 'http://httpbin.org',
                    'timeout'  => 2.0,
                ], $config
            );

            // instance doesn't exist yet, so create it
            self::$mailchimp_client_instance = new Client($a);
        }

        // Return the correct instance of this class
        return self::$mailchimp_client_instance;
    }

    /**
     * Create a new instance if one doesn't exist
     * Or if it was already created, return it.
     *
     * @param array $config
     * @return mixed
     */
    public static function get_mandrill_instance($config = [])
    {
        // Check if an instance exists with this key already
        if(null === self::$mandrill_client_instance)
        {

            $a = array_merge(
                [
                    'base_uri' => 'http://httpbin.org',
                    'timeout'  => 2.0,
                ], $config
            );

            // instance doesn't exist yet, so create it
            self::$mandrill_client_instance =  new Client($a);

        }

        // Return the correct instance of this class
        return self::$mandrill_client_instance;
    }

    /**
     * Hidden magic clone method, make sure no instances of this class
     * can be cloned using the clone keyword
     */
    private function __clone() { }

}