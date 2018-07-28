<?php

namespace CRMConnector;


use CRMConnector\Api\Exceptions\MessageParser;
use CRMConnector\Api\MailChimp;
use CRMConnector\Api\Models\CampaignDefaults;
use CRMConnector\Api\Models\Contact;
use CRMConnector\Api\Models\MailChimp\Creds;
use CRMConnector\Api\Models\MailChimp\GetListsResponse;
use CRMConnector\Api\Models\MailChimp\GetTemplatesResponse;
use CRMConnector\Api\Models\MailChimp\Template;
use CRMConnector\Api\Models\MailChimpList;
use CRMConnector\Concerns\Renderable;
use CRMConnector\Crons\Initializers\BatchContactImportCronInitializer;
use CRMConnector\Crons\Initializers\BatchSubscriptionCronInitializer;
use CRMConnector\Crons\Models\BatchContactImportCronModel;
use CRMConnector\Crons\Models\BatchSubscriptionCronModel;
use CRMConnector\Service\CustomPostType\CustomPostTypeCreator;
use finfo;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Api\GuzzleFactory;
use CRMConnector\Api\HubSpot;
use WP_Query;

/**
 * Class Backend
 * @package CRMConnector
 */
class Backend
{
    use Renderable;

    private $data;

    public function __construct()
    {
        $this->crmc_set_initial_data();
        CustomPostTypeCreator::create();



        /**************************************************************
        Backend actions and hoooks
        **************************************************************/

        add_action('admin_enqueue_scripts', array($this, 'add_admin_scripts'));

        add_action('admin_menu', array($this, 'crmc_connector_menu'));

        add_action('admin_init', array($this, 'add_admin_ajax_handlers'));

        add_action('admin_init', array($this, 'add_admin_post_handlers'));

        add_action('admin_footer', array($this, 'crmc_add_modals'));

        add_action('acf/input/admin_head', '\CRMConnector\Service\ACF\ACFHooksFilters::admin_head');

        add_action('admin_head','\CRMConnector\Service\WP\WPHooksFilters::admin_head');

        add_filter('wp_insert_post_data', '\CRMConnector\Service\WP\WPHooksFilters::insert_post_data', 10, 2);

        add_filter('gettext', '\CRMConnector\Service\WP\WPHooksFilters::gettext', 10, 4);

        add_filter('publish_lists', array($this, 'initialize_batch_contact_import'), 10, 2);

    }

    public function initialize_batch_contact_import($post_id, $post)
    {




    }

