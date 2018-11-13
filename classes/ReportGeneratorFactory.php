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
     * ReportGenerator constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return ReportGeneratorFactory|null
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $report_type
     * @return bool|DirectMailerReport
     */
    public function get($report_type)
    {
        switch($report_type)
        {
            case 'direct_mailing':
                $report = new DirectMailerReport();
                return $report;
                break;
        }

        return false;
    }



}
