<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Interface SubscriberInterface
 * @package CRMConnector\Events
 */
interface SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args);
}