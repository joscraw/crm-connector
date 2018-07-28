<?php

namespace CRMConnector;

/**
 * Class Frontend
 * @package CRMConnector
 */
class Frontend
{

    public function __construct()
    {

        /**************************************************************
        Frontend actions and hoooks
         **************************************************************/

        add_action('login_enqueue_scripts','\CRMConnector\Service\WP\WPHooksFilters::login_enqueue_scripts');

    }


}