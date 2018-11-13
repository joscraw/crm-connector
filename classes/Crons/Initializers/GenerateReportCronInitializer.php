<?php

namespace CRMConnector\Crons\Initializers;

use CRMConnector\Crons\Models\GenerateReportCronModel;
use CRMConnector\Utils\Logger;

/**
 * Class GenerateReportCronInitializer
 * @package CRMConnector\Crons\Initializers
 */
class GenerateReportCronInitializer
{
    private function __construct()
    {
    }

    /**
     * @param GenerateReportCronModel $generateReportCronModel
     * @return false|int
     */
    public static function enqueue_cron(GenerateReportCronModel $generateReportCronModel)
    {
        global $wpdb;

        $result = $wpdb->query(sprintf("INSERT INTO %s%s (report_type, report_id, status, created_at) VALUES ('%s', '%s', '%s', CURRENT_TIMESTAMP)",
            $wpdb->prefix,
            'generate_report_cron',
            $generateReportCronModel->getReportType(),
            $generateReportCronModel->getReport(),
            'IN_QUEUE'
        ));

        return $result;
    }

    /**
     * @param $cron_id
     */
    public static function fail_cron($cron_id)
    {
        global $wpdb;

        $wpdb->query(sprintf("UPDATE %s%s set status='%s', failed_attempts=failed_attempts + 1 WHERE id= %s",
            $wpdb->prefix,
            'generate_report_cron',
            'FAILED',
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     */
    public static function progress_cron($cron_id)
    {
        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set status='%s' WHERE id= %s",
            $wpdb->prefix,
            'generate_report_cron',
            'IN_PROGRESS',
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     */
    public static function succeed_cron($cron_id)
    {
        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set status='%s', completed_at='%s' WHERE id= %s",
            $wpdb->prefix,
            'generate_report_cron',
            'COMPLETED',
            (new \DateTime())->format('Y-m-d H:i:s'),
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     * @param Logger $logger
     */
    public static function set_log_file($cron_id, Logger $logger)
    {
        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set log_file='%s' WHERE id= %s",
            $wpdb->prefix,
            'generate_report_cron',
            $logger->get_file_name(),
            $cron_id
        ));
    }
}