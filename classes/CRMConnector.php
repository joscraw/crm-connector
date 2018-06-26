<?php


class CRMConnector
{
    private static $initiated = false;

    public static function init() {
        if ( ! self::$initiated ) {
            self::init_hooks();
        }

    }

    public static function init_hooks() {

        self::$initiated = true;
        add_action('admin_menu', array( 'CRMConnector', 'add_menu_pages' ), 10);
        add_action('admin_menu', array( 'CRMConnector', 'add_non_menu_pages' ), 10);
        /*add_action('admin_enqueue_scripts', array( 'CRMConnector', 'plugin_js'));*/
        add_action('admin_enqueue_scripts', array( 'CRMConnector', 'plugin_css'));
        add_action('admin_post_crmc_add_hubspot_api_key', array( 'CRMConnector', 'add_api_key_action'));
        add_action('admin_post_crmc_add_algolia_api_keys', array( 'CRMConnector', 'add_algolia_api_keys_action'));


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
        /*wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array('jquery'), null, true);*/
    }

    public static function plugin_css()
    {
        wp_register_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        wp_register_style( 'crm-connector.css', plugin_dir_url( __FILE__ ) . '../assets/crm-connector.css', array(), CRM_CONNECTOR_VERSION );
        wp_enqueue_style('bootstrap');
        wp_enqueue_style('crm-connector.css');
    }



    public static function add_api_key_action() {

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

        $errors = [];

        if( !isset( $_POST['crmc_add_algolia_api_keys_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_algolia_api_keys_nonce'], 'crmc_add_algolia_api_keys_nonce') ) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_algolia_application_id']) || !isset($_POST['crmc_algolia_api_key'])) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_algolia_application_id'])) {
            $errors['crmc_crmc_algolia_application_id'][] = 'You must enter in an Application ID.';
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
        $j = get_transient('successMessage');

        wp_redirect( $url );
        exit;

    }


}