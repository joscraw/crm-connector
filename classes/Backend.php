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
use CRMConnector\Crons\Initializers\BatchListExportCronInitializer;
use CRMConnector\Crons\Initializers\BatchSubscriptionCronInitializer;
use CRMConnector\Crons\Models\BatchContactImportCronModel;
use CRMConnector\Crons\Models\BatchListExportCronModel;
use CRMConnector\Crons\Models\BatchSubscriptionCronModel;
use CRMConnector\Service\CustomPostType\CustomPostTypeCreator;
use finfo;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Api\GuzzleFactory;
use CRMConnector\Api\HubSpot;
use WP_Query;

/*phpinfo();

die();*/

/*ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);*/

ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
ini_set('memory_limit', '256M');

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

        add_action('admin_init', array($this, 'add_admin_ajax_handlers'));

        add_action( 'admin_init', '\CRMConnector\Service\WP\WPHooksFilters::mailchimp_settings_page');

        add_action('admin_menu', array($this, 'remove_sidebar_admin_menu_items'));

        add_action('admin_footer', array($this, 'crmc_add_modals'));

        add_action('acf/input/admin_head', '\CRMConnector\Service\ACF\ACFHooksFilters::admin_head');

        add_action('admin_head','\CRMConnector\Service\WP\WPHooksFilters::admin_head');

        add_filter('gettext', '\CRMConnector\Service\WP\WPHooksFilters::gettext', 10, 4);

        add_filter('manage_imports_posts_columns', '\CRMConnector\Service\WP\WPHooksFilters::manage_imports_posts_columns');

        add_filter('manage_imports_posts_custom_column', '\CRMConnector\Service\WP\WPHooksFilters::manage_imports_posts_custom_column', 10, 2);

        add_filter('manage_exports_posts_columns', '\CRMConnector\Service\WP\WPHooksFilters::manage_exports_posts_columns');

        add_filter('manage_exports_posts_custom_column', '\CRMConnector\Service\WP\WPHooksFilters::manage_exports_posts_custom_column', 10, 2);

        add_filter('post_updated_messages', '\CRMConnector\Service\WP\WPHooksFilters::update_messages', 10, 1 );

        add_filter('publish_lists', array($this, 'initialize_batch_list_export'), 10, 2);

        add_action( 'admin_notices', '\CRMConnector\Service\WP\WPHooksFilters::admin_notices');

        add_filter('acf/load_field/name=chapter_chapter_officer_select', array($this, 'acf_load_chapter_officer_field_choices_for_given_chapter'));

        add_filter('acf/load_field/name=event_chapter_select_for_officer', array($this, 'acf_load_chapter_field_choice_contact_is_assigned_to'));

        add_filter('acf/load_field/name=custom_list_export_query_field_name', array($this, 'acf_load_contact_post_type_field_names'));

    }

    public function remove_sidebar_admin_menu_items()
    {
        $user = wp_get_current_user();
        if ( !in_array( 'administrator', (array) $user->roles ))
        {

            remove_menu_page('bulk-delete-posts');
            remove_menu_page('export_personal_data');
            remove_menu_page('options-general.php');
            remove_menu_page('tools.php');
            remove_menu_page('edit.php?post_type=acf-field-group');
        }

        if(in_array('chapter_officer', (array) $user->roles))
        {
            remove_menu_page('edit-comments.php');
            remove_menu_page('edit.php');
            remove_menu_page('index.php');
        }
    }

    /**
     * @param $post_id
     * @param $post
     */
    public function initialize_batch_list_export($post_id, $post)
    {
        $model = new BatchListExportCronModel();
        $model->setListId($post_id);

        if(!$model->is_valid())
        {
            set_transient('errors', $model->errors(), 20);
            return;
        }

        set_transient('notice', 'Successfully Added List to Queue For Export');

        BatchListExportCronInitializer::enqueue_cron($model);
    }

    /**
     * Setup the data property with a bunch of useful info being used all around the code
     */
    public function crmc_set_initial_data()
    {
        $this->data['plugin_url'] = plugins_url('/', dirname(__FILE__));

        $this->data['plugin_path'] = dirname(dirname(__FILE__)) . '/';

        $this->data['admin_url'] = admin_url();
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

    public function mailchimp_settings_page() {
        ?>

        <form action='options.php' method='post'>

            <?php
            settings_fields( 'pluginPage' );
            do_settings_sections( 'pluginPage' );
            submit_button();
            ?>

        </form>

        <p class="help-block"><a target="_blank" href="https://us1.admin.mailchimp.com/account/api/?_ga=2.194016621.1157657947.1531760872-769419101.1517934903">Here</a> to obtain your API Keys</p>

        <?php
    }

    public function add_admin_ajax_handlers()
    {
        add_action("wp_ajax_crmc_get_column_names", array( $this, 'get_column_names_action'));
        add_action('wp_ajax_crmc_import_contacts', array( $this, 'import_contacts_action'));
        add_action('wp_ajax_crmc_batch_subscribe_contacts', array( $this, 'batch_subscribe_contacts_action'));
        add_action('wp_ajax_crmc_batch_unsubscribe_contacts', array( $this, 'batch_unsubscribe_contacts_action'));
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

        if($model->move_file())
        {
            BatchContactImportCronInitializer::enqueue_cron($model);

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

    public function acf_load_chapter_officer_field_choices_for_given_chapter( $field ) {

        // reset choices
        $field['choices'] = array();

        global $post;

        $id = $post->ID;

        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1,
            'meta_query' => array(
            array(
                'key' => 'account_name',
                'value' => $id,
                'compare' => '=',
            ))
        ];

        $query = new WP_Query($args);

        if ($query->have_posts() ) {
            while ($query->have_posts()) {
                $query->the_post();
                $contact_id = get_the_ID();
                $contact_name = get_post_meta($contact_id, 'full_name', true);
                $field['choices'][$contact_id] = $contact_name;
            }
        }

       /* wp_reset_postdata();
        wp_reset_query();*/

        // return the field
        return $field;

    }


    public function acf_load_chapter_field_choice_contact_is_assigned_to( $field ) {

        // reset choices
        $field['choices'] = array();

        global $crmConnectorFrontend;
        $current_user_id = $crmConnectorFrontend->data['current_user_id'];

        if($current_user_id === 0 ||
            !$crmConnectorFrontend->data['current_user'] instanceof \WP_User ||
            !in_array( 'chapter_officer', (array) $crmConnectorFrontend->data['current_user']->roles ))
        {
            echo '<p>You must be assigned to a chapter before you can create events!</p>';
            return $field;
        }

        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => 'portal_user',
                    'value' => $current_user_id,
                    'compare' => '=',
                ]

            ]
        ];

        wp_reset_postdata();
        wp_reset_query();

        $query = new WP_Query($args);

        if ($query->have_posts() ) {
            while ($query->have_posts()) {
                $query->the_post();
                $chapter_id = get_post_meta(get_the_ID(), 'account_name', true);
                $chapter_name = get_post_meta($chapter_id, 'account_name', true);
                $field['choices'][$chapter_id] = $chapter_name;
                break;
            }
        }

        // return the field
        return $field;
    }


    public function acf_load_contact_post_type_field_names( $field ) {

        // reset choices
        $field['choices'] = array();

        $groups = acf_get_field_groups(array('post_type' => 'contacts'));

        $choices = [];
        foreach($groups as $group)
        {
            $group_fields = acf_get_fields($group['key']);
            foreach($group_fields as $group_field)
            {
                if(empty($group_field['name']) || empty($group_field['label']))
                {
                    continue;
                }

                $field['choices'][$group_field['name']] = $group_field['label'];
            }
        }

        // return the field
        return $field;
    }

}