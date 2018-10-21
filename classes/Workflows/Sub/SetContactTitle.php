<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetContactTitle
 * @package CRMConnector\Workflows\Sub
 */
class SetContactTitle implements SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {
        if(empty($args[0]['full_name'][0])) {
            return;
        }
        if(empty($args[1])) {
            return;
        }
        $contact_id = $args[1];
        $full_name = $args[0]['full_name'][0];
        wp_update_post(array(
            'ID'           => $contact_id,
            'post_title'   => $full_name,
        ));
    }
}