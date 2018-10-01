<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetChapterLeadershipName
 * @package CRMConnector\Events\Pub
 */
class SetChapterLeadershipName implements SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {
        if(empty($args[0]['contact'][0])) {
            return;
        }
        if(empty($args[1])) {
            return;
        }
        $chapter_leadership_id = $args[1];
        $contact_id = $args[0]['contact'][0];
        $meta = get_post_meta($contact_id, 'full_name');
        if(empty($meta[0])) {
            return;
        }
        $pieces = explode(" ", $meta[0]);
        $first_name = !empty($pieces[0]) ? $pieces[0] : "";
        $last_name = !empty($pieces[1]) ? $pieces[1] : "";
        $full_name = "$first_name $last_name";
        wp_update_post(array(
            'ID'           => $chapter_leadership_id,
            'post_title'   => $full_name,
        ));
        update_post_meta($chapter_leadership_id, 'name', "$first_name $last_name");
    }
}