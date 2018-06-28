<?php
/**
 * @package CRM Connector
 */
/*
Plugin Name: CRM Connector
Plugin URI: https://www.giftedhire.com/
Description: Connects Wordpress Site to CRM to manage/import students
Version: 1.0.0
Author: Josh Crawmer <joshcrawmer4@yahoo.com>
License: GPLv2 or later
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}


define('CRM_CONNECTOR_VERSION', '1.0.0');
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


/*register_activation_hook( __FILE__, array( 'Akismet', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );*/

require_once(ABSPATH.'wp-admin/includes/upgrade.php');
require_once( PLUGIN_DIR . 'includes/helpers.php' );
require_once( PLUGIN_DIR . 'classes/CRMConnector.php' );
require_once( PLUGIN_DIR . 'classes/DatabaseMigrationInitializer.php' );


register_activation_hook(__FILE__, array( 'DatabaseMigrationInitializer', 'init' ));

add_action( 'init', array( 'CRMConnector', 'init' ) );