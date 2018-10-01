<?php

namespace CRMConnector\Database;


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
        self::create_import_posttype();
        self::create_export_posttype();
        self::create_chapter_summary_posttype();
        self::create_partners_posttype();
        self::create_scholarships_posttype();
        self::create_potential_duplicates_posttype();
        self::create_reports_posttype();
        self::create_chapter_leadership();
    }

    private static function create_partners_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Partners',
            'singular_name'       => 'Partner',
            'menu_name'           => 'Partners',
            'parent_item_colon'   => 'Parent Partner',
            'all_items'           => 'All Partners',
            'view_item'           => 'View Partner',
            'add_new_item'        => 'Add New Partner',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Partner',
            'update_item'         => 'Update Partner',
            'search_items'        => 'Search Partners',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'Partners',
            'description'         => 'Partners',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_partner','cap_partners'),
            'map_meta_cap'        => true,
        );

        // Registering your Custom Post Type
        register_post_type( 'partners', $args );

    }

    private static function create_contact_posttype() {

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
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_contact','cap_contacts'),
            'map_meta_cap'        => true,
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
            'supports'            => array( 'title', 'custom-fields'),
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
            'capability_type'     => array('cap_chapter','cap_chapters'),
            'map_meta_cap'        => true,
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
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_chapter_invitation','cap_chapter_invitations'),
            'map_meta_cap'        => true,
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
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_drop','cap_drops'),
            'map_meta_cap'        => true,
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
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_list','cap_lists'),
            'map_meta_cap'        => true,
        );

        // Registering your Custom Post Type
        register_post_type( 'lists', $args );
    }

    private static function create_import_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Imports',
            'singular_name'       => 'Import',
            'menu_name'           => 'Imports',
            'parent_item_colon'   => 'Parent Imports',
            'all_items'           => 'All Imports',
            'view_item'           => 'View Imports',
            'add_new_item'        => 'Add New Import',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Import',
            'update_item'         => 'Update Import',
            'search_items'        => 'Search Imports',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'imports',
            'description'         => 'imports',
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
            'capability_type'     => array('cap_import','cap_imports'),
            'map_meta_cap'        => true,
        );

        // Registering your Custom Post Type
        register_post_type( 'imports', $args );
    }

    private static function create_export_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Exports',
            'singular_name'       => 'Export',
            'menu_name'           => 'Exports',
            'parent_item_colon'   => 'Parent Exports',
            'all_items'           => 'All Exports',
            'view_item'           => 'View Exports',
            'add_new_item'        => 'Add New Export',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Export',
            'update_item'         => 'Update Export',
            'search_items'        => 'Search Exports',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'exports',
            'description'         => 'exports',
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
            'capability_type'     => array('cap_export','cap_exports'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'exports', $args );
    }

    private static function create_chapter_summary_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Chapter Summaries',
            'singular_name'       => 'Chapter Summary',
            'menu_name'           => 'Chapter Summaries',
            'parent_item_colon'   => 'Parent Chapter Summaries',
            'all_items'           => 'All Chapter Summaries',
            'view_item'           => 'View Chapter Summaries',
            'add_new_item'        => 'Add New Chapter Summary',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Chapter Summary',
            'update_item'         => 'Update Chapter Summary',
            'search_items'        => 'Search Chapter Summaries',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'chapter_summaries',
            'description'         => 'Chapter Summaries',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_chapter_summary','cap_chapter_summaries'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'chapter_summaries', $args );
    }

    private static function create_scholarships_posttype() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Scholarships',
            'singular_name'       => 'Scholarship',
            'menu_name'           => 'Scholarships',
            'parent_item_colon'   => 'Parent Scholarships',
            'all_items'           => 'All Scholarships',
            'view_item'           => 'View Scholarships',
            'add_new_item'        => 'Add New Scholarship',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Scholarship',
            'update_item'         => 'Update Scholarship',
            'search_items'        => 'Search Scholarships',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'scholarships',
            'description'         => 'Scholarships',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'custom-fields' ),
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
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => array('cap_scholarship','cap_scholarships'),
            'map_meta_cap'        => true,
        );

        // Registering your Custom Post Type
        register_post_type( 'scholarships', $args );
    }

    private static function create_potential_duplicates_posttype()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Potential Duplicates',
            'singular_name'       => 'Potential Duplicate',
            'menu_name'           => 'Potential Duplicates',
            'parent_item_colon'   => 'Parent Potential Duplicates',
            'all_items'           => 'All Potential Duplicates',
            'view_item'           => 'View Potential Duplicates',
            'add_new_item'        => 'Add New Potential Duplicate',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Potential Duplicate',
            'update_item'         => 'Update Potential Duplicate',
            'search_items'        => 'Search Potential Duplicates',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'potential_duplicates',
            'description'         => 'Potential Duplicates',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_potential_duplicate','cap_potential_duplicates'),
            'map_meta_cap'        => true,
        );

        // Registering your Custom Post Type
        register_post_type( 'potential_duplicates', $args );
    }

    private static function create_reports_posttype()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Reports',
            'singular_name'       => 'Report',
            'menu_name'           => 'Reports',
            'parent_item_colon'   => 'Parent Reports',
            'all_items'           => 'All Reports',
            'view_item'           => 'View Reports',
            'add_new_item'        => 'Add New Report',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Report',
            'update_item'         => 'Update Report',
            'search_items'        => 'Search Reports',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'reports',
            'description'         => 'Reports',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_report','cap_reports'),
            'map_meta_cap'        => true,
        );

        // Registering your Custom Post Type
        register_post_type( 'reports', $args );
    }

    private static function create_chapter_leadership()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Chapter Leadership',
            'singular_name'       => 'Chapter Leadership',
            'menu_name'           => 'Chapter Leadership',
            'parent_item_colon'   => 'Parent Chapter Leadership',
            'all_items'           => 'All Chapter Leadership',
            'view_item'           => 'View Chapter Leadership',
            'add_new_item'        => 'Add New Chapter Leadership',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Chapter Leadership',
            'update_item'         => 'Update Chapter Leadership',
            'search_items'        => 'Search Chapter Leadership',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'chapter_leadership',
            'description'         => 'Chapter Leadership',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'custom-fields' ),
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
            'capability_type'     => array('cap_chapter_leadership','cap_chapter_leaderships'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'chapter_leadership', $args );
    }
}