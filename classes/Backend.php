<?php

namespace CRMConnector;


use CRMConnector\Concerns\Renderable;
use finfo;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Api\GuzzleFactory;
use CRMConnector\Api\HubSpot;

class Backend
{
    use Renderable;

    private $data;

    public function __construct()
    {
        $this->data['plugin_url'] = plugins_url('/', dirname(__FILE__));

        $this->data['plugin_path'] = dirname(dirname(__FILE__)) . '/';

        $this->data['admin_url'] = admin_url();;

        /**************************************************************
        Backend actions and hoooks
        **************************************************************/
        add_action('admin_enqueue_scripts', array($this, 'add_admin_scripts'));

        add_action('admin_menu', array($this, 'crmc_connector_menu'));

        add_action('admin_init', array($this, 'add_admin_ajax_handlers'));

        add_action('admin_init', array($this, 'add_admin_post_handlers'));
    }

    public function add_admin_scripts()
    {
        global $CRMConnectorPlugin;

        wp_register_script( 'instant-search.js', 'https://cdn.jsdelivr.net/npm/instantsearch.js@2.4/dist/instantsearch.min.js');
        wp_enqueue_script('instant-search.js');

        wp_register_script( 'bootstrap.js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
        wp_enqueue_script('bootstrap.js');

        wp_register_script('crm-connector.js', plugin_dir_url( __FILE__ ) . '../assets/crm-connector.js', array('jquery'), $CRMConnectorPlugin->version);
        wp_enqueue_script('crm-connector.js');

        wp_register_script('spin.js', 'https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js');
        wp_enqueue_script('spin.js');

        wp_register_style('instant-search.css', 'https://cdn.jsdelivr.net/npm/instantsearch.js@2.4/dist/instantsearch.min.css');
        wp_enqueue_style('instant-search.css');

        /*wp_register_style('bootstrap-local', plugin_dir_url( __FILE__ ) . '../assets/bootstrap.min.css', array(), $CRMConnectorPlugin->version);*/
        wp_register_style('bootstrap.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        wp_enqueue_style('bootstrap.css');

        wp_register_style( 'crm-connector.css', plugin_dir_url( __FILE__ ) . '../assets/crm-connector.css', array(), $CRMConnectorPlugin->version );
        wp_enqueue_style('crm-connector.css');

        wp_register_style( 'font-awesome.css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), $CRMConnectorPlugin->version);
        wp_enqueue_style('font-awesome.css');

    }

    public function crmc_connector_menu()
    {
        add_menu_page(__( 'CRM Connector', 'textdomain' ), 'CRM Connector', 'manage_options', 'crmc_settings', array($this, 'crmc_settings'), '', 1);

        global $menu;
        $menu[1][2] = "admin.php?page=crmc_settings&tab=contacts";
    }

    public function crmc_settings()
    {
        $current_tab = array_filter($this->getSettingsTabs(), function($tab) {
           return isset($_GET['tab']) && $_GET['tab'] == $tab['url'];
        });

        $current_tab = array_shift($current_tab);

        switch($current_tab['url'])
        {
            case "":

                break;

            case "contacts":
                    $tab_contents = $this->render('admin/tabs/contacts');
                break;

            case "chapters":
                $tab_contents = $this->render('admin/tabs/chapters');
                break;

            case "advanced_settings":
                $tab_contents = $this->render('admin/tabs/advanced_settings');
                break;

            case "invitation_settings":
                $tab_contents = $this->render('admin/tabs/invitation_settings');
                break;
        }

        echo $this->render('admin/crmc_settings', [
           'tab_contents' => isset($tab_contents) ? $tab_contents : ''
        ]);

    }

    public function show_admin_notices()
    {
        $message = null;
        if(isset($_GET['message']))
        {
            switch($_GET['message'])
            {
                case "algolia_success":
                    $message = "Successfully added Algolia Search Keys";
            }
        }
        if($message)
            echo "<div class='alert alert-success' role='alert'>$message</div>";
    }

    public function add_admin_ajax_handlers()
    {
        add_action("wp_ajax_crmc_create_group", array( $this, 'create_group_action'));
        add_action("wp_ajax_crmc_create_property", array( $this, 'create_property_action'));
        add_action("wp_ajax_crmc_set_group_name", array( $this, 'create_group_name_action'));
        add_action("wp_ajax_crmc_set_property_name", array( $this, 'create_property_name_action'));
        add_action("wp_ajax_crmc_set_property_value", array( $this, 'create_property_value_action'));
        add_action("wp_ajax_crmc_get_column_names", array( $this, 'get_column_names_action'));
        add_action('wp_ajax_crmc_import_contacts', array( $this, 'import_contacts_action'));
    }

    public function add_admin_post_handlers()
    {
        add_action('admin_post_crmc_add_hubspot_api_key', array( $this, 'add_api_key_action'));
        add_action('admin_post_crmc_add_algolia_api_keys', array( $this, 'add_algolia_api_keys_action'));
        add_action('admin_post_crmc_add_chapter', array( $this, 'add_chapter_action'));
        add_action('admin_post_crmc_add_chapter_mapping', array( $this, 'add_chapter_mapping_action'));
        add_action('admin_post_crmc_sync_mapping_to_hubspot', array($this, 'sync_mapping_to_hubspot'));
    }

    private function getSettingsTabs() {
        return [
            [
                'text'  =>  'Contacts',
                'url'   =>  'contacts',
            ],
            [
                'text'  =>  'Chapters',
                'url'   =>  'chapters',
            ],
            [
                'text'  =>  'Advanced Settings',
                'url'   =>  'advanced_settings',
            ],
            [
                'text'  =>  'Invitation Settings',
                'url'   =>  'invitation_settings',
            ]
        ];
    }

    public function add_api_key_action()
    {
        deleteTransients();
        $errors = [];

        if( !isset( $_POST['crmc_add_hubspot_api_key_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_hubspot_api_key_nonce'], 'crmc_add_hubspot_api_key_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_hubspot_api_key']))
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_hubspot_api_key']))
        {
            $errors['crmc_hubspot_api_key'][] = 'You must enter in an API Key';
        }

        if(count($errors) > 0)
        {
            set_transient( 'errors', $errors, 1 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "hubspot");
            exit;
        }


        $crmc_hubspot_api_key = sanitize_text_field( $_POST['crmc_hubspot_api_key']);
        $hubspot_api = HubSpot::Instance($crmc_hubspot_api_key);
        $response = null;
        try
        {
            $response = $hubspot_api->ping();
        }
        catch(\Exception $exception)
        {
            if(stripos($exception->getMessage(), '401') !== false)
            {
                $errors['crmc_hubspot_api_key'][] = 'You must enter in a valid API Key.';
            }
            else
            {
                $errors['main'][] = $exception->getMessage();
            }

            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "hubspot");
            exit;
        }

        if($response && $response->getStatusCode() !== 200)
        {
            $errors['crmc_hubspot_api_key'][] = 'You must enter in a valid API Key.';
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "hubspot");
            exit;
        }

        update_option( 'crmc_hubspot_api_key', $crmc_hubspot_api_key);

        set_transient( 'successMessage', 'HubSpot API credentials successfully saved.', 1 );
        redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "hubspot");
        exit;
    }

    public function add_algolia_api_keys_action()
    {
        deleteTransients();
        $errors = [];

        if( !isset( $_POST['crmc_add_algolia_api_keys_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_algolia_api_keys_nonce'], 'crmc_add_algolia_api_keys_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_algolia_application_id']) || !isset($_POST['crmc_algolia_api_key']) || !isset($_POST['crmc_algolia_index']) || !isset($_POST['crmc_algolia_search_only_api_key']))
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_algolia_application_id']))
        {
            $errors['crmc_algolia_application_id'][] = 'You must enter in an Application ID.';
        }

        if(empty($_POST['crmc_algolia_api_key']))
        {
            $errors['crmc_algolia_api_key'][] = 'You must enter in an API Key.';
        }

        if(empty($_POST['crmc_algolia_index']))
        {
            $errors['crmc_algolia_index'][] = 'You must enter in index name.';
        }

        if(empty($_POST['crmc_algolia_search_only_api_key']))
        {
            $errors['crmc_algolia_search_only_api_key'][] = 'You must enter in a search only api key.';
        }

        $algoliaAdapter = null;
        try
        {
            $algoliaAdapter = new AlgoliaAdapter($_POST['crmc_algolia_application_id'], $_POST['crmc_algolia_api_key'], $_POST['crmc_algolia_index']);
        }
        catch(\Exception $exception)
        {
            $errors['main'][] = $exception->getMessage();
        }

        // This is used to validate the API keys
        if($algoliaAdapter)
        {
            try
            {
                $response = $algoliaAdapter->listKeys();
            }
            catch(\Exception $exception)
            {
                $errors['main'][] = $exception->getMessage();
            }
        }

        if(!empty($response))
        {
            if(!in_array($_POST['crmc_algolia_search_only_api_key'], array_column($response['keys'], 'value')))
            {
                $errors['crmc_algolia_search_only_api_key'][] = 'You must enter in a valid search only api key.';
            }
        }

        if(count($errors) > 0)
        {
            set_transient( 'errors', $errors, 1 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "algolia");
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
        redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "algolia");
        exit;
    }


    public function add_chapter_action()
    {
        deleteTransients();
        $errors = [];
        global $wpdb;

        if( !isset( $_POST['crmc_add_chapter_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_chapter_nonce'], 'crmc_add_chapter_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_chapter_name']))
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_chapter_name']))
        {
            $errors['crmc_chapter_name'][] = 'You must enter a Chapter Name.';
        }

        if(false === get_option('crmc_hubspot_api_key', false))
        {
            $errors['main'][] = 'You must enter in a HubSpot API Key before you can create chapters.';
        }

        if(count($errors) > 0)
        {
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "create_chapter");
            exit;
        }

        $chapter_name = sanitize_text_field( $_POST['crmc_chapter_name']);

        $hubspot = HubSpot::Instance(get_option('crmc_hubspot_api_key'));
        $payload = [
            "properties"    =>  [
                ["name"  => "name", "value" => $chapter_name],
                ["name"  => "description", "value" => ""]
            ]
        ];
        try
        {
            $hubspot->createCompany($payload);
        }
        catch(\Exception $exception)
        {
            if(stripos($exception->getMessage(), '401') !== false)
            {
                $errors['main'][] = 'You must enter in a valid hubspot API Key.';
            }
            else
            {
                $errors['main'][] = $exception->getMessage();
            }

            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "create_chapter");
            exit;
        }



        $tablename = $wpdb->prefix.'chapters';
        $wpdb->insert( $tablename, array(
            'chapter_name' => $chapter_name
        ));

        set_transient( 'successMessage', 'Chapter successfully created.', 10 );
        redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "create_chapter");
        exit;
    }


    public function add_chapter_mapping_action()
    {
        deleteTransients();
        $errors = [];

        if( !isset( $_POST['crmc_add_chapter_mapping_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_chapter_mapping_nonce'], 'crmc_add_chapter_mapping_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['groups']))
        {
            $errors['main'][] = 'You must add at least 1 Property Group before you can create!';
        }

        $groups_table = self::$wpdb->prefix.'groups';
        $properties_table = self::$wpdb->prefix.'properties';
        $groups = $_POST['groups'];
        foreach($groups as $key => $value)
        {
            if(empty($value))
            {
                continue;
            }

            if(empty($value['group']))
            {
                $errors["groups[$key][group]"][] = 'You must enter a Chapter Name.';
                continue;
            }

            self::$wpdb->insert( $groups_table, array(
                'group_name' => $value['group']
            ));
            $last_inserted_group_id = self::$wpdb->insert_id;

            if(empty($value['properties']) || !$last_inserted_group_id)
            {
                continue;
            }

            foreach($value['properties'] as $property)
            {
                if(empty($property['property_name']))
                {
                    continue;
                }
                self::$wpdb->insert( $properties_table, array(
                    'group_id' => $last_inserted_group_id,
                    'property_name' => $property['property_name'],
                    'property_value' => $property['property_value'],
                ));
            }
        }



        if(count($errors) > 0)
        {
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
    public function create_group_action()
    {
        global $wpdb;
        $groups_table = $wpdb->prefix.'groups';
        $wpdb->insert( $groups_table, array(
            'group_name' => ""
        ));
        $group_id = $wpdb->insert_id;

        if($group_id)
        {
            $client = GuzzleFactory::get_mailchimp_instance();
        }

        $result = [];
        $result['type'] = "success";
        $result['group_id'] = $group_id;
        echo json_encode($result);
        exit;
    }


    /**
     * Creates empty property in the database
     */
    public function create_property_action()
    {
        global $wpdb;
        $groups_table = $wpdb->prefix.'properties';

        $group = $_POST['group'];

        $wpdb->insert( $groups_table, array(
            'group_id' => $group,
        ));

        $property_id = $wpdb->insert_id;

        $result = [];
        $result['type'] = "success";
        $result['property_id'] = $property_id;
        echo json_encode($result);
        exit;
    }


    /**
     * Adds/modifies an empty group name that has been created
     */
    public function create_group_name_action()
    {
        global $wpdb;
        $groups_table = $wpdb->prefix.'groups';
        $group_id = sanitize_key($_POST['group']);
        $group_name = sanitize_text_field($_POST['group_name']);

        $wpdb->query($wpdb->prepare("UPDATE $groups_table set group_name= %s WHERE id= %s",$group_name, $group_id));

        $result = [];
        $result['type'] = "success";
        echo json_encode($result);
        exit;

    }

    /**
     * Adds/modifies an empty property name that has been created
     */
    public function create_property_name_action() {

        global $wpdb;
        $properties_table = $wpdb->prefix.'properties';
        $group_id = sanitize_key($_POST['group']);
        $property_name = sanitize_text_field($_POST['property_name']);
        $property_id = sanitize_key($_POST['property_id']);

        $wpdb->query($wpdb->prepare("UPDATE $properties_table set property_name= %s WHERE group_id= %s and id= %s",$property_name, $group_id, $property_id));

        $result = [];
        $result['type'] = "success";
        echo json_encode($result);
        exit;
    }

    /**
     * Adds/modifies an empty property value that has been created
     */
    public function create_property_value_action() {

        global $wpdb;
        $properties_table = $wpdb->prefix.'properties';
        $group_id = sanitize_key($_POST['group']);
        $property_value = sanitize_text_field($_POST['property_value']);
        $property_id = sanitize_key($_POST['property_id']);

        $wpdb->query($wpdb->prepare("UPDATE $properties_table set property_value= %s WHERE group_id= %s and id= %s",$property_value, $group_id, $property_id));

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
    public function import_contacts_action() {

        global $wpdb;
        $errors = [];

        if(empty($_POST['chapter_id']) || !isset($_FILES['studentFile']))
        {
            $errors[] = 'Invalid form submission.';
        }

        if(empty($_FILES['studentFile']))
        {
            $errors[] = 'Please add an excel file to import';
        }

        // Check the file MIME Type
        $supported_file_extensions = array(
            'xsl' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if(false === $ext = array_search($finfo->file($_FILES['studentFile']['tmp_name']), $supported_file_extensions))
        {
            $errors[] = sprintf("Supported file types are (%s)", implode(", ", array_keys($supported_file_extensions)));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['studentFile']['error'])
        {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'No file sent.';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'Exceeded filesize limit.';
                break;
            default:
                $errors[] = 'Unknown error.';
        }

        // You should also check filesize here.
        if ($_FILES['studentFile']['size'] > 50000000)
        {
            $errors[] = 'Exceeded filesize limit.';
        }

        $chapter_id = $wpdb->get_var(sprintf("SELECT id FROM %schapters WHERE id = '%s'",
            $wpdb->prefix,
            $_POST['chapter_id']
        ));

        if(null === $chapter_id)
        {
            $errors[] = 'That chapter does not exist.';
        }

        if(count($errors) > 0)
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $errors['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        $upload_path = sprintf(
            '%s%s/%s',
            CRMCFunctions::plugin_dir() . '/',
            'imports',
            $chapter_id
        );

        if ( ! is_dir($upload_path))
        {
            mkdir($upload_path);
        }

        $upload_path = sprintf(
            '%s/%s.%s',
            $upload_path,
            date('m-d-Y_hia'),
            $ext
        );

        if(false === move_uploaded_file($_FILES['studentFile']['tmp_name'], $upload_path))
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $errors['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        //upload the data to algolia
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($upload_path);
        $rows = $spreadsheet->getActiveSheet()->toArray();
        $records = [];
        foreach($rows as $row)
        {
            $record = array_combine($_POST['database_column_name'], $row);
            $records[] = $record;
        }

        $algoliaAdapter = new AlgoliaAdapter(get_option('crmc_algolia_application_id'), get_option('crmc_algolia_api_key'), get_option('crmc_algolia_index'));
        try
        {
            $response = $algoliaAdapter->addObjects($records);
        }
        catch(\Exception $exception)
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = [$exception->getMessage()];
            echo json_encode($result);
            exit;
        }

        $result = $this->json_response();
        $result['notices'] = ["File Imported Successfully"];
        echo json_encode($result);
        exit;
    }


    /**
     * This function gets the column names for a given import file
     * This is more of a convenience call allowing the user to not have to
     * map all the column names manually
     */
    public function get_column_names_action()
    {
        global $wpdb;
        $errors = [];

        if(!isset($_FILES['studentFile']))
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_FILES['studentFile']))
        {
            $errors['studentFile'][] = 'Please add an excel file to import';
        }

        // Check the file MIME Type
        $supported_file_extensions = array(
            'xsl' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if(false === $ext = array_search($finfo->file($_FILES['studentFile']['tmp_name']), $supported_file_extensions))
        {
            $errors['studentFile'][] = sprintf("Supported file types are (%s)", implode(", ", array_keys($supported_file_extensions)));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['studentFile']['error'])
        {
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
        if ($_FILES['studentFile']['size'] > 50000000)
        {
            $errors['main'][] = 'Exceeded filesize limit.';
        }

        $chapter_id = $wpdb->get_var(sprintf("SELECT id FROM %schapters WHERE id = '%s'",
            $wpdb->prefix,
            $_POST['chapter_id']
        ));

        if(null === $chapter_id)
        {
            $errors['main'][] = 'That chapter does not exist.';
        }

        if(count($errors) > 0)
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['studentFile']['tmp_name']);
        $rows = $spreadsheet->getActiveSheet()->toArray();
        $columnNames = array_shift($rows);
        $student_import_file_mapping = get_option('student_import_file_mapping');

        $result = $this->json_response();
        $result['notices'] = ["Column Names Loaded Successfully"];
        $result['columns'] = $columnNames;
        $result['student_import_file_mapping'] = $student_import_file_mapping;
        echo json_encode($result);
        exit;
    }

    public function sync_mapping_to_hubspot()
    {
        deleteTransients();
        $errors = [];
        global $wpdb;

        if( !isset( $_POST['crmc_sync_mapping_to_hubspot_nonce'] ) || !wp_verify_nonce( $_POST['crmc_sync_mapping_to_hubspot_nonce'], 'crmc_sync_mapping_to_hubspot_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }


        if(false === get_option('crmc_hubspot_api_key', false))
        {
            $errors['main'][] = 'You must enter in a HubSpot API Key before you can sync your mapping';
        }

        if(empty($_POST['groups']))
        {
            $errors['main'][] = 'You must create at least one group before you can sync your mapping';
        }

        foreach($_POST['groups'] as $group)
        {
            if(empty($group['group']))
            {
                $errors['main'][] = 'Each one of your groups must have a name before you can sync the chapter mapping';
                break;
            }

            if(!empty($group['properties']))
            {
                foreach($group["properties"] as $property)
                {
                    if(empty($property['property_name']) || empty($property['property_value']))
                    {
                        $errors['main'][] = 'Each one of your properties must have both a name and a value before you can sync the chapter mapping';
                        break;
                    }
                }
            }
        }

        if(count($errors) > 0)
        {
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "chapter_mapping");
            exit;
        }

        // save the mapping to HubSpot for each Chapter Associated with the account
        $hubspot = HubSpot::Instance(get_option('crmc_hubspot_api_key'));

        $payload = [
            'name' => "",
            'displayName' => ""
        ];

        $update_company_payload = [
            'displayName' => ""
        ];

        foreach($_POST['groups'] as $group)
        {
            $payload['name'] = str_replace(" ", "_", $group['group']);
            $payload['displayName'] = $group['group'];
            try
            {
                $response = $hubspot->getCompanyGroups();
                $groups = json_decode((string) $response->getBody());

                $matches = array_filter($groups, function($group) use($payload) {
                    return $group->name === $payload['name'];
                });

                if(!empty($matches))
                {
                    $response = $hubspot->updateCompanyGroup(['displayName' => $payload['displayName']], $payload['name']);
                }
                else
                {
                    $response = $hubspot->createCompanyGroup($payload);
                }
            }
            catch(\Exception $exception)
            {
                if($exception->getCode() === 409)
                {
                    $errors['main'][] = "Chapter with that name already exists";
                }
                set_transient( 'errors', $errors, 10 );
                redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "chapter_mapping");
                exit;
            }
                foreach($group["properties"] as $property)
                {
                    // if the property name exists then just udate and don't create
                    //$property['property_name']
                    //$property['property_value']
                }
            }



        set_transient( 'successMessage', 'Chapter mapping successfully synced to HubSpot', 10 );
        redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "chapter_mapping");
        exit;


    }

    /**
     * @return array
     */
    private function json_response() {
        return [
            'type' => "success",
            'errors' => [],
            'notices' => [],
        ];
    }

}