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
        self::create_scholarships_posttype();
        self::create_potential_duplicates_posttype();
        self::create_reports_posttype();
        self::create_chapter_leadership();
        self::create_chapter_activity_reports();
        self::create_pace();
        self::create_chapter_update();
        self::create_partner_account();
        self::distinguished_member();
        self::benefits();
        self::partner_opportunities();
        self::officer_materials();
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

    private static function create_chapter_activity_reports()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Chapter Activity Report',
            'singular_name'       => 'Chapter Activity Report',
            'menu_name'           => 'Chapter Activity Report',
            'parent_item_colon'   => 'Parent Chapter Activity Report',
            'all_items'           => 'All Chapter Activity Reports',
            'view_item'           => 'View Chapter Activity Reports',
            'add_new_item'        => 'Add New Chapter Activity Report',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Chapter Activity Report',
            'update_item'         => 'Update Chapter Activity Report',
            'search_items'        => 'Search Chapter Activity Reports',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'chapter_activity_report',
            'description'         => 'Chapter Activity Reports',
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
            'capability_type'     => array('cap_chapter_activity_report','cap_chapter_activity_reports'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'chapter_act_repo', $args );
    }

    private static function create_pace()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Pace',
            'singular_name'       => 'Pace',
            'menu_name'           => 'Pace',
            'parent_item_colon'   => 'Pace',
            'all_items'           => 'All Paces',
            'view_item'           => 'View Paces',
            'add_new_item'        => 'Add New Pace',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Pace',
            'update_item'         => 'Update Pace',
            'search_items'        => 'Search Paces',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'pace',
            'description'         => 'Paces',
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
            'capability_type'     => array('cap_pace','cap_paces'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'pace', $args );
    }

    private static function create_chapter_update()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Chapter Update',
            'singular_name'       => 'Chapter Update',
            'menu_name'           => 'Chapter Update',
            'parent_item_colon'   => 'Chapter Update',
            'all_items'           => 'All Chapter Updates',
            'view_item'           => 'View Chapter Updates',
            'add_new_item'        => 'Add New Chapter Update',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Chapter Update',
            'update_item'         => 'Update Chapter Update',
            'search_items'        => 'Search Chapter Updates',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'chapter_updates',
            'description'         => 'Chapter Updates',
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
            'capability_type'     => array('cap_chapter_update','cap_chapter_updates'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'chapter_update', $args );
    }

    private static function create_partner_account()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Partner Account',
            'singular_name'       => 'Partner Account',
            'menu_name'           => 'Partner Account',
            'parent_item_colon'   => 'Partner Account',
            'all_items'           => 'All Partner Accounts',
            'view_item'           => 'View Partner Accounts',
            'add_new_item'        => 'Add New Partner Account',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Partner Account',
            'update_item'         => 'Update Partner Account',
            'search_items'        => 'Search Partner Accounts',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'partner_accounts',
            'description'         => 'Partner Accounts',
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
            'capability_type'     => array('cap_partner_account','cap_partner_accounts'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'partner_account', $args );
    }

    private static function distinguished_member()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Distinguished Member',
            'singular_name'       => 'Distinguished Member',
            'menu_name'           => 'Distinguished Member',
            'parent_item_colon'   => 'Distinguished Member',
            'all_items'           => 'All Distinguished Members',
            'view_item'           => 'View Distinguished Members',
            'add_new_item'        => 'Add New Distinguished Member',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Distinguished Member',
            'update_item'         => 'Update Distinguished Member',
            'search_items'        => 'Search Distinguished Members',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'distinguished_members',
            'description'         => 'Distinguished Members',
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
            'capability_type'     => array('cap_distinguished_member','cap_distinguished_members'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'distinguished_member', $args );
    }

    private static function benefits()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Benefit',
            'singular_name'       => 'Benefit',
            'menu_name'           => 'Benefit',
            'parent_item_colon'   => 'Benefit',
            'all_items'           => 'All Benefits',
            'view_item'           => 'View Benefits',
            'add_new_item'        => 'Add New Benefit',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Benefit',
            'update_item'         => 'Update Benefit',
            'search_items'        => 'Search Benefits',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'benefits',
            'description'         => 'Benefits',
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
            'capability_type'     => array('cap_benefit','cap_benefits'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'benefit', $args );
    }

    private static function partner_opportunities()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Partner Opportunity',
            'singular_name'       => 'Partner Opportunity',
            'menu_name'           => 'Partner Opportunity',
            'parent_item_colon'   => 'Partner Opportunity',
            'all_items'           => 'All Partner Opportunities',
            'view_item'           => 'View Partner Opportunities',
            'add_new_item'        => 'Add New Partner Opportunity',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Partner Opportunity',
            'update_item'         => 'Update Partner Opportunity',
            'search_items'        => 'Search Partner Opportunities',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'partner_opportunities',
            'description'         => 'Partner Opportunities',
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
            'capability_type'     => array('cap_partner_opportunity','cap_partner_opportunities'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'partner_opportunity', $args );
    }

    private static function officer_materials()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => 'Officer Material',
            'singular_name'       => 'Officer Material',
            'menu_name'           => 'Officer Material',
            'parent_item_colon'   => 'Officer Material',
            'all_items'           => 'All Officer Materials',
            'view_item'           => 'View Officer Materials',
            'add_new_item'        => 'Add New Officer Material',
            'add_new'             => 'Add New',
            'edit_item'           => 'Edit Officer Material',
            'update_item'         => 'Update Officer Material',
            'search_items'        => 'Search Officer Materials',
            'not_found'           => 'Not Found',
            'not_found_in_trash'  => 'Not found in Trash',
        );

        // Set other options for Custom Post Type

        $args = array(
            'label'               => 'officer_materials',
            'description'         => 'Officer Materials',
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
            'capability_type'     => array('cap_officer_material','cap_officer_materials'),
            'map_meta_cap'        => true,
            /*'capabilities' => array(
                'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
            ),*/
        );

        // Registering your Custom Post Type
        register_post_type( 'officer_material', $args );
    }
}