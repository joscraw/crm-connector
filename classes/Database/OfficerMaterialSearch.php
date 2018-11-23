<?php

namespace CRMConnector\Database;

use WP_Query;

/**
 * Class OfficerMaterialSearch
 */
class OfficerMaterialSearch
{
    /**
     * @param $chapter
     * @return bool|WP_Query
     */
    public function getAllByChapter($chapter)
    {
            $args = [
                'post_type' => 'officer_material',
                'posts_per_page' => 10,
                'paged' => get_query_var( 'paged' ) ?: 1,
                'meta_query' => array(
                    array(
                        'key' => 'chapter',
                        'value' => $chapter,
                        'compare' => '=',
                    ),
                ),
            ];

            if ( $s = get_query_var('search', '')  ) {
                $args['s'] = $s;
            }

            $query = new WP_Query($args);

            return $query;
    }
}