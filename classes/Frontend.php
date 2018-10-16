<?php

namespace CRMConnector;

use CRMConnector\Utils\CRMCFunctions;

/**
 * Class Frontend
 * @package CRMConnector
 */
class Frontend
{
    /**
     * @var array
     */
    public $data;

    public function __construct()
    {
        $this->setData();

        /**************************************************************
        Frontend actions and hoooks
         **************************************************************/

        add_action('login_enqueue_scripts', array($this, 'modify_login_page_logo'));

        add_action('admin_bar_menu', array($this, 'remove_from_admin_bar'), 999);


        if($this->data['current_user'] !== 0 &&
            $this->data['current_user'] instanceof \WP_User &&
            in_array( 'student', (array) $this->data['current_user']->roles ))
        {
            add_filter('show_admin_bar', '__return_false');
        }

    }

    private function setData()
    {
        $this->data['current_user'] = wp_get_current_user();
        $this->data['current_user_id'] = get_current_user_id();
        $this->data['current_user_roles'] = $this->data['current_user'] !== 0 ? (array) $this->data['current_user']->roles : [];
    }

    public function modify_login_page_logo()
    {
        ?>
        <style type="text/css">
            body.login div#login h1 a {
                background-image: url(<?php echo CRMCFunctions::plugin_url() . '/assets/images/nscs-logo-v.svg'?>);
            }
        </style>
        <?php
    }

    public function remove_from_admin_bar($wp_admin_bar) {
        $wp_admin_bar->remove_node('updates');
        $wp_admin_bar->remove_node('comments');
        $wp_admin_bar->remove_node('new-content');
        $wp_admin_bar->remove_node('wp-logo');

    }



}