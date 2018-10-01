<?php

namespace CRMConnector\Workflows\Sub;

use DateTime;

/**
 * Class SetChapterLeadershipIsFuture
 * @package CRMConnector\Workflows\Sub
 */
class SetChapterLeadershipIsFuture implements SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {
        if(empty($args[0]['start_date'][0])) {
            return;
        }
        if(empty($args[1])) {
            return;
        }
        $chapter_leadership_id = $args[1];
        $format_in = 'Ymd'; // the format your value is saved in (set in the field options)
        $start_date = DateTime::createFromFormat($format_in, $args[0]['start_date'][0]);
        $end_date = DateTime::createFromFormat($format_in, $args[0]['end_date'][0]);
        if(new DateTime() < $start_date) {
            update_post_meta($chapter_leadership_id, 'is_future', true);
        }
        else {
            update_post_meta($chapter_leadership_id, 'is_future', false);
        }
    }
}