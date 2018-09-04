<?php

namespace CRMConnector\Scholarships;

use WP_Query;

/**
 * Class ScholarshipSearch
 */
class ScholarshipSearch
{

    public function getAllScholarships()
    {
            $args = [
                'post_type' => 'scholarships',
                'posts_per_page' => -1,
            ];

            $query = new WP_Query($args);

            if(!$query->have_posts())
            {
                return false;
            }

            $query = new WP_Query();
            $query->query($args);

            return $query;
    }
}