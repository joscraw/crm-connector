<?php

namespace CRMConnector\Service\CustomPostType;


/**
 * Class CustomPostTypeCreator
 */
class CustomPostTypeCreator
{

    public static function create()
    {
        self::create_contact_posttype();
        self::create_chapter_posttype();
        self::create_chapter_invitations_posttype();
        self::create_drop_posttype();
        self::create_list_posttype();
    }

    private function create_contact_posttype() {

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
        );

        // Registering your Custom Post Type
        register_post_type( 'contacts', $args );
    }

    private static function create_chapter_posttype() {

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
        );

        // Registering your Custom Post Type
        register_post_type( 'chapters', $args );
    }

    private static function create_chapter_invitations_posttype() {

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
        );

        // Registering your Custom Post Type
        register_post_type( 'chapters_invitations', $args );
    }

    private static function create_drop_posttype() {

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
        );

        // Registering your Custom Post Type
        register_post_type( 'drops', $args );
    }

    private static function create_list_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Lists',
            'singular_name'       => 'List',
            'menu_name'           => 'Lists',
            'parent_item_colon'   => 'Parent Lists',
            'all_items'           => 'All Lists',
            'view_item'           => 'View Lists',
            'add_new_item'        => 'Add New List',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit List',
            'update_item'         => 'Update List',
            'search_items'        => 'Search Lists',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'lists',
            'description'         => 'lists',
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
        );

        // Registering your Custom Post Type
        register_post_type( 'lists', $args );
    }
}