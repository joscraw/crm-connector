<?php

namespace CRMConnector\Utils;

use CRMConnector\Utils\CRMCFunctions;


/**
 * Class Logger
 * @package CRMConnector\Utils
 */
class Logger
{

    private $file_name;

    public function __construct()
    {
        $this->file_name = $this->generate_log_file_name();
    }


    public function write($message)
    {
        file_put_contents(CRMCFunctions::plugin_dir() . '/logs/' . $this->get_file_name(), $this->format_message($message), FILE_APPEND);
    }

    public function get_file_name()
    {
        return $this->file_name;
    }

    /**
     * @param $message
     * @return string
     */
    private function format_message($message)
    {
        $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
            "Attempt: ".($message).PHP_EOL.
            "-------------------------".PHP_EOL;
        return $log;
    }

    /**
     * @return string
     */
    private function generate_log_file_name()
    {
        return date('m-d-Y_h:i:s_a') . '.log';
    }

}