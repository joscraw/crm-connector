<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetChapterUpdateChapter
 * @package CRMConnector\Workflows\Sub
 */
class SetChapterUpdateChapter implements SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {
        $user = wp_get_current_user();
        if ( in_array( 'administrator', (array) $user->roles ))
        {
            return;
        }
        $chapter_update_id = $args[1];
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'portal_user',
                    'value' => $user->ID,
                    'compare' => '=',
                ),
            ),
        ];
        $posts = get_posts($args);
        if(count($posts) === 0)
        {
            set_transient('errors', ['You must be added as a contact in the system.']);
            return;
        }
        $contactId = $posts[0]->ID;
        $account_name = get_post_meta($contactId, 'account_name', true);
        update_post_meta($chapter_update_id, 'chapter', $account_name);
    }
}