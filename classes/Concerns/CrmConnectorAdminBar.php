<?php

namespace CRMConnector\Concerns;


use WP_Admin_Bar;


trait CrmConnectorAdminBar
{


    protected function registerAdminBarMenu()
    {
        if(current_user_can('manage_options') === false)
            return;

        add_action('admin_bar_menu', [$this, 'admin_bar']);


    }


    public function admin_bar()
    {
    }


}