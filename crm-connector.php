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
use CRMConnector\Crons\Initializers\ChapterUpdateMailerCronInitializer;
use CRMConnector\Frontend;
use CRMConnector\Database\DatabaseTableCreator;
use CRMConnector\Support\DatabaseTables;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Workflows\Pub\ChapterLeadershipChanged;
use CRMConnector\Workflows\Pub\ChapterUpdateChanged;
use CRMConnector\Workflows\Pub\ContactChanged;
use CRMConnector\Workflows\Sub\SetChapterLeadershipChapterOperationsName;
use CRMConnector\Workflows\Sub\SetChapterLeadershipChapterOperationsEmail;
use CRMConnector\Workflows\Sub\SetChapterLeadershipIsCurrent;
use CRMConnector\Workflows\Sub\SetChapterLeadershipIsFuture;
use CRMConnector\Workflows\Sub\SetChapterLeadershipName;
use CRMConnector\Workflows\Sub\SetChapterLeadershipTitle;
use CRMConnector\Workflows\Sub\AlertManagerOfChapterLeadershipChange;
use CRMConnector\Workflows\Sub\SetChapterLeadershipChapterOperationsManager;
use CRMConnector\Workflows\Sub\SetChapterAdvisor;
use CRMConnector\Workflows\Sub\SetChapterPresident;
use CRMConnector\Workflows\Sub\SetChapterUpdateChapter;
use CRMConnector\Workflows\Sub\SetChapterUpdateSender;
use CRMConnector\Workflows\Sub\SetContactTitle;
use CRMConnector\Workflows\Sub\SetCurrentChapterLeadership;
use CRMConnector\Models\Contact;
use CRMConnector\Database\Hydrator;
use CRMConnector\Database\ContactSearch;
use CRMConnector\Workflows\Pub\EventChanged;
use CRMConnector\Workflows\Sub\SetChapterOnEvent;

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
        add_action( 'save_post', array($this, 'pre_save_meta'), 1, 3 );
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
        $this->data['current_user'] = wp_get_current_user();
        $this->data['current_user_email'] = wp_get_current_user()->user_email;
        $this->data['current_user_first_name'] = wp_get_current_user()->user_firstname;
        $this->data['current_user_last_name'] = wp_get_current_user()->user_lastname;
        $this->data['current_user_id'] = get_current_user_id();
        $this->data['is_chapter_leader'] = false;
        $this->data['is_system_administrator'] = false;
        $this->data['associated_contact'] = new Contact();
        $this->data['contact_id'] = null;
        $this->data['chapter_id'] = null;
        $user = wp_get_current_user();
        $current_user_roles = ['chapter_officer', 'chapter_president', 'chapter_advisor'];
        $accessible_roles = (array) $user->roles;
        if($current_user_roles !== array_diff($current_user_roles, $accessible_roles)) {
            $this->data['is_chapter_leader'] = true;
        }
        if(in_array('administrator', $accessible_roles)) {
            $this->data['is_system_administrator'] = true;
        }

        if($this->data['current_user_id']) {
            $contact_search = new ContactSearch();
            $contact = $contact_search->get_from_portal_user_id($this->data['current_user_id']);
            if(!empty($contact[0]->ID)) {
                $contact_array = $contact_search->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $contact[0]->ID);
                if(is_array($contact_array)) {
                    Hydrator::toObject($contact_array, $this->data['associated_contact'], true);
                }
            }
        }

        if (!empty($this->data['associated_contact']->ID)){
            $this->data['contact_id'] = $this->data['associated_contact']->ID;
        }

        if(!empty($this->data['associated_contact']->chapter->ID)) {
            $this->data['chapter_id'] = $this->data['associated_contact']->chapter->ID;
        }
    }

    private function _events()
    {
        $this->data['events'][ChapterLeadershipChanged::class] = (new ChapterLeadershipChanged())
            ->addSubscriber(new SetChapterLeadershipName())
            ->addSubscriber(new SetChapterLeadershipChapterOperationsEmail())
            ->addSubscriber(new SetChapterLeadershipChapterOperationsName())
            ->addSubscriber(new SetChapterLeadershipTitle())
            ->addSubscriber(new SetChapterLeadershipIsCurrent())
            ->addSubscriber(new SetChapterLeadershipIsFuture())
            ->addSubscriber(new AlertManagerOfChapterLeadershipChange())
            ->addSubscriber(new SetChapterLeadershipChapterOperationsManager())
            ->addSubscriber(new SetChapterAdvisor())
            ->addSubscriber(new SetChapterPresident())
            ->addSubscriber(new SetCurrentChapterLeadership());

        $this->data['events'][ChapterUpdateChanged::class] = (new ChapterUpdateChanged())
            ->addSubscriber(new SetChapterUpdateChapter())
            ->addSubscriber(new SetChapterUpdateSender());

        $this->data['events'][ContactChanged::class] = (new ContactChanged())
            ->addSubscriber(new SetContactTitle())
            ->addSubscriber(new \CRMConnector\Workflows\Sub\SetContactCodes());

        $this->data['events'][EventChanged::class] = (new EventChanged())
            ->addSubscriber(new SetChapterOnEvent());
    }

    /**
     * @param $post_id
     * @param $post
     * @param $update
     */
    public function pre_save_meta( $post_id, $post, $update )
    {
        if(!empty($this->data['pre_save_meta'])) {
            return;
        }

        $meta = get_post_meta($post_id);
        $this->data['pre_save_meta'] = $meta;
    }

    /**
     * @param $post_id
     * @param $post
     * @param $update
     */
    public function after_save_meta( $post_id, $post, $update )
    {
        // We need to make sure this only gets called when creating or editing a post and not on page load
        if(empty($_POST)) {
            return;
        }
        $post_type = get_post_type($post_id);
        $meta = get_post_meta($post_id);
        $args = [$meta, $post_id];
        $args['pre_save_meta'] = !empty($this->data['pre_save_meta']) ? $this->data['pre_save_meta'] : [];
        $priority = has_action('save_post', array($this, 'after_save_meta'));
        remove_action('save_post', array($this, 'after_save_meta'), $priority);
        switch($post_type) {
            case 'chapter_leadership':
                $this->data['events'][ChapterLeadershipChanged::class]->notify($args);
                break;
            case 'chapter_update':
                $this->data['events'][ChapterUpdateChanged::class]->notify($args);

                if(!$this->data['events'][ChapterUpdateChanged::class]->has_errors()) {
                    // If no manual errors have been thrown
                    ChapterUpdateMailerCronInitializer::enqueue_cron($args[1]);
                }

                break;
            case 'contacts':
                $this->data['events'][ContactChanged::class]->notify($args);
                break;
            case 'tribe_events':
                $this->data['events'][EventChanged::class]->notify($args);
                break;
        }
        add_action('save_post', array($this, 'after_save_meta'), 100, 3);

        unset($this->data['pre_save_meta']);
    }
}

global $CRMConnectorPlugin;
$CRMConnectorPlugin = new CRMConnector();

register_activation_hook( __FILE__, array($CRMConnectorPlugin, 'activate'));

add_action('init', array($CRMConnectorPlugin, 'initialize'));












