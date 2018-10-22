<?php


namespace CRMConnector\Workflows\Pub;

use CRMConnector\Workflows\Sub\SubscriberInterface;

/**
 * Class AbstractPublisher
 * @package CRMConnector\Events
 */
class AbstractPublisher implements PublisherInterface
{
    /**
     * @var array
     */
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

    /**
     * @param SubscriberInterface $subscriber
     * @return $this
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        $this->observers[] = $subscriber;

        return $this;
    }

    /**
     * @return bool
     */
    public function has_errors()
    {
        foreach($this->observers as $observer) {
            if($observer->has_errors()) {
                return true;
            }
        }
        return false;
    }
}
