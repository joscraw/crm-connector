<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetChapterLeadershipTitle
 * @package CRMConnector\Workflows\Sub
 */
class SetChapterLeadershipTitle implements SubscriberInterface
{

    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {
        if(empty($args[0]['chapter'][0])) {
            return;
        }
        if(empty($args[1])) {
            return;
        }
        $chapter_leadership_id = $args[1];
        $meta = get_post_meta($chapter_leadership_id, 'position');
        if(empty($meta[0])) {
            return;
        }
        $position = $meta[0];
        update_post_meta($chapter_leadership_id, 'title', $position);
    }
}