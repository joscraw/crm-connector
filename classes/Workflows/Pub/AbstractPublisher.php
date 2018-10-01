<?php


namespace CRMConnector\Workflows\Pub;

use CRMConnector\Workflows\Sub\SubscriberInterface;

/**
 * Class AbstractPublisher
 * @package CRMConnector\Events
 */
class AbstractPublisher implements PublisherInterface
{

    private $observers;

    /**
     * @param $args
     * @return mixed|void
     */
    public function notify($args)
    {
        foreach($this->observers as $observer)
        {
            $observer->update($args);
        }
    }

    public function addSubscriber(SubscriberInterface $subscriber)
    {
        $this->observers[] = $subscriber;

        return $this;
    }
}