    /**
     * Setup the data property with a bunch of useful info being used all around the code
     */
    public function crmc_set_initial_data()
    {
        $this->data['plugin_url'] = plugins_url('/', dirname(__FILE__));

        $this->data['plugin_path'] = dirname(dirname(__FILE__)) . '/';

        $this->data['admin_url'] = admin_url();


       /* global $my_admin_page;
        global $post;
        $screen = get_current_screen();

        if ( is_admin() && ($screen->id == 'chapters') ) {
            $this->data['chapter_id'] = $post->ID;;
        }*/
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

    public function crmc_add_modals()
    {
       include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/modals/_import_modal.php');
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

                $list_response = $mailchimp_api->get_lists($creds, $_REQUEST);
                $get_lists_response = new GetListsResponse;
                $get_lists_response->handleResponse($list_response);

                $template_response = $mailchimp_api->get_templates($creds, $_REQUEST);
                $get_templates_response = new GetTemplatesResponse;
                $get_templates_response->handleResponse($template_response);

                $tab_contents = $this->render('admin/tabs/invitation_settings', array(
                    'lists_response' => $get_lists_response->toArray(),
                    'templates_response'    => $get_templates_response->toArray()
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
        add_action('wp_ajax_crmc_create_custom_export', array( $this, 'create_custom_export_action'));
        add_action('wp_ajax_crmc_batch_subscribe_contacts', array( $this, 'batch_subscribe_contacts_action'));
        add_action('wp_ajax_crmc_batch_unsubscribe_contacts', array( $this, 'batch_unsubscribe_contacts_action'));
        add_action('wp_ajax_crmc_get_edit_template_form', array( $this, 'get_edit_template_form_action'));
        add_action('wp_ajax_crmc_post_edit_template_form', array( $this, 'post_edit_template_form_action'));
        add_action('wp_ajax_crmc_delete_template', array( $this, 'delete_template_action'));


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
        add_action('admin_post_crmc_create_template', array($this, 'create_template'));
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

        $model = new BatchContactImportCronModel();
        $model->handle_request($_REQUEST, $_FILES);

        if(!$model->is_valid())
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $model->getErrors();
            echo json_encode($result);
            exit;
        }

        if($model->move_file() && BatchContactImportCronInitializer::enqueue_cron($model))
        {
            $result = $this->json_response();
            $result['notices'] = ["File Successfully Added to Queue For Importing!"];
            echo json_encode($result);
            exit;
        }

        $result = $this->json_response();
        $result['type'] = "error";
        $errors['errors'] = $model->getErrors();
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
                $errors['main'][] = MessageParser::ParseMailChimpMessage($exception);
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

    public function create_custom_export_action()
    {
        deleteTransients();
        $errors = [];
        global $wpdb;

        if( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'crmc_create_custom_export_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(count($errors) > 0)
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        $search_query = $_POST['search_query'];

        $response = null;
        try
        {
            $algoliaAdapter = new AlgoliaAdapter(get_option('crmc_algolia_application_id'), get_option('crmc_algolia_api_key'), get_option('crmc_algolia_index'));
            $hits = [];
            foreach ($algoliaAdapter->browse($search_query) as $hit) {
                $hits[] = $hit;
            }
        }
        catch(\Exception $exception) {
            $errors['main'][] = $exception->getMessage();
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        if(!empty($hits))
        {
            $object_ids = array_column($hits, 'objectID');
                    $wpdb->query(sprintf("INSERT INTO %s%s (algolia_object_ids,created_at) VALUES ('%s', CURRENT_TIMESTAMP)",
                    $wpdb->prefix,
                    'exports',
                    serialize($object_ids))
            );

            $result = $this->json_response();
            $result['notices'] = ["Successfully Created Custom Export List"];
            echo json_encode($result);
            exit;
        }

        $errors['main'][] = "Woah! You Need to Import Some Students First!";
        $result = $this->json_response();
        $result['type'] = "error";
        $result['errors'] = $errors;
        echo json_encode($result);
        exit;
    }

    public function batch_subscribe_contacts_action()
    {
        deleteTransients();
        $errors = [];

        if( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'crmc_batch_subscribe_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        $model = new BatchSubscriptionCronModel();
        $model->handle_request($_REQUEST);

        if($model->is_valid() && BatchSubscriptionCronInitializer::enqueue_cron($model))
        {
            $result = $this->json_response();
            $result['notices'] = ($model->getType() === 'unsubscribed') ? ["Contacts Queued to be Unsubscribed"] : ["Contacts Queued to be Subscribed"];
            echo json_encode($result);
            exit;
        }

        $result = $this->json_response();
        $result['type'] = "error";
        $result['errors'] = $errors;
        echo json_encode($result);
        exit;
    }

    public function batch_unsubscribe_contacts_action()
    {
        deleteTransients();
        $errors = [];

        if( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'crmc_batch_unsubscribe_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        $model = new BatchSubscriptionCronModel();
        $model->handle_request($_REQUEST);

        if($model->is_valid() && BatchSubscriptionCronInitializer::enqueue_cron($model))
        {
            $result = $this->json_response();
            $result['notices'] = ["Contacts queued to be imported"];
            echo json_encode($result);
            exit;
        }

        $result = $this->json_response();
        $result['type'] = "error";
        $result['errors'] = $errors;
        echo json_encode($result);
        exit;
    }

    public function create_template()
    {
        deleteTransients();
        $errors = [];

        if( !isset( $_POST['crmc_create_template_nonce'] ) || !wp_verify_nonce( $_POST['crmc_create_template_nonce'], 'crmc_create_template_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';

            set_transient( 'errors', $errors, 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings'), "create_template");
            exit;
        }

        $template = new Template();
        $template->handle_request($_REQUEST);

        if($template->is_valid(true))
        {
            $creds = new Creds;
            $creds->api_key = get_option('crmc_mailchimp_api_key', null);
            $creds->username = get_option('crmc_mailchimp_username', null);

            $mailchimp_api = MailChimp::instance();
            try
            {
                $mailchimp_api->create_template($creds, $template);
            }
            catch(\Exception $exception)
            {
                $errors['main'][] = MessageParser::ParseMailChimpMessage($exception->getMessage());
                set_transient( 'errors', $errors, 10 );
                $field_params = $template->toArray();
                redirectToPage((array('page' => 'crmc_settings','tab' => 'invitation_settings') + $field_params), "create_template");
                exit;
            }

            set_transient( 'successMessage', 'Template Successfully Created', 10 );
            redirectToPage(array('page' => 'crmc_settings','tab' => 'invitation_settings'), "create_template");
            exit;
        }

        set_transient( 'errors', $template->errors(), 10 );
        $field_params = $template->toArray();
        redirectToPage((array('page' => 'crmc_settings','tab' => 'invitation_settings') + $field_params), "create_template");
        exit;
    }

    public function get_edit_template_form_action()
    {
        $errors = [];

        if( !isset( $_GET['template_id']) || empty($_GET['template_id']))
        {
            $errors['main'][] = 'Invalid form submission.';

            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        $template_id = sanitize_text_field($_GET['template_id']);

        $creds = new Creds;
        $creds->api_key = get_option('crmc_mailchimp_api_key', null);
        $creds->username = get_option('crmc_mailchimp_username', null);

        $mailchimp_api = MailChimp::instance();
        $response = null;
        try
        {
            $response = $mailchimp_api->get_template($creds, $template_id);
        }
        catch(\Exception $exception)
        {
            $errors['main'][] = MessageParser::ParseMailChimpMessage($exception->getMessage());
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        if($response)
        {
            $template = json_decode(((string) $response->getBody()), true);

            $html_form = $this->render('admin/partials/forms/edit_template_form', array(
                'template' => $template
            ));

            $result = $this->json_response();
            $result['html_form'] = $html_form;
            echo json_encode($result);
            exit;

        }

    }

    public function post_edit_template_form_action()
    {
        $errors = [];

        if( !isset( $_POST['crmc_edit_template_nonce'] ) || !wp_verify_nonce( $_POST['crmc_edit_template_nonce'], 'crmc_edit_template_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if( !isset( $_POST['template_id']) || empty($_POST['template_id']))
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(count($errors) > 0)
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        $template_id = sanitize_text_field($_POST['template_id']);
        $template = new Template();
        $template->handle_request($_REQUEST);

        if($template->is_valid(true))
        {
            $creds = new Creds;
            $creds->api_key = get_option('crmc_mailchimp_api_key', null);
            $creds->username = get_option('crmc_mailchimp_username', null);

            $mailchimp_api = MailChimp::instance();
            try
            {
                $response = $mailchimp_api->edit_template($creds, $template, $template_id);
            }
            catch(\Exception $exception)
            {
                $errors['main'][] = MessageParser::ParseMailChimpMessage($exception->getMessage());
                $result = $this->json_response();
                $result['type'] = "error";
                $result['errors'] = $errors;
                echo json_encode($result);
                exit;
            }

            $result = $this->json_response();
            $result['notices'] = ["Successfully Modified Template!"];
            echo json_encode($result);
            exit;
        }

        $errors = $template->errors();
        $template = $template->to_array_for_view();

        $html_form = $this->render('admin/partials/forms/edit_template_form', array(
            'template' => $template,
            'errors'   => $errors
        ));

        $result = $this->json_response();
        $result['html_form'] = $html_form;
        echo json_encode($result);
        exit;
    }

    public function delete_template_action()
    {
        $errors = [];

        if( !isset( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'crmc_delete_template_nonce') )
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if( !isset( $_POST['template_id']) || empty($_POST['template_id']))
        {
            $errors['main'][] = 'Invalid form submission.';
        }

        if(count($errors) > 0)
        {
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        $template_id = sanitize_text_field($_POST['template_id']);

        $creds = new Creds;
        $creds->api_key = get_option('crmc_mailchimp_api_key', null);
        $creds->username = get_option('crmc_mailchimp_username', null);

        $mailchimp_api = MailChimp::instance();
        try
        {
            $response = $mailchimp_api->delete_template($creds, $template_id);
        }
        catch(\Exception $exception)
        {
            $errors['main'][] = MessageParser::ParseMailChimpMessage($exception->getMessage());
            $result = $this->json_response();
            $result['type'] = "error";
            $result['errors'] = $errors;
            echo json_encode($result);
            exit;
        }

        $result = $this->json_response();
        $result['notices'] = ["Successfully Deleted Template!"];
        echo json_encode($result);
        exit;

    }
}