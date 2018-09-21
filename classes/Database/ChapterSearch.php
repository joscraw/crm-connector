<?php

namespace CRMConnector\Database;


use CRMConnector\Models\Contact;
use WP_Query;

/**
 * Class ChapterSearch
 * @package CRMConnector\Database
 */
class ChapterSearch
{

    /**
     * @param Contact $contact
     * @return bool|WP_Query
     */
    public function get_chapter_from_contact(Contact $contact)
    {

        // FETCH THE CHAPTER THE USER BELONGS TO
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'email',
                    'value' => $contact->email,
                    'compare' => '=',
                ),
            ),
        ];

        $query = new WP_Query($args);

        if(!$query->have_posts())
        {
            return false;
        }

        while ($query->have_posts()) {
            $query->the_post();
            $chapter_id = get_post_meta(get_the_ID(), 'account_name', true);
            break;
        }

        if (!isset($chapter_id)) {
            return false;
        }

        $query = new WP_Query();
        $query->query($args);

        return $query;

    }

    /**
     * @return array|bool
     */
    public function get_all()
    {
        $chapters = get_posts([
            'post_type' => 'chapters',
            'posts_per_page' => -1,
        ]);

        if(!$chapters)
        {
            return false;
        }

        return $chapters;

    }

    /**
     * This method will get all chapters with the data structured
     * as a normal mysql query.
     *
     * @return array|null|object
     */
    public function get_all_chapters_normalized()
    {
        global $wpdb;

        $r = $wpdb->get_results(
            "SELECT p.ID, 
               p.post_title, 
               pm1.meta_value as account_name
              FROM  {$wpdb->posts} p 
              INNER JOIN {$wpdb->postmeta} AS pm1  ON pm1.post_id = p.ID
              WHERE
              pm1.meta_key = 'account_name' AND        
              p.post_type = 'chapters' AND
              p.post_status = 'publish'"
        );

        return $r;
    }

}