<?php

namespace CRMConnector;


use CRMConnector\Api\Exceptions\MessageParser;
use CRMConnector\Api\MailChimp;
use CRMConnector\Api\Models\CampaignDefaults;
use CRMConnector\Api\Models\Contact;
use CRMConnector\Api\Models\MailChimp\Creds;
use CRMConnector\Api\Models\MailChimp\GetListsResponse;
use CRMConnector\Api\Models\MailChimpList;
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
        global $wpdb;

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

                $creds = new Creds;
                $creds->api_key = get_option('crmc_mailchimp_api_key', null);
                $creds->username = get_option('crmc_mailchimp_username', null);

                $mailchimp_api = MailChimp::instance();
                $response = null;
                try
                {
                    $response = $mailchimp_api->get_lists($creds);
                }
                catch(\Exception $exception)
                {
                    $name = "Josh";
                }

                if($response)
                {
                    $body = (string) $response->getBody();
                    $lists = json_decode($body, true);
                    $response = new GetListsResponse;
                    $response->fromArray($lists);
                    $lists_response = $response->toArray();
                }


                $tab_contents = $this->render('admin/tabs/invitation_settings', array(
                    'lists_response' => $lists_response
                ));
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
        add_action("wp_ajax_crmc_set_data_type", array( $this, 'set_data_type_action'));
        add_action("wp_ajax_crmc_get_column_names", array( $this, 'get_column_names_action'));
        add_action('wp_ajax_crmc_import_contacts', array( $this, 'import_contacts_action'));
    }

    public function add_admin_post_handlers()
    {
        add_action('admin_post_crmc_add_hubspot_api_key', array( $this, 'add_api_key_action'));
        add_action('admin_post_crmc_add_algolia_api_keys', array( $this, 'add_algolia_api_keys_action'));
        add_action('admin_post_crmc_add_mailchimp_api_key', array( $this, 'add_mailchimp_api_key_action'));
        add_action('admin_post_crmc_add_chapter', array( $this, 'add_chapter_action'));
        add_action('admin_post_crmc_sync_mapping_to_hubspot', array($this, 'sync_mapping_to_hubspot'));
        add_action('admin_post_crmc_rollback_import', array($this, 'crmc_rollback_import'));
        add_action('admin_post_crmc_add_list', array($this, 'add_list'));
        add_action('admin_post_crmc_remove_list', array($this, 'remove_list'));
        add_action('admin_post_crmc_edit_list', array($this, 'edit_list'));

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

    public function add_mailchimp_api_key_action()
    {

        deleteTransients();
        $errors = [];

        if( !isset( $_POST['crmc_add_mailchimp_api_key_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_mailchimp_api_key_nonce'], 'crmc_add_mailchimp_api_key_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['crmc_mailchimp_api_key']) || !isset($_POST['crmc_mailchimp_username']))
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(empty($_POST['crmc_mailchimp_api_key']))
        {
            $errors['crmc_mailchimp_api_key'][] = 'You must enter in an API Key';
        }

        if(empty($_POST['crmc_mailchimp_username']))
        {
            $errors['crmc_mailchimp_username'][] = 'You must enter your MailChimp Username';
        }

        if(count($errors) > 0)
        {
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "mailchimp");
            exit;
        }


        $crmc_mailchimp_api_key = sanitize_text_field( $_POST['crmc_mailchimp_api_key']);
        $crmc_mailchimp_username = sanitize_text_field( $_POST['crmc_mailchimp_username']);

        $mailchimp_api = MailChimp::Instance();
        $creds = new Creds;
        $creds->api_key = $crmc_mailchimp_api_key;
        $creds->username = $crmc_mailchimp_username;

        try
        {
            $mailchimp_api->ping($creds);
        }
        catch(\Exception $exception)
        {
            $errors['main'][] = MessageParser::ParseMailChimpMessage($exception->getMessage());
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "mailchimp");
            exit;
        }

        update_option('crmc_mailchimp_api_key', $crmc_mailchimp_api_key);
        update_option('crmc_mailchimp_username', $crmc_mailchimp_username);

        set_transient( 'successMessage', 'MailChimp API credentials successfully saved.', 10 );
        redirectToPage(array('page' => 'crmc_settings','tab' => 'advanced_settings'), "mailchimp");
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
            'chapter_name' => $chapter_name,
            'created_at'    => (new \DateTime())->format("Y-m-d H:i:s")
        ));

        set_transient( 'successMessage', 'Chapter successfully created.', 10 );
        redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "create_chapter");
        exit;
    }

    /**
     * Creates empty group in the database
     */
    public function create_group_action()
    {
        global $wpdb;
        $groups_table = $wpdb->prefix.'groups';
        $count = (int) $wpdb->get_var(sprintf("SELECT count(id) FROM $groups_table"));
        $wpdb->insert( $groups_table, array(
            'name' => "group_$count",
            'created_at'    => (new \DateTime())->format("Y-m-d H:i:s")
        ));
        $group_id = $wpdb->insert_id;

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
        $property_table = $wpdb->prefix.'properties';

        $group = $_POST['group'];

        $count = (int) $wpdb->get_var(sprintf("SELECT count(id) FROM $property_table WHERE group_id= %s",$group));

        $wpdb->insert( $property_table, array(
            'group_id' => $group,
            'name'  => "group_{$group}_property_{$count}",
            'created_at'    => (new \DateTime())->format("Y-m-d H:i:s")
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
        $display_name = sanitize_text_field($_POST['group_name']);

        $wpdb->query(sprintf("UPDATE $groups_table set displayName='%s' WHERE id= %s", $display_name, $group_id));


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
        $label = sanitize_text_field($_POST['label']);
        $property_id = sanitize_key($_POST['property_id']);

        $wpdb->query($wpdb->prepare("UPDATE $properties_table set label= %s WHERE group_id= %s and id= %s", $label, $group_id, $property_id));


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

        $wpdb->query($wpdb->prepare("UPDATE $properties_table set description= %s WHERE group_id= %s and id= %s",$property_value, $group_id, $property_id));

        $result = [];
        $result['type'] = "success";
        echo json_encode($result);
        exit;
    }

    public function set_data_type_action()
    {
        global $wpdb;
        $properties_table = $wpdb->prefix.'properties';
        $group_id = sanitize_key($_POST['group']);
        $type = sanitize_text_field($_POST['type']);
        $property_id = sanitize_key($_POST['property_id']);

        $wpdb->query($wpdb->prepare("UPDATE $properties_table set type= %s WHERE group_id= %s and id= %s",$type, $group_id, $property_id));

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

        if(empty($_POST['database_column_name']))
        {
            $errors[] = 'Please map at least one database column an excel spreadsheet column';
        }

        // check to make sure there are no duplicate mapped database column names
        $dupe_array = array();
        foreach ($_POST['database_column_name'] as $val) {

            if (++$dupe_array[$val] > 1) {
                $errors[] = 'You cannot use the same database column name twice!';
                break;
            }
        }

        // check to make sure that a student email has been added to the import
        if(!in_array('Personal Email', $_POST['database_column_name']))
            $errors[] = 'You must map an email address!';


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

        $results = $wpdb->get_results(sprintf("SELECT id, chapter_name FROM %schapters WHERE id = '%s'",
            $wpdb->prefix,
            $_POST['chapter_id']
        ));

        if(empty($results))
        {
            $errors[] = 'That chapter does not exist.';
        }

        if(count($errors) > 0)
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        $chapter_id = $results[0]->id;
        $chapter_name = $results[0]->chapter_name;

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
        $selected_database_columns = json_decode($_POST['selected_database_columns']);

        array_shift($rows);
        foreach($rows as $row)
        {
            $record = [];
            $record['chapter_name'] = $chapter_name;
            $record['chapter_id'] = $chapter_id;
            foreach($selected_database_columns as $key => $selectedDatabaseColumn)
            {
                $record[$_POST['database_column_name'][$key]] = $row[$selectedDatabaseColumn];
            }
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

        $result = $wpdb->query(sprintf("INSERT INTO %s%s (algolia_object_ids, chapter_id, created_at) VALUES ('%s', '%s', CURRENT_TIMESTAMP)",
            $wpdb->prefix,
            'imports',
            serialize($response['objectIDs']),
            $chapter_id)
        );


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
        $result['notices'] = ["Column Names Loaded Successfully", "Select as many or as few of the columns you would like to import."];
        $result['columns'] = $columnNames;
        $result['student_import_file_mapping'] = $student_import_file_mapping;
        echo json_encode($result);
        exit;
    }

    public function sync_mapping_to_hubspot()
    {
        $errors = [];
        global $wpdb;
        $results = $wpdb->get_results(sprintf("
              SELECT %s%s.id as group_id, %s%s.name as group_name, %s%s.displayName as 
              group_display_name, %s%s.id as property_id, %s%s.name as property_name, %s%s.label as property_label, %s%s.description as property_description, 
              %s%s.type as property_type FROM %s%s LEFT JOIN %s%s ON %s%s.id = %s%s.group_id",
            $wpdb->prefix,
            'groups',
            $wpdb->prefix,
            'groups',
            $wpdb->prefix,
            'groups',
            $wpdb->prefix,
            'properties',
            $wpdb->prefix,
            'properties',
            $wpdb->prefix,
            'properties',
            $wpdb->prefix,
            'properties',
            $wpdb->prefix,
            'properties',
            $wpdb->prefix,
            'groups',
            $wpdb->prefix,
            'properties',
            $wpdb->prefix,
            'groups',
            $wpdb->prefix,
            'properties'
            ));


        // save the mapping to HubSpot for each Chapter Associated with the account
        $hubspot = HubSpot::Instance(get_option('crmc_hubspot_api_key'));
        $groups_affected = 0;
        $properties_affected = 0;
        foreach($results as $result) {
            try
            {
                $response = $hubspot->getCompanyGroups();
                $groups = json_decode((string) $response->getBody());

                $group_matches = array_filter($groups, function($group) use($result){
                    return $group->name === $result->group_name;
                });

                // if the result name or display name are missing
                // then skip to the next result
                if(!$result->group_name || !$result->group_display_name)
                {
                    continue;
                }

                // the group already exists so update instead of insert
                if(count($group_matches) !== 0)
                {
                    $response = $hubspot->updateCompanyGroup([
                        'displayName' => $result->group_display_name
                    ], $result->group_name);
                    $groups_affected++;
                }
                else
                {
                    $response = $hubspot->createCompanyGroup([
                        'name'  =>  $result->group_name,
                        'displayName'   => $result->group_display_name
                    ]);
                    $groups_affected++;
                }

                if(!$result->property_label)
                {
                    continue;
                }

                $response = $hubspot->getAllCompanyProperties();
                $properties = json_decode((string) $response->getBody());
                $property_matches = array_filter($properties, function($property) use($result){
                    return $property->name === $result->property_name;
                });

                if(count($property_matches) !== 0)
                {
                    $response = $hubspot->updateCompanyProperty([
                        'name'  =>  $result->property_name,
                        'label' => $result->property_label,
                        'type'  => $result->property_type,
                        'groupName' => $result->group_name,
                        'description'   => $result->property_description
                    ], $result->property_name);
                    $properties_affected++;
                }
                else
                {
                    $response = $hubspot->createCompanyProperty([
                        'name'  =>  $result->property_name,
                        'label' => $result->property_label,
                        'description'   => $result->property_description,
                        'groupName' => $result->group_name,
                        'type'  => $result->property_type
                    ]);
                    $properties_affected++;
                }

            }
            catch(\Exception $exception)
            {
                $errors['main'][] = $this->exceptionMessageParser($exception->getMessage());
                set_transient( 'errors', $errors, 10 );
                redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "chapter_mapping");
                exit;
            }
        }

        if($properties_affected === 0 && $groups_affected === 0)
        {
            $errors['main'][] = 'You must add some Group or Property data before you sync!';
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "chapter_mapping");
            exit;
        }

        set_transient( 'successMessage', 'Chapter mapping successfully synced to HubSpot', 10 );
        redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "chapter_mapping");
        exit;


    }

    public function crmc_rollback_import()
    {
        deleteTransients();
        $errors = [];
        global $wpdb;

        if( !isset( $_POST['crmc_rollback_import_nonce'] ) || !wp_verify_nonce( $_POST['crmc_rollback_import_nonce'], 'crmc_rollback_import_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['importId']) || empty($_POST['importId']))
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(count($errors) > 0)
        {
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "all_chapters");
            exit;
        }

        $importId = sanitize_key($_POST['importId']);

        $result = $wpdb->get_var(sprintf("SELECT algolia_object_ids FROM %s%s WHERE id = %s", $wpdb->prefix, 'imports', $importId));

        if(!$result)
        {
            $errors['main'][] = 'There was an error trying to rollback that import';
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "all_chapters");
            exit;
        }

        $object_ids = unserialize($result);

        $algoliaAdapter = new AlgoliaAdapter(get_option('crmc_algolia_application_id'), get_option('crmc_algolia_api_key'), get_option('crmc_algolia_index'));

        if(!$algoliaAdapter->deleteObjects($object_ids))
        {
            $errors['main'][] = 'There was an error trying to rollback that import';
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "all_chapters");
            exit;
        }

        $wpdb->query(sprintf("DELETE FROM %s%s WHERE id = %s", $wpdb->prefix, 'imports', $importId));

        set_transient( 'successMessage', 'Rollack Successful', 10 );
        redirectToPage(array('page' => 'crmc_settings','tab' => 'chapters'), "all_chapters");
        exit;

    }

    /**
     * Try to make sense of all the log crazy error message sent back from exceptions
     *
     * @param $message
     * @return mixed
     */
    private function exceptionMessageParser($message)
    {
        $message_map = [
            'Property must have type set",'     =>  'Whoops! You forgot to set a Data Type one one or more of your Properties!',
            'API Key Invalid'                   =>  'Whoopls, You need to enter in a valid API Key!',
        ];

        foreach($message_map as $key => $value)
        {
            if (strpos($message, $key) !== false)
            {
                return $message_map[$key];
            }
        }

        return $message;
    }

    /**
     * @return array
     */
    private function json_response()
    {
        return [
            'type' => "success",
            'errors' => [],
            'notices' => [],
        ];
    }

    public function add_list()
    {
        deleteTransients();
        $errors = [];

        if( !isset( $_POST['crmc_add_list_nonce'] ) || !wp_verify_nonce( $_POST['crmc_add_list_nonce'], 'crmc_add_list_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';

            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings'), "create_list");
            exit;
        }

        $mailchimp_list = new MailChimpList();
        $mailchimp_list->contact = new Contact();
        $mailchimp_list->campaign_defaults = new CampaignDefaults();
        $mailchimp_list->handle_request($_REQUEST);

        if($mailchimp_list->is_valid(true))
        {
            $creds = new Creds;
            $creds->api_key = get_option('crmc_mailchimp_api_key', null);
            $creds->username = get_option('crmc_mailchimp_username', null);

            $mailchimp_api = MailChimp::instance();
            try
            {
                $mailchimp_api->create_list($creds, $mailchimp_list);
            }
            catch(\Exception $exception)
            {
                $errors['main'][] = MessageParser::ParseMailChimpMessage($exception->getMessage());
                set_transient( 'errors', $errors, 10 );
                $field_params = $mailchimp_list->toArray();
                redirectToPage((array('page' => 'crmc_settings','tab' => 'invitation_settings') + $field_params), "create_list");
                exit;
            }

            set_transient( 'successMessage', 'List Successfully Created', 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings'), "create_list");
            exit;
        }

        set_transient( 'errors', $mailchimp_list->errors(), 10 );
        $field_params = $mailchimp_list->toArray();
        redirectToPage((array('page' => 'crmc_settings','tab' => 'invitation_settings') + $field_params), "create_list");
        exit;
    }

    public function remove_list()
    {
        deleteTransients();
        $errors = [];

        if( !isset( $_POST['crmc_remove_list_nonce'] ) || !wp_verify_nonce( $_POST['crmc_remove_list_nonce'], 'crmc_remove_list_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(!isset($_POST['list_id']) || empty($_POST['list_id']))
        {
            $errors['main'][] = "Problem removing the List";
        }

        if(count($errors) > 0)
        {
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings'), "lists");
            exit;
        }


        $creds = new Creds;
        $creds->api_key = get_option('crmc_mailchimp_api_key', null);
        $creds->username = get_option('crmc_mailchimp_username', null);

        $mailchimp_api = MailChimp::instance();
        try
        {
            $response = $mailchimp_api->remove_list($creds, $_POST['list_id']);
        }
        catch(\Exception $exception)
        {
            $errors['main'][] = MessageParser::ParseMailChimpMessage($exception->getMessage());
            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings'), "lists");
            exit;
        }

        set_transient( 'successMessage', 'List Successfully Removed', 10 );
        redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings'), "lists");
        exit;
    }

    public function edit_list()
    {
        deleteTransients();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if( !isset( $_GET['crmc_edit_list_nonce'] ) || !wp_verify_nonce( $_GET['crmc_edit_list_nonce'], 'crmc_edit_list_nonce') )
            {
                $errors['main'][] = 'Invalid form submission.';
            }

            if(!isset($_GET['list_id']) || empty($_GET['list_id']))
            {
                $errors['main'][] = "Problem trying to edit the List";
            }

            if(count($errors) > 0)
            {
                set_transient( 'errors', $errors, 10 );
                redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings'), "lists");
                exit;
            }

            redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings', 'list_id' => $_GET['list_id']), "edit_list");
            exit;

        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if( !isset( $_POST['crmc_edit_list_nonce'] ) || !wp_verify_nonce( $_POST['crmc_edit_list_nonce'], 'crmc_edit_list_nonce') )
            {
                $errors['main'][] = 'Invalid form submission.';

                set_transient( 'errors', $errors, 10 );
                redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings', 'list_id' => $_POST['list_id']), "edit_list");
                exit;
            }

            $mailchimp_list = new MailChimpList();
            $mailchimp_list->contact = new Contact();
            $mailchimp_list->campaign_defaults = new CampaignDefaults();
            $mailchimp_list->handle_request($_REQUEST);

            if($mailchimp_list->is_valid(true))
            {
                $creds = new Creds;
                $creds->api_key = get_option('crmc_mailchimp_api_key', null);
                $creds->username = get_option('crmc_mailchimp_username', null);

                $mailchimp_api = MailChimp::instance();
                try
                {
                    $mailchimp_api->edit_list($creds, $mailchimp_list, $_POST['list_id']);
                }
                catch(\Exception $exception)
                {
                    $errors['main'][] = MessageParser::ParseMailChimpMessage($exception->getMessage());
                    set_transient( 'errors', $errors, 10 );
                    $field_params = $mailchimp_list->toArray();
                    redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings', 'list_id' => $_POST['list_id']), "edit_list");
                    exit;
                }

                set_transient( 'successMessage', 'List Successfully Updated!', 10 );
                redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings', 'list_id' => $_POST['list_id']), "edit_list");
                exit;
            }

            set_transient( 'errors', $mailchimp_list->errors(), 10 );
            $field_params = $mailchimp_list->toArray();
            redirectToPage((array('page' => 'crmc_settings','tab' => 'invitation_settings', 'list_id' => $_POST['list_id']) + $field_params), "edit_list");
            exit;

        }
    }
}