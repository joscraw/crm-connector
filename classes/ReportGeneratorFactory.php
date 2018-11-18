<?php

namespace CRMConnector;

/**
 * Class ReportGeneratorFactory
 * @package CRMConnector
 */
class ReportGeneratorFactory
{
    /**
     * @var null|ReportGeneratorFactory
     */
    private static $instance = null;

    /**
     * @var string
     */
    private $report_id;

    /**
     * ReportGenerator constructor.
     * @param $report_id
     */
    private function __construct($report_id)
    {
        $this->report_id = $report_id;
    }

    /**
     * @param $report_id
     * @return ReportGeneratorFactory|null
     */
    public static function getInstance($report_id)
    {
        if (self::$instance == null)
        {
            self::$instance = new self($report_id);
        }

        return self::$instance;
    }

    /**
     * @param $report_type
     * @return bool|DirectMailerReport
     */
    public function get($report_type)
    {
        switch($report_type) {
            case 'direct_mailing':
                $report = new DirectMailerReport($this->report_id);
                return $report;
                break;
        }

        return false;
    }



}
