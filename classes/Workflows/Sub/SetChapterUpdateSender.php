<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetChapterUpdateSender
 * @package CRMConnector\Workflows\Sub
 */
class SetChapterUpdateSender implements SubscriberInterface
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
            set_transient('errors', ['You must be added as a contact in the system before you can send a chapter update.']);
            return;
        }
        $contactId = $posts[0]->ID;
        update_post_meta($chapter_update_id, 'sender', $contactId);
    }
}