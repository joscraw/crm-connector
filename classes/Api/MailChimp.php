<?php
/**
 * Created by PhpStorm.
 * User: joshcrawmer
 * Date: 7/16/18
 * Time: 12:24 PM
 */

namespace CRMConnector\Api;

use CRMConnector\Api\GuzzleFactory;
use CRMConnector\Api\Models\MailChimp\Creds;
use CRMConnector\Api\Models\MailChimpList;

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
     * @return HubSpot
     */
    public static function Instance()
    {
        if (self::$inst === null)
        {
            self::$inst = new self();
        }
        return self::$inst;
    }

    /**
     * Hits the MailChimp API with a dummy call
     * to make sure the API key is valid
     *
     * @param Creds $creds
     * @return mixed
     */
    public function ping(Creds $creds)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->get(
            "{$this->api_version}/ping",
            [
                'auth' => $creds->toArray(),
                'headers' => [
                    'Accept'     => 'application/json',
                ]
            ]);

        return $response;
    }

    /**
     * @param Creds $creds
     * @param MailChimpList $mailchimp_list
     * @see https://github.com/drewm/mailchimp-api/issues/196
     * @return mixed
     */
    public function create_list(Creds $creds, MailChimpList $mailchimp_list)
    {

        $payload = $mailchimp_list->toArray();

        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->post(
            "{$this->api_version}/lists",
            [   'json' => $payload,
                'auth' => $creds->toArray(),
                'headers' => [
                    'Accept'     => 'application/json',
                    'content-type' =>  'application/json'
                ]
            ]);

        return $response;
    }

    /**
     * @param Creds $creds
     * @return mixed
     */
    public function get_lists(Creds $creds)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->get(
            "{$this->api_version}/lists",
            [
                'auth' => $creds->toArray(),
                'headers' => [
                    'Accept'     => 'application/json',
                    'content-type' =>  'application/json'
                ]
            ]);

        return $response;
    }

    /**
     * @param Creds $creds
     * @param $list_id
     * @return mixed
     */
    public function remove_list(Creds $creds, $list_id)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->delete(
            sprintf("{$this->api_version}/lists/%s", $list_id),
            [
                'auth' => $creds->toArray(),
                'headers' => [
                    'Accept'     => 'application/json',
                    'content-type' =>  'application/json'
                ]
            ]);

        return $response;
    }

    /**
     * @param Creds $creds
     * @param MailChimpList $mailchimp_list
     * @param $list_id
     * @return mixed
     */
    public function edit_list(Creds $creds, MailChimpList $mailchimp_list, $list_id)
    {
        $payload = $mailchimp_list->toArray();

        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->patch(
            sprintf("{$this->api_version}/lists/%s", $list_id),
            [
                'json' => $payload,
                'auth' => $creds->toArray(),
                'headers' => [
                    'Accept'     => 'application/json',
                    'content-type' =>  'application/json'
                ]
            ]);

        return $response;
    }


}