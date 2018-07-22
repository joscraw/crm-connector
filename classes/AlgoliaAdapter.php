<?php

namespace CRMConnector;

use Exception;

/**
 * Class AlgoliaAdapter
 * @author Josh Crawmer <joshcrawmer4@yahoo.com>
 */
class AlgoliaAdapter
{
    /**
     * Algolia predefined setting. Algolia does
     * not allow you to return more then this per page
     *
     * @var string
     */
    const MAX_HITS_PER_PAGE = 1000;

    private $client;

    private $index;

    private $applicationId;

    private $apiKey;

    /**
     * AlgoliaAdapter constructor.
     * @param null $applicationId
     * @param null $apiKey
     * @param null $indexName
     * @throws Exception
     */
    public function __construct($applicationId, $apiKey, $indexName)
    {

        $this->applicationId = $applicationId;
        $this->apiKey = $apiKey;
        try {
            $this->client = new \AlgoliaSearch\Client($applicationId, $apiKey);
        } catch(\Exception $exception) {
            throw $exception;
        }
        $this->index = $this->client->initIndex($indexName);
        /*$this->index->setSettings($this->getDefaultSettings());*/

    }

    /**
     * @param $objects
     * @param string $objectId
     * @param array $extraHeaders
     * @return mixed
     * @throws Exception
     */
    public function addObjects($objects, $objectId = 'objectID', $extraHeaders = array())
    {
        $response = null;

        try{
            $response = $this->index->addObjects($objects, $objectId, $extraHeaders);
        } catch(\Exception $exception) {
            throw $exception;
        }

        return $response;
    }

    /**
     * @param $objectIds
     * @return mixed|null
     * @throws Exception
     */
    public function deleteObjects($objectIds)
    {
        try{
            $this->index->deleteObjects($objectIds);
        } catch(\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * Handy function that lists out valid API keys associated with the account. We use this method to
     * verify the API keys being entered into the form.
     *
     * @return bool
     * @throws Exception
     */
    public function listKeys() {
        $response = array();

        try {
            $response = $this->client->listApiKeys();
        } catch(\Exception $exception) {
            throw $exception;
        }

        return $response;

    }

    /**
     * @param $query
     * @param null $searchParameters
     * @return mixed
     */
    public function search($query, $searchParameters = null)
    {
        return $this->index->search($query, $searchParameters);
    }


    /**
     * @param $search_query
     * @return mixed
     */
    public function browse($search_query)
    {
        return $this->index->browse($search_query);
    }

    /**
     * Default settings used for the index.
     * Take a look at AlgoliaSearch\Index::setSettings() for a list
     * of all the options you can add to this.
     *
     * @return array
     */
    private function getDefaultSettings() {
        return  [
            "searchableAttributes" => [
                "brand",
                "name",
                "categories",
                "unordered(description)"
            ],
        ];
    }


}