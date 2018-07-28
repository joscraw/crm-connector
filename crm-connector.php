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