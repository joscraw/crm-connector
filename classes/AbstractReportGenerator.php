<?php

namespace CRMConnector;

use CRMConnector\Database\ReportSearch;
use CRMConnector\Utils\Logger;

/**
 * Interface AbstractReportGenerator
 * @package CRMConnector
 */
abstract class AbstractReportGenerator
{
    protected $report_id;

    /**
     * This array should be set in the child class.
     * It reflects the order of the column names in the csv
     *
     * @var array
     */
    protected $column_names = [];

    /**
     * AbstractReportGenerator constructor.
     * @param $report_id
     */
    public function __construct($report_id)
    {
        $this->report_id = $report_id;
    }

    /**
     * @param bool $sendToBrowser
     * @param bool $saveAsFile
     * @param Logger $logger
     * @param $report_id
     * @return mixed
     */
    abstract public function generate($sendToBrowser = true, $saveAsFile = false, Logger $logger, $report_id);

    /**
     * @return array|bool
     */
    public function get_report() {

        $report_search = new ReportSearch();

        return $report_search->get_post_with_meta_values_from_post_id(ReportSearch::POST_TYPE, $this->report_id);

    }
}