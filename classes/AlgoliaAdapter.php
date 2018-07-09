<?php

require __DIR__ . '/../vendor/autoload.php';

/**
 * Class AlgoliaAdapter
 * @author Josh Crawmer <joshcrawmer4@yahoo.com>
 */
class AlgoliaAdapter
{

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