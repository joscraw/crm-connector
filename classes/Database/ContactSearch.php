<?php

namespace CRMConnector\Database;

/**
 * Class ContactSearch
 * @package CRMConnector\Database
 */
class ContactSearch
{
    /**
     * @return array
     */
    public function get_contact_fields()
    {
        $groups = acf_get_field_groups(array('post_type' => 'contacts'));

        $fields = [];
        foreach($groups as $group)
        {
            $group_fields = acf_get_fields($group['key']);
            foreach($group_fields as $group_field)
            {
                if(empty($group_field['name']) || empty($group_field['label']))
                {
                    continue;
                }

                $fields[$group_field['name']] = null;
            }
        }

        return $fields;
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
                array(
                    'key' => 'email',
                    'value' => $email,
                    'compare' => '=',
                ),
            ),
        ];

        $posts = get_posts($args);

        if(count($posts) === 0)
        {
            return false;
        }

        return $posts;
    }

    /**
     * @param $args
     * @return array|bool
     */
    public function get_from_args($args)
    {
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => 1,
            'meta_query' => array(
                $args
            ),
        ];

        $posts = get_posts($args);

        if(count($posts) === 0)
        {
            return false;
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