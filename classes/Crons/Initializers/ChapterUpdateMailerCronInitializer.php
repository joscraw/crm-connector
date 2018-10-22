<?php

namespace CRMConnector\Crons\Initializers;

use CRMConnector\Utils\Logger;

class ChapterUpdateMailerCronInitializer
{
    private function __construct()
    {
    }

    public static function enqueue_cron($chapter_update_id)
    {
        global $wpdb;

        $wpdb->query(sprintf("INSERT INTO %s%s (chapter_update_id, status, created_at) VALUES ('%s', '%s', CURRENT_TIMESTAMP)",
            $wpdb->prefix,
            'chapter_update_cron',
            $chapter_update_id,
            'IN_QUEUE'
        ));
    }

    /**
     * @param $cron_id
     */
    public static function fail_cron($cron_id)
    {
        global $wpdb;

        $wpdb->query(sprintf("UPDATE %s%s set status='%s', failed_attempts=failed_attempts + 1 WHERE id= %s",
            $wpdb->prefix,
            'chapter_update_cron',
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
            'chapter_update_cron',
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
            'chapter_update_cron',
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
            'chapter_update_cron',
            $logger->get_file_name(),
            $cron_id
        ));
    }
}