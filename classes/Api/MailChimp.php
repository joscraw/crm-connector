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
use CRMConnector\Api\Models\MailChimp\Template;
use CRMConnector\Api\Models\MailChimpList;

/**
 * Class MailChimp
 * @package CRMConnector\Api
 */
class MailChimp
{

    /**
     * Max number of members that can be added per api call lists/{id}
     * @var string
     */
    const MAX_NUM_MEMBERS_ADDED_PER_CALL = 500;

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
     * @param $request
     * @return mixed
     */
    public function get_lists(Creds $creds, $request)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $query = [];

        if(isset($request['lists_offset']))
        {
            $query['offset'] = $request['lists_offset'];
        }

        if(isset($request['lists_count']))
        {
            $query['count'] = $request['lists_count'];
        }

        $response = $mailchimp_client->get(
            "{$this->api_version}/lists",
            [
                'query' => $query,
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

    /**
     * @param Creds $creds
     * @param $list_id
     * @return mixed
     */
    public function get_list(Creds $creds, $list_id)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->get(
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
     * @param $members
     * @param $list_id
     */
    public function batch_sub_unsub_members(Creds $creds, $members, $list_id)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->post(
            sprintf("{$this->api_version}/lists/%s", $list_id),
            [
                'json' => $members,
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
     * @param Template $template
     * @return
     */
    public function create_template(Creds $creds, Template $template)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->post(
            "{$this->api_version}/templates",
            [
                'json' => $template->toObject(),
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
     * @param $request
     * @return mixed
     */
    public function get_templates(Creds $creds, $request)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $query = [
            'type' => 'user'
        ];

        if(isset($request['templates_offset']))
        {
            $query['offset'] = $request['templates_offset'];
        }

        if(isset($request['templates_count']))
        {
            $query['count'] = $request['templates_count'];
        }

        $response = $mailchimp_client->get(
            "{$this->api_version}/templates",
            [
                // fetch only the templates created by a user
                'query' => $query,
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
     * @param $template_id
     * @return mixed
     */
    public function get_template(Creds $creds, $template_id)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->get(
            sprintf("{$this->api_version}/templates/%s", $template_id),
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
     * @param Template $template
     * @param $template_id
     * @return mixed
     */
    public function edit_template(Creds $creds, Template $template, $template_id)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->patch(
            sprintf("{$this->api_version}/templates/%s", $template_id),
            [
                'json' => $template->toObject(),
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
     * @param $template_id
     * @return mixed
     */
    public function delete_template(Creds $creds, $template_id)
    {
        $mailchimp_client = GuzzleFactory::get_mailchimp_instance([
            "base_uri" => "https://{$creds->data_center}.api.mailchimp.com"
        ]);

        $response = $mailchimp_client->delete(
            sprintf("{$this->api_version}/templates/%s", $template_id),
            [
                'auth' => $creds->toArray(),
                'headers' => [
                    'Accept'     => 'application/json',
                    'content-type' =>  'application/json'
                ]
            ]);

        return $response;
    }



}