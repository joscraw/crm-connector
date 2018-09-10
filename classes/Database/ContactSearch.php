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

}