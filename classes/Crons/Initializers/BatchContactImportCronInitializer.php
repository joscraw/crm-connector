<?php

namespace CRMConnector\Crons\Initializers;

use CRMConnector\Crons\Models\BatchContactImportCronModel;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Utils\Logger;


/**
 * Class BatchContactImportCronInitializer
 * @package CRMConnector\Crons\Initializers
 */
class BatchContactImportCronInitializer
{

    private function __construct()
    {
    }

    /**
     * @param BatchContactImportCronModel $batchContactImportCronModel
     */
    public static function enqueue_cron(BatchContactImportCronModel $batchContactImportCronModel)
    {
        global $wpdb;

        $post_id = wp_insert_post([
            "post_type"     =>  'imports',
            "post_status"   =>  'publish',
        ]);

        update_field('date_started',CRMCFunctions::current_date("d/m/Y g:i a"), $post_id);
        update_field('status', 'IN_QUEUE', $post_id);

        $wpdb->query(sprintf("INSERT INTO %s%s (import_id, database_column_names, selected_file_columns, chapter_id, file_upload_path, status, created_at) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', CURRENT_TIMESTAMP)",
            $wpdb->prefix,
            'batch_import_contacts_cron',
            $post_id,
            $batchContactImportCronModel->getDatabaseColumnNames(),
            $batchContactImportCronModel->getSelectedFileColumns(),
            $batchContactImportCronModel->getChapterId(),
            $batchContactImportCronModel->getFileUploadPath(),
            'IN_QUEUE'
        ));
    }

    /**
     * @param $cron_id
     * @param $import_id
     */
    public static function fail_cron($cron_id, $import_id)
    {
        update_field('status', 'FAILED', $import_id);

        global $wpdb;

        $wpdb->query(sprintf("UPDATE %s%s set status='%s' failed_attempts=failed_attempts + 1 WHERE id= %s",
            $wpdb->prefix,
            'batch_import_contacts_cron',
            'FAILED',
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     * @param $import_id
     */
    public static function progress_cron($cron_id, $import_id)
    {
        update_field('status', 'IN_PROGRESS', $import_id);

        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set status='%s' WHERE id= %s",
            $wpdb->prefix,
            'batch_import_contacts_cron',
            'IN_PROGRESS',
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     * @param $import_id
     */
    public static function succeed_cron($cron_id, $import_id)
    {
        update_field('status', 'COMPLETED', $import_id);
        update_field('date_completed',CRMCFunctions::current_date("d/m/Y g:i a"), $import_id);

        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set status='%s', completed_at='%s' WHERE id= %s",
            $wpdb->prefix,
            'batch_import_contacts_cron',
            'COMPLETED',
            (new \DateTime())->format('Y-m-d H:i:s'),
            $cron_id
        ));
    }

    /**
     * @param $cron_id
     * @param $import_id
     * @param Logger $logger
     */
    public static function set_log_file($cron_id, $import_id, Logger $logger)
    {
        update_field('log_file', $logger->get_file_name(), $import_id);

        global $wpdb;
        $wpdb->query(sprintf("UPDATE %s%s set log_file='%s' WHERE id= %s",
            $wpdb->prefix,
            'batch_import_contacts_cron',
            $logger->get_file_name(),
            $cron_id
        ));
    }


}