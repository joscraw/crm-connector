<?php

require_once ('AlgoliaAdapter.php');

class CRMConnector
{
    private static $initiated = false;

    private static $wpdb;

    public static function init() {
        global $wpdb;
        self::$wpdb = $wpdb;

        if ( ! self::$initiated ) {
            self::init_hooks();
        }

    }

    public static function init_hooks() {

        $algoliaAdapter = new AlgoliaAdapter();

        self::$initiated = true;
        add_action('admin_menu', array( 'CRMConnector', 'add_menu_pages' ), 10);
        add_action('admin_menu', array( 'CRMConnector', 'add_non_menu_pages' ), 10);
        /*add_action('admin_enqueue_scripts', array( 'CRMConnector', 'plugin_js'));*/
        add_action('admin_enqueue_scripts', array( 'CRMConnector', 'plugin_css'));
        add_action('admin_enqueue_scripts', array( 'CRMConnector', 'plugin_js'));
        add_action('admin_post_crmc_add_hubspot_api_key', array( 'CRMConnector', 'add_api_key_action'));
        add_action('admin_post_crmc_add_algolia_api_keys', array( 'CRMConnector', 'add_algolia_api_keys_action'));
        add_action('admin_post_crmc_add_chapter', array( 'CRMConnector', 'add_chapter_action'));
        add_action('admin_post_crmc_add_chapter_mapping', array( 'CRMConnector', 'add_chapter_mapping_action'));

        add_action("wp_ajax_crmc_create_group", array( 'CRMConnector', 'create_group_action'));
        add_action("wp_ajax_crmc_create_property", array( 'CRMConnector', 'create_property_action'));



    }

    public static function add_menu_pages()
    {

        add_menu_page(
            __( 'CRM Connector', 'textdomain' ),
            'CRM Connector',
            'manage_options',
            'import',
            array('CRMConnector', 'get_import_action'),
            '',
            1
        );

    }


    public static function add_non_menu_pages()
    {
        add_submenu_page(
            null, 'Advanced Settings', 'Advanced Settings', 'manage_options', 'advanced-settings', array('CRMConnector', 'get_advanced_settings_action')
        );

        add_submenu_page(
            null, 'Chapters', 'Chapters', 'manage_options', 'chapters', array('CRMConnector', 'get_chapters_action')
        );

        add_submenu_page(
            null, 'Contacts', 'Contacts', 'manage_options', 'contacts', array('CRMConnector', 'get_contacts_action')
        );
    }


    public static function get_advanced_settings_action() {
        include_once(PLUGIN_DIR . 'views/advanced_settings.php');
    }

    public static function get_chapters_action() {
        include_once(PLUGIN_DIR . 'views/chapters.php');
    }

    public static function get_contacts_action() {
        include_once(PLUGIN_DIR . 'views/contacts.php');
    }

    public static function get_import_action() {
        include_once(PLUGIN_DIR . 'views/import.php');
    }


    public static function plugin_js()
    {
        wp_register_script('crm-connector.js', plugin_dir_url( __FILE__ ) . '../assets/crm-connector.js', array('jquery'), CRM_CONNECTOR_VERSION, true);
        wp_enqueue_script('crm-connector.js');
    }

    public static function plugin_css()
    {
        wp_register_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        wp_register_style( 'crm-connector.css', plugin_dir_url( __FILE__ ) . '../assets/crm-connector.css', array(), CRM_CONNECTOR_VERSION );
        wp_enqueue_style('bootstrap');
        wp_enqueue_style('crm-connector.css');
    }



