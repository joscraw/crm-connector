<?php
/**
 * Created by PhpStorm.
 * User: joshcrawmer
 * Date: 9/20/18
 * Time: 7:40 PM
 */

namespace CRMConnector\Database;

/**
 * Class DatabaseQuery
 * @package CRMConnector\Database
 */
trait DatabaseQuery
{
    /**
     * Returns a single column for a given post type
     *
     * @param string $key
     * @param string $type
     * @param string $status
     * @return array|void
     */
    public function get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

        global $wpdb;

        if( empty( $key ) )
            return;

        $r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = '%s' 
        AND p.post_status = '%s' 
        AND p.post_type = '%s'
    ", $key, $status, $type ) );

        return $r;
    }
}