<?php

namespace CRMConnector\Crons\Initializers;

use CRMConnector\Crons\Models\BatchContactImportCronModel;
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
     * @return false|int
     */
    public static function enqueue_cron(BatchContactImportCronModel $batchContactImportCronModel)
    {
        global $wpdb;

        $result = false;

        $result = $wpdb->query(sprintf("INSERT INTO %s%s (database_column_names, selected_file_columns, chapter_id, file_upload_path, status, created_at) VALUES ('%s', '%s', '%s', '%s', '%s', CURRENT_TIMESTAMP)",
            $wpdb->prefix,
            'batch_import_contacts_cron',
            $batchContactImportCronModel->getDatabaseColumnNames(),
            $batchContactImportCronModel->getSelectedFileColumns(),
            $batchContactImportCronModel->getChapterId(),
            $batchContactImportCronModel->getFileUploadPath(),
            'IN_PROGRESS'
        ));

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
            'batch_import_contacts_cron',
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
            'batch_import_contacts_cron',
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
            'batch_import_contacts_cron',
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
            'batch_import_contacts_cron',
            $logger->get_file_name(),
            $cron_id
        ));
    }


}