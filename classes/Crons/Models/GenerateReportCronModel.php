<?php

namespace CRMConnector\Crons\Models;

/**
 * Class GenerateReportCronModel
 *
 * @package CRMConnector\Crons\Models
 */
class GenerateReportCronModel
{
    /**
     * @var string
     */
    private $report_type;

    /**
     * @var int
     */
    private $report;

    /**
     * Array or errors which are attached when is_valid() gets called
     * @var array
     */
    private $errors = [];

    /**
     * Returning just one error at a time
     *
     * @return mixed
     */
    public function getErrors()
    {
        return [array_shift($this->errors['main'])];
    }

    /**
     * @return string
     */
    public function getReportType()
    {
        return $this->report_type;
    }

    /**
     * @param string $report_type
     */
    public function setReportType($report_type)
    {
        $this->report_type = $report_type;
    }

    /**
     * @return int
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param int $report
     */
    public function setReport($report)
    {
        $this->report = $report;
    }

    /**
     * @return bool
     */
    public function is_valid()
    {
        $this->validate_report_type()
             ->validate_report();

        return count($this->errors) === 0;
    }


    public function handle_request($request)
    {
        if(isset($request['report_type']))
        {
            $this->setReportType($request['report_type']);
        }

        if(isset($request['report']))
        {
            $this->setReport($request['report']);
        }
    }


    private function validate_report_type()
    {
        $supported_report_types = [
          'direct_mailing'
        ];
        if(!in_array($this->report_type, $supported_report_types)) {
            $this->errors['main'][] = sprintf('No report type exists for %s', $this->report_type);
            return $this;
        }
        return $this;
    }

    private function validate_report()
    {
        if(empty($this->report)) {
            $this->errors['main'][] = 'Missing Report ID';
            return $this;
        }
        return $this;
    }
}