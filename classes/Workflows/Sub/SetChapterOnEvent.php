<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetChapterOnEvent
 * @package CRMConnector\Workflows\Sub
 */
class SetChapterOnEvent implements SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {

        global $CRMConnectorPlugin;

        if(empty($args[1])) {
            return;
        }

        $event = $args[1];

        if(empty($CRMConnectorPlugin->data['chapter_id'])) {
            set_transient('errors', ['Woah hold up! Your user account must have a Contact Record associated with it that has been assigned to a given Chapter for you to create events that are displayed on the front end!']);
            return;
        }

        update_post_meta($event, 'chapter', $CRMConnectorPlugin->data['chapter']);

    }
}