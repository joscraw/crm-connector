<?php

namespace CRMConnector;

/**
 * Class Fileable
 * @package CRMConnector
 */
trait Fileable
{
    /**
     * @return string
     */
    private function generate_file_name()
    {
        return date('m-d-Y_h:i:s_a');
    }

}