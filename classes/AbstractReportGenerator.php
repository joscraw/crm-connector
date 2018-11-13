<?php

namespace CRMConnector;

use CRMConnector\Utils\Logger;

/**
 * Interface AbstractReportGenerator
 * @package CRMConnector
 */
abstract class AbstractReportGenerator
{
    /**
     * This array should be set in the child class.
     * It reflects the order of the column names in the csv
     *
     * @var array
     */
    protected $column_names = [];

    /**
     * @param bool $sendToBrowser
     * @param bool $saveAsFile
     * @param Logger $logger
     * @param $report_id
     * @return mixed
     */
    abstract public function generate($sendToBrowser = true, $saveAsFile = false, Logger $logger, $report_id);
}