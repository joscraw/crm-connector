<?php
/**
 * Created by PhpStorm.
 * User: joshcrawmer
 * Date: 7/16/18
 * Time: 12:24 PM
 */

namespace CRMConnector\Api;

use CRMConnector\Api\GuzzleFactory;

/**
 * Class MailChimp
 * @package CRMConnector\Api
 */
class MailChimp
{

    /**
     * Private constructor so nobody else can instantiate it
     *
     */
    private function __construct()
    {
    }

    /**
     * string API Version
     * prepended to beginning of every URI
     */
    private $api_version = '3.0';

    /**
     * @var HubSpot
     */
    private static $inst;

    /**
     * @var string
     */
    private $api_key;

    /**
     * @var string
     */
    private $data_center;

    /**
     * @var string
     */
    private $username;

    /**
     * Call this method to get singleton
     *
     * @param string $api_key
     * @param null $username
     * @param null $data_center
     * @return HubSpot
     * @throws \Exception
     */
    public static function Instance($api_key = null, $username = null, $data_center = null)
    {
        if (self::$inst === null)
        {
            if($api_key === null || $data_center === null || $username === null)
                throw new \Exception("Api Key, Username, and Data Center must be passed in when instantiating for the first time");

            self::$inst = new self();
            self::$inst->api_key = $api_key;
            self::$inst->data_center = $data_center;
            self::$inst->username = $username;
        }
        return self::$inst;
    }

    /**
     * Hits the MailChimp API with a dummy call
     * to make sure the API key is valid
     *
     * @return mixed
     */
    public function ping()
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$this->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->get(
            "{$this->api_version}/ping",
            [
                'auth' => [$this->username, $this->api_key],
                'headers' => [
                    'Accept'     => 'application/json',
                ]
            ]);

        return $response;
    }


}