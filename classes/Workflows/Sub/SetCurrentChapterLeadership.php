<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetCurrentChapterLeadership
 * @package CRMConnector\Workflows\Sub
 */
class SetCurrentChapterLeadership implements SubscriberInterface
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
        if(empty($args[0]['chapter'][0])) {
            return;
        }
        if(empty($args[0]['type'][0])) {
            return;
        }
        if(empty($args[0]['position'][0])) {
            return;
        }
        $chapter_id = $args[0]['chapter'][0];
        $query_args = [
            'post_type' => 'chapter_leadership',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'chapter',
                    'value' => $chapter_id,
                    'compare' => '=',
                ),
            ),
        ];
        $posts = get_posts($query_args);
        if(empty($posts)) {
            return;
        }
        $chapter_leadership = '';
        foreach($posts as $post) {
            $contact_id = get_post_meta($post->ID, 'contact', true);
            $chapter_leadership .= sprintf("Full Name: %s Email: %s Type: %s Position: %s",
                get_post_meta($contact_id, 'full_name', true),
                get_post_meta($contact_id, 'email', true),
                get_post_meta($post->ID, 'type', true),
                get_post_meta($post->ID, 'position', true)
            );
            $chapter_leadership .= PHP_EOL;
        }

        update_post_meta($chapter_id, 'current_chapter_leadership_', $chapter_leadership);
    }
}