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
use CRMConnector\Importer\CRMCDatabaseTables;
use CRMConnector\Support\DatabaseTables;

require_once __DIR__ . '/vendor/autoload.php';
require_once( plugin_dir_path( __FILE__ ). 'includes/helpers.php' );

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}




class CRMConnector {

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

        if(is_admin()) {
            global $crmConnectorBackend;

            $crmConnectorBackend = new Backend();
        }

        CRMCDatabaseTables::verify();


        $this->registerAdminBarMenu();


    }


}


$CRMConnectorPlugin = new CRMConnector();

register_activation_hook( __FILE__, array($CRMConnectorPlugin, 'activate'));

add_action('init', array($CRMConnectorPlugin, 'initialize'));


/*define('CRM_CONNECTOR_VERSION', '1.0.0');
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );*/


/*register_activation_hook( __FILE__, array( 'Akismet', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );*/

/*require_once(ABSPATH.'wp-admin/includes/upgrade.php');
require_once( PLUGIN_DIR . 'includes/helpers.php' );
require_once( PLUGIN_DIR . 'classes/CRMConnector.php' );
require_once( PLUGIN_DIR . 'classes/DatabaseMigrationInitializer.php' );*/


/*register_activation_hook(__FILE__, array( 'DatabaseMigrationInitializer', 'init' ));

add_action( 'init', array( 'CRMConnector', 'init' ) );*/

