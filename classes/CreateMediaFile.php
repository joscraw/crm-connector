<?php

namespace CRMConnector;

/**
 * Class CreateMediaFile
 * @package CRMConnector
 */
class CreateMediaFile {

    public $post_id;
    public $file;
    public $wp_upload_url;
    public $attachment_id;
    public $field_name;

    /**
     * Setup the class variables
     * @param $file
     * @param $field_name
     * @param int $post_id
     */
    public function __construct( $file, $field_name, $post_id = 0 ) {

        // Setup class variables
        $this->file = $file;
        $this->field_name = $field_name;
        $this->post_id = absint( $post_id );
        $this->wp_upload_url = $this->get_wp_upload_url();
        $this->attachment_id = $this->attachment_id ?: false;

        return $this->create_image_id();

    }

    /**
     * Set the upload directory
     */
    private function get_wp_upload_url() {
        $wp_upload_dir = wp_upload_dir();
        return isset( $wp_upload_dir['url'] ) ? $wp_upload_dir['url'] : false;
    }

    /**
     * Create the image and return the new media upload id.
     *
     * @see https://gist.github.com/hissy/7352933
     *
     * @see http://codex.wordpress.org/Function_Reference/wp_insert_attachment#Example
     */
    public function create_image_id() {

        if ( !function_exists('wp_handle_upload') ) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        if( empty( $this->file ) || empty( $this->file ) )
            return false;

        // Move file to media library
        $movefile = wp_handle_upload( $this->file, array('test_form' => false) );

        // If move was successful, insert WordPress attachment
        if ( $movefile && !isset($movefile['error']) ) {
            $attachment = array(
                'guid' => $this->wp_upload_url.'/'.basename($movefile['file']),
                'post_mime_type' => $movefile['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attachment_id = wp_insert_attachment($attachment, $movefile['file']);

            if( ! is_wp_error( $attachment_id ) ) {

                require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );

                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file']);
                wp_update_attachment_metadata( $attachment_id,  $attachment_data );

                $this->attachment_id = $attachment_id;

                update_field($this->field_name, $attachment_id, $this->post_id);

                return $attachment_id;
            }
        }

        return false;

    } // end function get_image_id
}