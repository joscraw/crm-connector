<?php


/** Loads the WordPress Environment and Template */
use CRMConnector\ContactMapper;
use CRMConnector\Crons\Initializers\BatchContactImportCronInitializer;
use CRMConnector\ContactTransformer;
use CRMConnector\Crons\Initializers\ChapterUpdateMailerCronInitializer;
use CRMConnector\Database\ContactSearch;
use CRMConnector\ExcelMapper;
use CRMConnector\Mailers\ChapterUpdateMailer;
use CRMConnector\Models\Collection;
use CRMConnector\StudentImportMapper;
use CRMConnector\Utils\Logger;
use CRMConnector\DeDuplicate;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;

require( dirname( __FILE__ ) . '/../../../../wp/wp-load.php' );

global $wpdb;

$results = $wpdb->get_results(sprintf("SELECT id, chapter_update_id FROM %s%s WHERE status = '%s' AND failed_attempts <= 3 ORDER BY created_at DESC",
    $wpdb->prefix,
    'chapter_update_cron',
    'IN_QUEUE'
));

foreach($results as $result) {
    $import_failed = false;
    $cron_id = $result->id;
    $chapter_update_id = $result->chapter_update_id;
    $logger = new Logger();
    $logger->write(sprintf("Initializing Cron with id %s...", $cron_id));
    ChapterUpdateMailerCronInitializer::set_log_file($cron_id, $logger);

    $args = [
        'p'         => $chapter_update_id,
        'post_type' => 'chapter_update',
        'posts_per_page' => -1
    ];
    $posts = get_posts($args);
    if(count($posts) === 0)
    {
        ChapterUpdateMailerCronInitializer::fail_cron($cron_id);
        $logger->write(sprintf("Chapter update %s could not be found.", $chapter_update_id));
        continue;
    }

    $chapter_id = get_post_meta($chapter_update_id, 'chapter', true);
    $contact_id = get_post_meta($chapter_update_id, 'sender', true);
    $message_body = get_post_meta($chapter_update_id, 'message_body', true);
    $subject = get_post_meta($chapter_update_id, 'subject', true);
    $recipients = get_post_meta($chapter_update_id, 'recipients', true);
    $contact_ids = [];

    foreach($recipients as $recipient) {
        $contact_ids = [];
        switch($recipient) {
            case 'All Undergraduates':
                $args = [
                    'post_type' => 'contacts',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'account_name',
                            'value' => $chapter_id,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'expected_graduation_date',
                            'value' => date("Y-m-d"),
                            'compare' => '>=',
                            'type' => 'DATE'
                        )
                    ),
                ];
                $posts = get_posts($args);
                foreach($posts as $post) {
                    $contact_ids[] = $post->ID;
                }
                break;
            case 'Officers':
                $contact_ids = get_post_meta($chapter_id, 'chapter_officer', true);
                break;
            case 'Alumni':
                $args = [
                    'post_type' => 'contacts',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'account_name',
                            'value' => $chapter_id,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'expected_graduation_date',
                            'value' => date("Y-m-d"),
                            'compare' => '<=',
                            'type' => 'DATE'
                        )
                    ),
                ];
                $posts = get_posts($args);
                foreach($posts as $post) {
                    $contact_ids[] = $post->ID;
                }
                break;
            case 'New Members':
                $time = strtotime("-1 year", time());
                $one_year_ago = date("Y-m-d", $time);
                $args = [
                    'post_type' => 'contacts',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'account_name',
                            'value' => $chapter_id,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'join_date',
                            'value' => $one_year_ago,
                            'compare' => '>=',
                            'type' => 'DATE'
                        )
                    ),
                ];
                $posts = get_posts($args);
                foreach($posts as $post) {
                    $contact_ids[] = $post->ID;
                }
                break;
        }

        if(count($contact_ids) === 0) {
            continue;
        }

        foreach($contact_ids as $id) {
            $full_name = get_post_meta($id, 'full_name', true);
            $email = get_post_meta($id, 'email', true);
            // send the email here
            $mailer = new ChapterUpdateMailer();
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mailer->addAddress($email, $full_name);
            } else {
                continue;
            }
            $args = [];
            $args['Subject'] = $subject;
            $args['Body'] = $message_body;
            $mailer->initialize($args);
            try {
                $mailer->send();
                $logger->write(sprintf("Email sent to %s.", $email));
            } catch(Exception $e) {
                $logger->write(sprintf('Message could not be sent. Mailer Error: %s', $mailer->get_mail()->ErrorInfo));
            }
        }
    }

    $logger->write(sprintf("Finished Cron %s.", $cron_id));
    ChapterUpdateMailerCronInitializer::succeed_cron($cron_id);
}