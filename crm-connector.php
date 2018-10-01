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
use CRMConnector\Database\DatabaseTableCreator;
use CRMConnector\Support\DatabaseTables;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Workflows\Pub\ChapterLeadershipChanged;
use CRMConnector\Workflows\Sub\SetChapterLeadershipChapterOperationsName;
use CRMConnector\Workflows\Sub\SetChapterLeadershipChapterOperationsEmail;
use CRMConnector\Workflows\Sub\SetChapterLeadershipIsCurrent;
use CRMConnector\Workflows\Sub\SetChapterLeadershipIsFuture;
use CRMConnector\Workflows\Sub\SetChapterLeadershipName;
use CRMConnector\Workflows\Sub\SetChapterLeadershipTitle;

$autoload_path = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $autoload_path ) ) {
    require_once( $autoload_path );
}

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
    /**
     * @var
     */
    public $data;

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
        add_action( 'save_post', array($this, 'after_save_meta'), 100, 3 );

        if (is_admin()) {
            global $crmConnectorBackend;

            $crmConnectorBackend = new Backend();
        }

        global $crmConnectorFrontend;
        $crmConnectorFrontend = new Frontend();

        DatabaseTableCreator::create();

        $this->registerAdminBarMenu();

        $this->_events();

        $this->_set_data();

    }

    private function _set_data()
    {
    }

    private function _events()
    {
        $this->data['events'][ChapterLeadershipChanged::class] = (new ChapterLeadershipChanged())
            ->addSubscriber(new SetChapterLeadershipName())
            ->addSubscriber(new SetChapterLeadershipChapterOperationsEmail())
            ->addSubscriber(new SetChapterLeadershipChapterOperationsName())
            ->addSubscriber(new SetChapterLeadershipTitle())
            ->addSubscriber(new SetChapterLeadershipIsCurrent())
            ->addSubscriber(new SetChapterLeadershipIsFuture());
    }

    /**
     * @param $post_id
     * @param $post
     * @param $update
     */
    public function after_save_meta( $post_id, $post, $update )
    {
        $post_type = get_post_type($post_id);
        $meta = get_post_meta($post_id);
        $args = [$meta, $post_id];
        $priority = has_action('save_post', array($this, 'after_save_meta'));
        remove_action('save_post', array($this, 'after_save_meta'), $priority);
        switch($post_type) {
            case 'chapter_leadership':
                $this->data['events'][ChapterLeadershipChanged::class]->notify($args);
                break;
        }
        add_action('save_post', array($this, 'after_save_meta'), 100, 3);
    }
}

global $CRMConnectorPlugin;
$CRMConnectorPlugin = new CRMConnector();

register_activation_hook( __FILE__, array($CRMConnectorPlugin, 'activate'));

add_action('init', array($CRMConnectorPlugin, 'initialize'));










