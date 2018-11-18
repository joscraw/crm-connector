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
     * Returns entire post including post meta values
     *
     * @param $post_type
     * @param $post_id
     * @return array|bool
     */
    public function get_post_with_meta_values_from_post_id($post_type, $post_id)
    {
        $data = [];

        if(!$post = get_post($post_id)) {
            return false;
        }

        foreach($post as $key => $value) {
            $data[$key] = $value;
        }

        foreach($this->get_field_names_for_post_type($post_type) as $field) {
            $data[$field] = get_post_meta($post_id, $field, true) !== false ? get_post_meta($post_id, $field, true) : null;
        }
        return $data;
    }

    /**
     * @param $post_type
     * @return array
     */
    public function get_field_names_for_post_type($post_type)
    {
        $groups = acf_get_field_groups(array('post_type' => $post_type));
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

                $fields[] = $group_field['name'];
            }
        }
        return $fields;
    }

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