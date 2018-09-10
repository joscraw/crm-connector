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

}