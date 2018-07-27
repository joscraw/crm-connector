<?php

/*
Plugin Name: CRM Connector
Plugin URI: https://www.giftedhire.com/
Description: Connects Wordpress Site to CRM to manage/import students
Version: 1.0.0
Author: Josh Crawmer <joshcrawmer4@yahoo.com>
License: GPLv2 or later
*/

use CRMConnector\Backend;
use CRMConnector\Concerns\CrmConnectorAdminBar;
use CRMConnector\Importer\CRMCDatabaseTables;
use CRMConnector\Support\DatabaseTables;

require_once __DIR__ . '/vendor/autoload.php';
require_once( plugin_dir_path( __FILE__ ). 'includes/helpers.php' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}




class CRMConnector {

    use CrmConnectorAdminBar;

    /**
     * @var string
     */
    public $version = '1.0.0';


    public function __construct()
    {
    }

    /**
     * Run when the plugin is activated
     */
    public function activate()
    {
    }

    /**
     * Run during the initialization of Wordpress
     */
    public function initialize()
    {

        if(is_admin()) {
            global $crmConnectorBackend;

            $crmConnectorBackend = new Backend();
        }

        CRMCDatabaseTables::verify();


        $this->registerAdminBarMenu();


    }

    public function create_contact_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Contacts',
            'singular_name'       => 'Contact',
            'menu_name'           => 'Contacts',
            'parent_item_colon'   => 'Parent Contact',
            'all_items'           => 'All Contacts',
            'view_item'           => 'View Contact',
            'add_new_item'        => 'Add New Contact',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Contact',
            'update_item'         => 'Update Contact',
            'search_items'        => 'Search Contacts',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

// Set other options for Custom Post Type

        $args = array(
            'label'               => 'contacts',
            'description'         => 'Imported Contacts',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'custom-fields', ),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            'taxonomies'          => array( 'chapters' ),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );

        // Registering your Custom Post Type
        register_post_type( 'contacts', $args );
    }

    public function create_chapter_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Chapters',
            'singular_name'       => 'Chapter',
            'menu_name'           => 'Chapters',
            'parent_item_colon'   => 'Parent Chapter',
            'all_items'           => 'All Chapters',
            'view_item'           => 'View Chapter',
            'add_new_item'        => 'Add New Chapter',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Chapter',
            'update_item'         => 'Update Chapter',
            'search_items'        => 'Search Chapters',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

// Set other options for Custom Post Type

        $args = array(
            'label'               => 'chapters',
            'description'         => 'Imported Chapters',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array('custom-fields'),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            'taxonomies'          => array(),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );

        // Registering your Custom Post Type
        register_post_type( 'chapters', $args );
    }

    public function create_chapter_invitations_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Chapter Invitations',
            'singular_name'       => 'Chapter Invitation',
            'menu_name'           => 'Chapter Invitations',
            'parent_item_colon'   => 'Parent Invitation',
            'all_items'           => 'All Chapter Invitations',
            'view_item'           => 'View Chapter Invitation',
            'add_new_item'        => 'Add New Chapter Invitation',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Chapter Invitation',
            'update_item'         => 'Update Chapter Invitation',
            'search_items'        => 'Search Chapter Invitations',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

// Set other options for Custom Post Type

        $args = array(
            'label'               => 'chapter invitations',
            'description'         => 'Imported Chapter Invitations',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'custom-fields' ),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            'taxonomies'          => array(),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );

        // Registering your Custom Post Type
        register_post_type( 'chapters_invitations', $args );
    }

    public function create_drop_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Drops',
            'singular_name'       => 'Drop',
            'menu_name'           => 'Drops',
            'parent_item_colon'   => 'Parent Drops',
            'all_items'           => 'All Drops',
            'view_item'           => 'View Drops',
            'add_new_item'        => 'Add New Drop',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Drop',
            'update_item'         => 'Update Drop',
            'search_items'        => 'Search Drops',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

// Set other options for Custom Post Type

        $args = array(
            'label'               => 'drops',
            'description'         => 'drops',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'custom-fields' ),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            'taxonomies'          => array(),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );

        // Registering your Custom Post Type
        register_post_type( 'drops', $args );
    }

}


$CRMConnectorPlugin = new CRMConnector();

register_activation_hook( __FILE__, array($CRMConnectorPlugin, 'activate'));

add_action('init', array($CRMConnectorPlugin, 'initialize'));
// Hooking up our function to theme setup
add_action( 'init', array($CRMConnectorPlugin, 'create_contact_posttype') );
add_action( 'init', array($CRMConnectorPlugin, 'create_chapter_posttype') );
add_action( 'init', array($CRMConnectorPlugin, 'create_chapter_invitations_posttype') );
add_action( 'init', array($CRMConnectorPlugin, 'create_drop_posttype') );

function custom_js_to_head() {
    if(isset($_GET['action']) && $_GET['action'] === 'edit'):
    ?>
    <script>
        jQuery(function(){
            jQuery("body.post-type-chapters .wrap h1").append('<a href="javascript:void(0)" class="page-title-action js-show-import-modal-button">Import Contacts</a>');
        });
    </script>

    <?php
    endif;
}

add_action('admin_head','custom_js_to_head');

function my_acf_admin_head() {
    ?>
    <script type="text/javascript">
        (function($){

            $(document).ready(function(){

                $('.layout').addClass('-collapsed');
                $('.acf-postbox').addClass('closed');

            });

        })(jQuery);
    </script>
    <?php
}

add_action('acf/input/admin_head', 'my_acf_admin_head');

add_filter('wp_insert_post_data', function($data, $post) {


    if(in_array($post['post_type'], ['chapters', 'contacts', 'chapters_invitations']))
    {
        if($post['acf'])
            $data['post_title'] = array_shift($post['acf']);
    }


    return $data;
}, 10, 2);



