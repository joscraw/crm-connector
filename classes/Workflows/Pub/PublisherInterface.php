<?php

namespace CRMConnector\Workflows\Pub;

/**
 * Class PublisherInterface
 * @package CRMConnector\Events
 */
interface PublisherInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function notify($args);
}