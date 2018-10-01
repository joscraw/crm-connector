<?php
/**
 * Created by PhpStorm.
 * User: joshcrawmer
 * Date: 9/30/18
 * Time: 5:44 PM
 */

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetChapterLeadershipChapterOperationsEmail
 * @package CRMConnector\Workflows\Sub
 */
class SetChapterLeadershipChapterOperationsEmail implements SubscriberInterface
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
        $chapter_id = $args[0]['chapter'][0];
        $meta = get_post_meta($chapter_id, 'chapter_operations_email');
        if(empty($meta[0])) {
            return;
        }
        $me_manager_name = $meta[0];
        update_post_meta($chapter_leadership_id, 'chapter_operations_email', $me_manager_name);
    }
}