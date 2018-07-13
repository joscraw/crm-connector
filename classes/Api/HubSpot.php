<?php

namespace CRMConnector\Api;

use CRMConnector\Api\GuzzleFactory;

/**
 * Class HubSpot
 */
class HubSpot
{

    /**
     * Private constructor so nobody else can instantiate it
     *
     */
    private function __construct()
    {
    }

    /**
     * @var HubSpot
     */
    private static $inst;

    /**
     * @var string
     */
    private $api_key;

    /**
     * Call this method to get singleton
     *
     * @param string $api_key
     * @return HubSpot
     * @throws \Exception
     */
    public static function Instance($api_key = null)
    {
        if (self::$inst === null)
        {
            if($api_key === null)
                throw new \Exception("Api Key must be passed in when instantiating for the first time");

            self::$inst = new self();
            self::$inst->api_key = $api_key;
        }
        return self::$inst;
    }

    /**
     * Hits the HubSpot API with a dummy call
     * to make sure the API key is valid
     *
     * @return mixed
     */
    public function ping()
    {
        $hubspot_client = GuzzleFactory::get_hubspot_instance();

        $response = $hubspot_client->get(
            'contacts/v1/lists/all/contacts/all',
        [
            'query' => [
                'hapikey'=> $this->api_key
            ]
        ]);

        return $response;
    }

    /**
     * @param array $payload data to be passed up in the request
     * @return mixed
     */
    public function createCompany($payload)
    {
        $hubspot_client = GuzzleFactory::get_hubspot_instance();

        $response = $hubspot_client->post(
            'companies/v2/companies',
            [
                'json' => $payload,
                'query' => [
                    'hapikey'=> $this->api_key
                ]
            ]);

        return $response;
    }

    /**
     * @param $payload
     * @return mixed
     */
    public function createCompanyGroup($payload)
    {
        $hubspot_client = GuzzleFactory::get_hubspot_instance();

        $response = $hubspot_client->post(
            'properties/v1/companies/groups',
            [
                'json' => $payload,
                'query' => [
                    'hapikey'=> $this->api_key
                ]
            ]);

        return $response;
    }

    /**
     * @return mixed
     */
    public function getCompanyGroups()
    {
        $hubspot_client = GuzzleFactory::get_hubspot_instance();

        $response = $hubspot_client->get(
            'properties/v1/companies/groups',
            [
                'query' => [
                    'hapikey'=> $this->api_key
                ]
            ]);

        return $response;
    }

    /**
     * @param $payload
     * @param $group_name
     * @return mixed
     */
    public function updateCompanyGroup($payload, $group_name)
    {
        $hubspot_client = GuzzleFactory::get_hubspot_instance();

        $response = $hubspot_client->put(
            sprintf('properties/v1/companies/groups/named/%s', $group_name),
            [
                'json' => $payload,
                'query' => [
                    'hapikey'=> $this->api_key
                ]
            ]);

        return $response;
    }







}