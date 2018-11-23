<?php

namespace CRMConnector\Database;

use CRMConnector\Models\Contact;

/**
 * Class ContactSearch
 * @package CRMConnector\Database
 */
class ContactSearch
{
    use DatabaseQuery;

    /**
     * @var string
     */
    const POST_TYPE = 'contacts';

    /**
     * Returns all the data fields for the contact object
     *
     * @param $contact_id
     * @return array|null|\WP_Post
     */
    public function get_from_id($contact_id)
    {
        $data = [];
        foreach($this->get_field_names_for_post_type(self::POST_TYPE) as $field) {
            $data[$field] = get_post_meta($contact_id, $field, true);
        }
        return $data;
    }

    /**
     * @param $account_name
     * @return array
     */
    public function get_all_from_chapter($account_name) {

        // FETCH THE CHAPTER THE USER BELONGS TO
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'account_name',
                    'value' => $account_name,
                    'compare' => '=',
                ),
            ),
        ];
        $posts = get_posts($args);
        return $posts;
    }

    /**
     * @param $portal_user_id
     * @return array
     */
    public function get_from_portal_user_id($portal_user_id)
    {
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'portal_user',
                    'value' => $portal_user_id,
                    'compare' => '=',
                )
            ),
        ];
        $posts = get_posts($args);
        return $posts;
    }

    /**
     * @param $email
     * @return bool
     */
    public function get_from_email($email)
    {
        // FETCH THE CHAPTER THE USER BELONGS TO
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => 1,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'email',
                    'value' => $email,
                    'compare' => '=',
                ),
                array(
                    'key' => 'school_email',
                    'value' => $email,
                    'compare' => '=',
                ),
            ),
        ];
        $posts = get_posts($args);
        return $posts;
    }

    /**
     * @param $args
     * @return array|bool
     */
    public function get_post_from_args($args)
    {
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1,
            'meta_query' => array(
                $args
            ),
        ];

        $posts = get_posts($args);

        if(count($posts) === 0)
        {
            return [];
        }

        return $posts;
    }

    /**
     * This method will get all contacts with the data structured
     * as a normal mysql query.
     *
     * @return array|null|object
     */
    public function get_all_contacts_normalized()
    {
        global $wpdb;

        $r = $wpdb->get_results(
            "SELECT p.ID, 
               p.post_title, 
               pm1.meta_value as account_name,
               pm2.meta_value as email,
               pm3.meta_value as contact_type,
               pm4.meta_value as full_name,
               pm5.meta_value as permanent_state
              FROM  {$wpdb->posts} p 
              LEFT JOIN {$wpdb->postmeta} AS pm1 ON pm1.post_id  = p.ID
              LEFT JOIN {$wpdb->postmeta} AS pm2 ON pm2.post_id = p.ID
              LEFT JOIN {$wpdb->postmeta} AS pm3 ON pm3.post_id = p.ID
              LEFT JOIN {$wpdb->postmeta} AS pm4 ON pm4.post_id = p.ID
              LEFT JOIN {$wpdb->postmeta} AS pm5 ON pm5.post_id = p.ID
              WHERE
              pm1.meta_key = 'account_name' AND
              pm2.meta_key = 'email' AND
              pm3.meta_key = 'contact_type' AND
              pm4.meta_key = 'full_name' AND
              pm5.meta_key = 'permanent_state' AND
        
              p.post_type = 'contacts' AND
              p.post_status = 'publish'"
            );

        return $r;
    }

}