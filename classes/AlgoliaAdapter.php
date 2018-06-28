<?php

require __DIR__ . '/../vendor/autoload.php';

/**
 * Class AlgoliaAdapter
 * @author Josh Crawmer <joshcrawmer4@yahoo.com>
 */
class AlgoliaAdapter
{

    private $client;


    /**
     * AlgoliaAdapter constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if(!get_option('crmc_algolia_application_id') || !get_option('crmc_algolia_api_key')) {
            throw new \Exception("Algolia application id and api key must be set to create an instance of this.");
        }

        $this->client = new \AlgoliaSearch\Client(get_option('crmc_algolia_application_id'), get_option('crmc_algolia_api_key'));
    }






}