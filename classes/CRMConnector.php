<?php

require_once ('AlgoliaAdapter.php');
require_once(PLUGIN_DIR . 'vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        self::$initiated = true;

        add_action('admin_menu', array( 'CRMConnector', 'add_menu_pages' ), 10);
        add_action('admin_menu', array( 'CRMConnector', 'add_non_menu_pages' ), 10);
        add_action('admin_enqueue_scripts', array( 'CRMConnector', 'plugin_css'));
        add_action('admin_enqueue_scripts', array( 'CRMConnector', 'plugin_js'));
        add_action('admin_post_crmc_add_hubspot_api_key', array( 'CRMConnector', 'add_api_key_action'));
        add_action('admin_post_crmc_add_algolia_api_keys', array( 'CRMConnector', 'add_algolia_api_keys_action'));
        add_action('admin_post_crmc_add_chapter', array( 'CRMConnector', 'add_chapter_action'));
        add_action('admin_post_crmc_add_chapter_mapping', array( 'CRMConnector', 'add_chapter_mapping_action'));
        add_action('admin_post_crmc_import_contacts', array( 'CRMConnector', 'import_contacts_action'));
        add_action("wp_ajax_crmc_create_group", array( 'CRMConnector', 'create_group_action'));
        add_action("wp_ajax_crmc_create_property", array( 'CRMConnector', 'create_property_action'));
        add_action("wp_ajax_crmc_set_group_name", array( 'CRMConnector', 'create_group_name_action'));
        add_action("wp_ajax_crmc_set_property_name", array( 'CRMConnector', 'create_property_name_action'));
        add_action("wp_ajax_crmc_set_property_value", array( 'CRMConnector', 'create_property_value_action'));
        add_action("wp_ajax_crmc_get_column_names", array( 'CRMConnector', 'get_column_names_action'));

    }

    public static function add_menu_pages()
    {

        add_menu_page(
            __( 'CRM Connector', 'textdomain' ),
            'CRM Connector',
            'manage_options',
            'contacts',
            array('CRMConnector', 'get_contacts_action'),
            '',
            1
        );

    }


    public static function add_non_menu_pages()
    {

        add_submenu_page(
            'contacts', 'Contacts', 'Contacts', 'manage_options', 'contacts', array('CRMConnector', 'get_contacts_action')
        );


        add_submenu_page(
            'contacts', 'Chapters', 'Chapters', 'manage_options', 'chapters', array('CRMConnector', 'get_chapters_action')
        );

        add_submenu_page(
            'contacts', 'Advanced Settings', 'Advanced Settings', 'manage_options', 'advanced-settings', array('CRMConnector', 'get_advanced_settings_action')
        );


        add_submenu_page(
            '', 'Import', 'Import', 'manage_options', 'import', array('CRMConnector', 'get_import_action')
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
        wp_register_script( 'instant-search.js', 'https://cdn.jsdelivr.net/npm/instantsearch.js@2.4/dist/instantsearch.min.js');
        wp_register_script('crm-connector.js', plugin_dir_url( __FILE__ ) . '../assets/crm-connector.js', array('jquery'), CRM_CONNECTOR_VERSION, true);
        wp_enqueue_script('instant-search.js');
        wp_enqueue_script('crm-connector.js');
    }

    public static function plugin_css()
    {
        /*wp_register_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');*/
        wp_register_style('instant-search.css', 'https://cdn.jsdelivr.net/npm/instantsearch.js@2.4/dist/instantsearch.min.css');
        wp_register_style('bootstrap-local', plugin_dir_url( __FILE__ ) . '../assets/bootstrap.min.css', array(), CRM_CONNECTOR_VERSION);
        wp_register_style( 'crm-connector.css', plugin_dir_url( __FILE__ ) . '../assets/crm-connector.css', array(), CRM_CONNECTOR_VERSION );
        wp_register_style( 'crm-connector.css', plugin_dir_url( __FILE__ ) . '../assets/crm-connector.css', array(), CRM_CONNECTOR_VERSION );

        /*wp_enqueue_style('bootstrap');*/
        wp_enqueue_style('instant-search.css');
        wp_enqueue_style('bootstrap-local');
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

        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 1 );
            redirectToPage(array('page' => 'advanced-settings','pill' => 'hubspot'));
            exit;
        }


        $crmc_hubspot_api_key = sanitize_key( $_POST['crmc_hubspot_api_key']);
        if(!get_option('crmc_hubspot_api_key')) {
            add_option( 'crmc_hubspot_api_key', $crmc_hubspot_api_key, '', 'yes' );
        } else {
            update_option( 'crmc_hubspot_api_key', $crmc_hubspot_api_key);
        }

        set_transient( 'successMessage', 'HubSpot API credentials successfully saved.', 1 );

        redirectToPage(array('page' => 'advanced-settings','pill' => 'hubspot'));
        exit;

    }

    public static function add_algolia_api_keys_action() {

        deleteTransients();

        $errors = [];

        if( !isset( $_POST['crmc_add_algolia_api_keys_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_algolia_api_keys_nonce'], 'crmc_add_algolia_api_keys_nonce') ) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_algolia_application_id']) || !isset($_POST['crmc_algolia_api_key']) || !isset($_POST['crmc_algolia_index']) || !isset($_POST['crmc_algolia_search_only_api_key'])) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_algolia_application_id'])) {
            $errors['crmc_algolia_application_id'][] = 'You must enter in an Application ID.';
        }

        if(empty($_POST['crmc_algolia_api_key'])) {
            $errors['crmc_algolia_api_key'][] = 'You must enter in an API Key.';
        }

        if(empty($_POST['crmc_algolia_index'])) {
            $errors['crmc_algolia_index'][] = 'You must enter in index name.';
        }

        if(empty($_POST['crmc_algolia_search_only_api_key'])) {
            $errors['crmc_algolia_search_only_api_key'][] = 'You must enter in a search only api key.';
        }


        $algoliaAdapter = null;
        try{
            $algoliaAdapter = new AlgoliaAdapter($_POST['crmc_algolia_application_id'], $_POST['crmc_algolia_api_key'], $_POST['crmc_algolia_index']);
        } catch(\Exception $exception) {
            $errors['main'][] = $exception->getMessage();
        }

        // This is used to validate the API keys

        if($algoliaAdapter) {
            try{
                $response = $algoliaAdapter->listKeys();
            } catch(\Exception $exception) {
                $errors['main'][] = $exception->getMessage();
            }
        }

        if(!empty($response)) {
            if(!in_array($_POST['crmc_algolia_search_only_api_key'], array_column($response['keys'], 'value'))) {
                $errors['crmc_algolia_search_only_api_key'][] = 'You must enter in a valid search only api key.';
            }
        }

        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 1 );
            redirectToPage(array('page' => 'advanced-settings', 'pill' => 'algolia'));
            exit;
        }


        $algolia_application_id = sanitize_text_field( $_POST['crmc_algolia_application_id']);
        $algolia_api_key = sanitize_text_field( $_POST['crmc_algolia_api_key']);
        $algolia_index = sanitize_text_field( $_POST['crmc_algolia_index']);
        $algolia_search_only_api_key = sanitize_text_field($_POST['crmc_algolia_search_only_api_key']);

        update_option( 'crmc_algolia_application_id', $algolia_application_id );
        update_option( 'crmc_algolia_api_key', $algolia_api_key );
        update_option( 'crmc_algolia_index', $algolia_index );
        update_option('crmc_algolia_search_only_api_key', $algolia_search_only_api_key);



        set_transient( 'successMessage', 'Algolia API credentials successfully saved.', 1 );

        redirectToPage(array('page' => 'advanced-settings', 'pill' => 'algolia'));
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


        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 1 );
            redirectToPage(array('page' => 'chapters','pill' => 'add'));
            exit;
        }

        $chapter_name = sanitize_text_field( $_POST['crmc_chapter_name']);
        $tablename = self::$wpdb->prefix.'chapters';

        self::$wpdb->insert( $tablename, array(
            'chapter_name' => $chapter_name
        ));

        set_transient( 'successMessage', 'Chapter successfully created.', 1 );

        redirectToPage(array('page' => 'chapters','pill' => 'add'));
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



        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 1 );
            redirectToPage(array('page' => 'chapters','pill' => 'mapping'));
            exit;
        }

        redirectToPage(array('page' => 'chapters','pill' => 'mapping'));
        exit;

    }


    /**
     * Creates empty group in the database
     */
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


    /**
     * Creates empty property in the database
     */
    public static function create_property_action() {

        $groups_table = self::$wpdb->prefix.'properties';

        $group = $_POST['group'];

        self::$wpdb->insert( $groups_table, array(
            'group_id' => $group,
        ));

        $property_id = self::$wpdb->insert_id;

        $result = [];
        $result['type'] = "success";
        $result['property_id'] = $property_id;
        echo json_encode($result);
        exit;
    }


    /**
     * Adds/modifies an empty group name that has been created
     */
    public static function create_group_name_action() {

        $groups_table = self::$wpdb->prefix.'groups';
        $group_id = sanitize_key($_POST['group']);
        $group_name = sanitize_text_field($_POST['group_name']);

        self::$wpdb->query(self::$wpdb->prepare("UPDATE $groups_table set group_name= %s WHERE id= %s",$group_name, $group_id));

        $result = [];
        $result['type'] = "success";
        echo json_encode($result);
        exit;

    }

    /**
     * Adds/modifies an empty property name that has been created
     */
    public static function create_property_name_action() {

        $properties_table = self::$wpdb->prefix.'properties';
        $group_id = sanitize_key($_POST['group']);
        $property_name = sanitize_text_field($_POST['property_name']);
        $property_id = sanitize_key($_POST['property_id']);

        self::$wpdb->query(self::$wpdb->prepare("UPDATE $properties_table set property_name= %s WHERE group_id= %s and id= %s",$property_name, $group_id, $property_id));

        $result = [];
        $result['type'] = "success";
        echo json_encode($result);
        exit;
    }

    /**
     * Adds/modifies an empty property value that has been created
     */
    public static function create_property_value_action() {

        $properties_table = self::$wpdb->prefix.'properties';
        $group_id = sanitize_key($_POST['group']);
        $property_value = sanitize_text_field($_POST['property_value']);
        $property_id = sanitize_key($_POST['property_id']);

        self::$wpdb->query(self::$wpdb->prepare("UPDATE $properties_table set property_value= %s WHERE group_id= %s and id= %s",$property_value, $group_id, $property_id));

        $result = [];
        $result['type'] = "success";
        echo json_encode($result);
        exit;
    }


    /**
     * This function Validates an uploaded file and copies it over into the proper folder.
     * It also adds a job to the crons table for our background workers/crons to pick up
     * to start the process of importing the data into algolia
     */
    public static function import_contacts_action() {

        deleteTransients();
        $errors = [];

        if(empty($_POST['chapter_id']) || !isset($_FILES['studentFile'])) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_FILES['studentFile'])) {
            $errors['studentFile'][] = 'Please add an excel file to import';
        }


        // Check the file MIME Type
        $supported_file_extensions = array(
            'xsl' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        if(false === $ext = array_search($finfo->file($_FILES['studentFile']['tmp_name']), $supported_file_extensions)) {
            $errors['studentFile'][] = sprintf("Supported file types are (%s)", implode(", ", array_keys($supported_file_extensions)));
        }

            // Check $_FILES['upfile']['error'] value.
            switch ($_FILES['studentFile']['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errors['main'][] = 'No file sent.';
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                $errors['main'][] = 'Exceeded filesize limit.';
                break;
                default:
                    $errors['main'][] = 'Unknown error.';
            }

            // You should also check filesize here.
            if ($_FILES['studentFile']['size'] > 50000000) {
                $errors['main'][] = 'Exceeded filesize limit.';
            }


        $chapter_id = self::$wpdb->get_var(sprintf("SELECT id FROM %schapters WHERE id = '%s'",
            self::$wpdb->prefix,
            $_POST['chapter_id']
        ));


        if(null === $chapter_id) {
            $errors['main'][] = 'That chapter does not exist.';
        }


        if(count($errors) > 0) {
            set_transient( 'errors', $errors, 1 );
            redirectToPage(array('page' => 'import','chapter_id' => $chapter_id));
            exit;
        }


        $upload_path = sprintf(
            '%s%s/%s',
            PLUGIN_DIR,
            'imports',
            $chapter_id
        );


        if ( ! is_dir($upload_path)) {
            mkdir($upload_path);
        }


        $upload_path = sprintf(
            '%s/%s.%s',
            $upload_path,
            date('m-d-Y_hia'),
            $ext
        );

        if(false === move_uploaded_file($_FILES['studentFile']['tmp_name'], $upload_path)) {
            $errors['main'][] = 'System Error.';

            redirectToPage(array('page' => 'import','chapter_id' => $chapter_id));
            exit;
        }


        //upload the data to algolia



        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($upload_path);

        $rows = $spreadsheet->getActiveSheet()->toArray();
        $records = [];
        foreach($rows as $row) {
            $record = array_combine($_POST['database_column_name'], $row);
            $records[] = $record;
        }


        $algoliaAdapter = new AlgoliaAdapter(get_option('crmc_algolia_application_id'), get_option('crmc_algolia_api_key'), get_option('crmc_algolia_index'));
        try {
            $response = $algoliaAdapter->addObjects($records);
        } catch(\Exception $exception) {
            $errors['main'][] = $exception->getMessage();
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'import','chapter_id' => $chapter_id));

            exit;
        }

        set_transient( 'successMessage', 'Student file list successfully imported into Algolia', 1 );
        redirectToPage(array('page' => 'import','chapter_id' => $chapter_id));
        exit;

    }


    /**
     * This function gets the column names for a given import file
     * This is more of a convenience call allowing the user to not have to
     * map all the column names manually
     */
    public static function get_column_names_action() {

        deleteTransients();
        $errors = [];

        if(!isset($_FILES['studentFile'])) {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_FILES['studentFile'])) {
            $errors['studentFile'][] = 'Please add an excel file to import';
        }


        // Check the file MIME Type
        $supported_file_extensions = array(
            'xsl' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        if(false === $ext = array_search($finfo->file($_FILES['studentFile']['tmp_name']), $supported_file_extensions)) {
            $errors['studentFile'][] = sprintf("Supported file types are (%s)", implode(", ", array_keys($supported_file_extensions)));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['studentFile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors['main'][] = 'No file sent.';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors['main'][] = 'Exceeded filesize limit.';
                break;
            default:
                $errors['main'][] = 'Unknown error.';
        }

        // You should also check filesize here.
        if ($_FILES['studentFile']['size'] > 50000000) {
            $errors['main'][] = 'Exceeded filesize limit.';
        }

        $chapter_id = self::$wpdb->get_var(sprintf("SELECT id FROM %schapters WHERE id = '%s'",
            self::$wpdb->prefix,
            $_POST['chapter_id']
        ));


        if(null === $chapter_id) {
            $errors['main'][] = 'That chapter does not exist.';
        }


        if(count($errors) > 0) {

            /*$result = [];
            $result['type'] = "error";
            echo json_encode($result);*/

            exit;
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['studentFile']['tmp_name']);

        $rows = $spreadsheet->getActiveSheet()->toArray();
        $columnNames = array_shift($rows);

        $student_import_file_mapping = get_option('student_import_file_mapping');


        $result = [];
        $result['type'] = "success";
        $result['columns'] = $columnNames;
        $result['student_import_file_mapping'] = $student_import_file_mapping;
        echo json_encode($result);
        exit;

    }



    private static function validateImportFile() {

    }



}