<?php

namespace CRMConnector\Crons\Initializers;


use CRMConnector\Crons\Models\BatchListExportCronModel;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Utils\Logger;

class BatchListExportCronInitializer
{

    private function __construct()
    {
    }

    /**
     * @param BatchListExportCronModel $batchListExportCronModel
     */
    public static function enqueue_cron(BatchListExportCronModel $batchListExportCronModel)
    {
        global $wpdb;

        $post_id = wp_insert_post([
            "post_type"     =>  'exports',
            "post_status"   =>  'publish',
        ]);

        update_field('date_started',CRMCFunctions::current_date("d/m/Y g:i a"), $post_id);
        update_field('status', 'IN_QUEUE', $post_id);

        $wpdb->query(sprintf("INSERT INTO %s%s (export_id, list_id, status, created_at) VALUES ('%s', '%s', '%s', CURRENT_TIMESTAMP)",
            $wpdb->prefix,
            'batch_list_export_crons',
            $post_id,
            $batchListExportCronModel->getListId(),
            'IN_QUEUE'
        ));
    }

    /**
     * @param $cron_id
     * @param $export_id
     */
    public static function fail_cron($cron_id, $export_id)
    {
        update_field('status', 'FAILED', $export_id);

        global $wpdb;

        $wpdb->query(sprintf("UPDATE %s%s set status='%s', failed_attempts=failed_attempts + 1 WHERE id= %s",
            $wpdb->prefix,
            'batch_list_export_crons',
            'FAILED',
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     * @param $export_id
     */
    public static function progress_cron($cron_id, $export_id)
    {
        update_field('status', 'IN_PROGRESS', $export_id);

        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set status='%s' WHERE id= %s",
            $wpdb->prefix,
            'batch_list_export_crons',
            'IN_PROGRESS',
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     * @param $export_id
     */
    public static function succeed_cron($cron_id, $export_id)
    {
        update_field('status', 'COMPLETED', $export_id);
        update_field('date_completed',CRMCFunctions::current_date("d/m/Y g:i a"), $export_id);

        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set status='%s', completed_at='%s' WHERE id= %s",
            $wpdb->prefix,
            'batch_list_export_crons',
            'COMPLETED',
            (new \DateTime())->format('Y-m-d H:i:s'),
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     * @param $export_id
     * @param Logger $logger
     */
    public static function set_log_file($cron_id, $export_id, Logger $logger)
    {
        update_field('log_file', $logger->get_file_name(), $export_id);

        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set log_file='%s' WHERE id= %s",
            $wpdb->prefix,
            'batch_list_export_crons',
            $logger->get_file_name(),
            $cron_id
        ));
    }

}