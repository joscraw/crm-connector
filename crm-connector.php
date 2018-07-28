<?php

/*
Plugin Name: CRM Connector
Plugin URI: https://www.giftedhire.com/
Description: Connects Wordpress Site to CRM to manage/import students
Version: 1.0.0
Author: Josh Crawmer <joshcrawmer4@yahoo.com>
License: GPLv2 or later
*/

use CRMConnector\Backend;
use CRMConnector\Concerns\CrmConnectorAdminBar;
use CRMConnector\Frontend;
use CRMConnector\Importer\CRMCDatabaseTables;
use CRMConnector\Support\DatabaseTables;
use CRMConnector\Utils\CRMCFunctions;

require_once __DIR__ . '/vendor/autoload.php';
require_once( plugin_dir_path( __FILE__ ). 'includes/helpers.php' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}


/**
 * Class CRMConnector
 */
class CRMConnector
{

    use CrmConnectorAdminBar;

    /**
     * @var string
     */
    public $version = '1.0.0';


    public function __construct()
    {
    }

    /**
     * Run when the plugin is activated
     */
    public function activate()
    {
    }

    /**
     * Run during the initialization of Wordpress
     */
    public function initialize()
    {

        if (is_admin()) {
            global $crmConnectorBackend;

            $crmConnectorBackend = new Backend();
        }

        global $crmConnectorFrontend;
        $crmConnectorFrontend = new Frontend();

        CRMCDatabaseTables::verify();

        $this->registerAdminBarMenu();

    }
}

$CRMConnectorPlugin = new CRMConnector();

register_activation_hook( __FILE__, array($CRMConnectorPlugin, 'activate'));

add_action('init', array($CRMConnectorPlugin, 'initialize'));




/*function acf_load_color_field_choices( $field ) {

    // reset choices
    $field['sub_fields'][0]['choices'] = array();

    $groups = acf_get_field_groups(array('post_type' => 'contacts'));

    $select_fields = [];
    foreach($groups as $group)
    {
        $group_key = $group['key'];
        $fields = acf_get_fields($group_key);
        foreach($fields as $field)
        {
            $select_fields[] = $field['label'];
        }
    }

    foreach($select_fields as $selectField)
    {
        file_put_contents(CRMCFunctions::plugin_dir() . '/logs/test.txt', $selectField . "\n", FILE_APPEND);
    }

    $field['sub_fields'][0]['choices'] = $select_fields;
    $name = "jsoH";



    // get the textarea value from options page without any formatting
    $choices = get_field('my_select_values', 'option', false);

    // return the field
    return $field;

}

add_filter('acf/load_field/name=query_fields', 'acf_load_color_field_choices');*/