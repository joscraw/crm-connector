<?php

namespace CRMConnector\Crons\Initializers;

use CRMConnector\Crons\Models\BatchSubscriptionCronModel;
use CRMConnector\Utils\Logger;

/**
 * Class BatchSubscriptionCronInitializer
 * @package CRMConnector\Crons\Initializers
 */
class BatchSubscriptionCronInitializer
{
    private function __construct()
    {
    }

    /**
     * @param BatchSubscriptionCronModel $batchSubscriptionCronModel
     * @return false|int
     */
    public static function enqueue_cron(BatchSubscriptionCronModel $batchSubscriptionCronModel)
    {
        global $wpdb;

        $result = false;

        if($batchSubscriptionCronModel->getImportId())
        {
            $result = $wpdb->query(sprintf("INSERT INTO %s%s (import_id, status, list_id, type, created_at) VALUES ('%s', '%s', '%s', '%s', CURRENT_TIMESTAMP)",
                $wpdb->prefix,
                'batch_subscription_crons',
                $batchSubscriptionCronModel->getImportId(),
                'IN_PROGRESS',
                $batchSubscriptionCronModel->getListId(),
                $batchSubscriptionCronModel->getType()
            ));
        }

        if($batchSubscriptionCronModel->getExportId())
        {
            $result = $wpdb->query(sprintf("INSERT INTO %s%s (export_id, status, list_id, type, created_at) VALUES ('%s', '%s', '%s', '%s', CURRENT_TIMESTAMP)",
                $wpdb->prefix,
                'batch_subscription_crons',
                $batchSubscriptionCronModel->getExportId(),
                'IN_PROGRESS',
                $batchSubscriptionCronModel->getListId(),
                $batchSubscriptionCronModel->getType()
            ));
        }

        return $result;
    }

    /**
     * @param $cron_id
     */
    public static function fail_cron($cron_id)
    {
        global $wpdb;

        $wpdb->query(sprintf("UPDATE %s%s set status='%s' failed_attempts=failed_attempts + 1 WHERE id= %s",
            $wpdb->prefix,
            'batch_subscription_crons',
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
            'batch_subscription_crons',
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
            'batch_subscription_crons',
            'COMPLETED',
            (new \DateTime())->format('Y-m-d H:i:s'),
            $cron_id
        ));

        $results = $wpdb->get_results(sprintf("SELECT type, import_id, export_id FROM %s%s WHERE id = '%s'",
            $wpdb->prefix,
            'batch_subscription_crons',
            $cron_id
        ));

        $import_id = $results[0]->import_id;
        $export_id = $results[0]->export_id;
        $type = $results[0]->type;

        if($import_id)
        {
            $wpdb->query(sprintf("UPDATE %s%s set in_mandrill='%s' WHERE id= %s",
                $wpdb->prefix,
                'imports',
                ($type === 'unsubscribed') ? false : true,
                $import_id
            ));
        }

        if($export_id)
        {
            $wpdb->query(sprintf("UPDATE %s%s set in_mandrill='%s' WHERE id= %s",
                $wpdb->prefix,
                'exports',
                ($type === 'unsubscribed') ? false : true,
                $export_id
            ));
        }
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
            'batch_subscription_crons',
            $logger->get_file_name(),
            $cron_id
        ));
    }


}