    public static function add_api_key_action() {

        deleteTransients();

        $errors = [];

        if( !isset( $_POST['crmc_add_hubspot_api_key_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_hubspot_api_key_nonce'], 'crmc_add_hubspot_api_key_nonce') ) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_hubspot_api_key'])) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_hubspot_api_key'])) {
            $errors['crmc_hubspot_api_key'][] = 'You must enter in an API Key';
        }

        $slug = 'advanced-settings&pill=hubspot';
        $path = "admin.php?page=$slug";
        $url = admin_url($path);

        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 45 );
            wp_redirect( $url );
            exit;
        }


        $crmc_hubspot_api_key = sanitize_key( $_POST['crmc_hubspot_api_key']);
        if(!get_option('crmc_hubspot_api_key')) {
            add_option( 'crmc_hubspot_api_key', $crmc_hubspot_api_key, '', 'yes' );
        } else {
            update_option( 'crmc_hubspot_api_key', $crmc_hubspot_api_key);
        }

        set_transient( 'successMessage', 'HubSpot API credentials successfully saved.', 45 );

        wp_redirect( $url );
        exit;

    }

    public static function add_algolia_api_keys_action() {

        deleteTransients();

        $errors = [];

        if( !isset( $_POST['crmc_add_algolia_api_keys_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_algolia_api_keys_nonce'], 'crmc_add_algolia_api_keys_nonce') ) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_algolia_application_id']) || !isset($_POST['crmc_algolia_api_key'])) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_algolia_application_id'])) {
            $errors['crmc_algolia_application_id'][] = 'You must enter in an Application ID.';
        }

        if(empty($_POST['crmc_algolia_api_key'])) {
            $errors['crmc_algolia_api_key'][] = 'You must enter in an API Key.';
        }

        $slug = 'advanced-settings&pill=algolia';
        $path = "admin.php?page=$slug";
        $url = admin_url($path);

        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 45 );
            wp_redirect( $url );
            exit;
        }


        $algolia_application_id = sanitize_key( $_POST['crmc_algolia_application_id']);
        $algolia_api_key = sanitize_key( $_POST['crmc_algolia_api_key']);

        if(!get_option('crmc_algolia_application_id') || !get_option('crmc_algolia_api_key')) {
            add_option( 'crmc_algolia_application_id', $algolia_application_id, '', 'yes' );
            add_option( 'crmc_algolia_api_key', $algolia_api_key, '', 'yes' );
        } else {
            update_option( 'crmc_algolia_application_id', $algolia_application_id );
            update_option( 'crmc_algolia_api_key', $algolia_api_key );
        }

        set_transient( 'successMessage', 'Algolia API credentials successfully saved.', 45 );

        wp_redirect( $url );
        exit;

    }


    public static function add_chapter_action() {

        deleteTransients();

        $errors = [];

        if( !isset( $_POST['crmc_add_chapter_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_chapter_nonce'], 'crmc_add_chapter_nonce') ) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_chapter_name'])) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_chapter_name'])) {
            $errors['crmc_chapter_name'][] = 'You must enter a Chapter Name.';
        }


        $slug = 'chapters&pill=add';
        $path = "admin.php?page=$slug";
        $url = admin_url($path);

        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 45 );
            wp_redirect( $url );
            exit;
        }

        $chapter_name = sanitize_text_field( $_POST['crmc_chapter_name']);
        $tablename = self::$wpdb->prefix.'chapters';

        self::$wpdb->insert( $tablename, array(
            'chapter_name' => $chapter_name
        ));

        set_transient( 'successMessage', 'Chapter successfully created.', 45 );

        wp_redirect( $url );
        exit;

    }


    public static function add_chapter_mapping_action() {

        deleteTransients();

        $errors = [];

        if( !isset( $_POST['crmc_add_chapter_mapping_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_chapter_mapping_nonce'], 'crmc_add_chapter_mapping_nonce') ) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['groups'])) {
            $errors['main'][] = 'You must add at least 1 Property Group before you can create!';
        }


        $groups_table = self::$wpdb->prefix.'groups';
        $properties_table = self::$wpdb->prefix.'properties';

        $groups = $_POST['groups'];
        foreach($groups as $key => $value) {
            if(empty($value)) {
                continue;
            }

            if(empty($value['group'])) {
                $errors["groups[$key][group]"][] = 'You must enter a Chapter Name.';
                continue;
            }

            self::$wpdb->insert( $groups_table, array(
                'group_name' => $value['group']
            ));
            $last_inserted_group_id = self::$wpdb->insert_id;

            if(empty($value['properties']) || !$last_inserted_group_id) {
                continue;
            }

                foreach($value['properties'] as $property) {
                    if(empty($property['property_name'])) {
                        continue;
                    }
                    self::$wpdb->insert( $properties_table, array(
                        'group_id' => $last_inserted_group_id,
                        'property_name' => $property['property_name'],
                        'property_value' => $property['property_value'],
                    ));
                }
            }


        $slug = 'chapters&pill=mapping';
        $path = "admin.php?page=$slug";
        $url = admin_url($path);


        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 45 );
            wp_redirect( $url );
            exit;
        }

        wp_redirect( $url );
        exit;

    }



    public static function create_group_action() {

        $groups_table = self::$wpdb->prefix.'groups';
        self::$wpdb->insert( $groups_table, array(
            'group_name' => ""
        ));
        $group_id = self::$wpdb->insert_id;

        $result = [];
        $result['type'] = "success";
        $result['group_id'] = $group_id;
        echo json_encode($result);
        exit;
    }


    public static function create_property_action() {

        $groups_table = self::$wpdb->prefix.'properties';

        $group = $_POST['group'];

        self::$wpdb->insert( $groups_table, array(
            'group_id' => $group,
        ));


        $result = [];
        $result['type'] = "success";
        echo json_encode($result);
        exit;
    }




}