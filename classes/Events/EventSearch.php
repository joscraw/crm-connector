<?php

namespace CRMConnector\Events;

use TribeEvents;
use WP_Query;

/**
 * Class EventSearch
 * @package CRMConnector\Events
 */
class EventSearch
{
    /**
     * @return bool|WP_Query
     */
    public function getEventsForLoggedInUserByRole()
    {
        global $crmConnectorFrontend;

        $current_user_id = $crmConnectorFrontend->data['current_user_id'];
        $current_user_roles = $crmConnectorFrontend->data['current_user_roles'];

        if($current_user_id === 0 || empty($current_user_roles))
        {
            return false;
        }

        // GET THE EVENTS FOR A STUDENT IF THE USER HAS THAT ROLE
        if (in_array( 'student', $current_user_roles ) ||
            in_array( 'chapter_adviser', $current_user_roles ) ||
            in_array( 'chapter_officer', $current_user_roles ))
        {
            // FETCH THE CHAPTER THE USER BELONGS TO
            $args = [
                'post_type' => 'contacts',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'portal_user',
                        'value' => $current_user_id,
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

            wp_reset_postdata();

            if (!isset($chapter_id)) {
                return false;
            }

            $args = array(
                'post_status' => 'publish',
                'post_type' => array(TribeEvents::POSTTYPE),
                'posts_per_page' => 10,
                'meta_query' => array(
                    array(
                        'key' => 'chapter',
                        'value' => $chapter_id,
                        'compare' => '=',
                    ),
                )
            );

            $query = new WP_Query();
            $query->query($args);

            return $query;
        }

        // GET THE EVENTS FOR AN ADMINISTRATOR OR HONOR SOCIETY SUPER ADMIN IF THE USER HAS THAT ROLE
        if(in_array( 'administrator', $current_user_roles ) || in_array( 'honor_society_admin', $current_user_roles ))
        {

            $args = array(
                'post_status' => 'publish',
                'post_type' => array(TribeEvents::POSTTYPE),
                'posts_per_page' => 10,
            );

            $query = new WP_Query();
            $query->query($args);

            return $query;
        }

        return false;
    }


       /* $query->have_posts()
        return $query;*/
}