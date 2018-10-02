<?php
/**
 * Created by PhpStorm.
 * User: joshcrawmer
 * Date: 10/1/18
 * Time: 11:23 PM
 */

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetChapterLeadershipChapterOperationsManager
 * @package CRMConnector\Workflows\Sub
 */
class SetChapterLeadershipChapterOperationsManager implements SubscriberInterface
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
        $meta = get_post_meta($chapter_leadership_id, 'chapter_operations_email');
        if(empty($meta[0])) {
            return;
        }
        $query_args = [
            'post_type' => 'contacts',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'email',
                    'value' => $meta[0],
                    'compare' => '=',
                ),
            ),
        ];
        $posts = get_posts($query_args);
        $contact_id = !empty($posts[0]->ID) ? $posts[0]->ID : "";
        if(!$contact_id) {
            return;
        }
        update_post_meta($chapter_id, 'chapter_operations_manager', $contact_id);
    }
}