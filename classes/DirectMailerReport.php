<?php

namespace CRMConnector;

ini_set('max_execution_time', 300);

use CRMConnector\Api\Models\Contact;
use CRMConnector\Database\ChapterSearch;
use CRMConnector\Database\ContactSearch;
use CRMConnector\Database\ChapterInvitationSearch;
use CRMConnector\Database\DropSearch;
use CRMConnector\Database\DatabaseQuery;
use CRMConnector\Models\Chapter;
use CRMConnector\AbstractReportGenerator;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Utils\Logger;
use WP_Query;

/**
 * Class CurrentChapterRosterReport
 * @author Josh Crawmer <jcrawmer@edoutcome.com>
 */
class DirectMailerReport extends AbstractReportGenerator
{
    use DatabaseQuery;
    use Fileable;

    /**
     * @var ContactSearch
     */
    private $contact_search;

    /**
     * @var ChapterSearch
     */
    private $chapter_search;

    /**
     * @var ChapterInvitationSearch
     */
    private $chapter_invitation_search;

    /**
     * @var DropSearch
     */
    private $drop_search;

    /**
     * @var ReportProgress
     */
    private $progress;

    /**
     * @var array
     */
    protected $column_names = [
        'First Name',
        'Last Name',
        'Permanent Address 1',
        'Permanent City',
        'Permanent State',
        'Permanent Zip',
        'Permanent Country',
        'Current Address 1',
        'Current City',
        'Current State',
        'Current Zip',
        'Current Country',
        'Invitation Code',
        'P1 Code',
        'P2 Code',
        'P3 Code',
        'P4 Code',
        'S1 Code',
        'S2 Code',
        'S3 Code',
        'S4 Code',
        'Facebook URL',
        'Induction Date',
        'Induction Location',
        'Induction Time',
        'Letterhead',
        'Signature Type',
        'Advisor Provided Electronic Signature',
        'Chapter Officers',
        'Chapter Presidents',
        'Chapter Advisers',
        'Summer Drop Deadline Wave 1',
        'Summer Drop Deadline Wave 2',
        'Summer Drop Deadline Wave 3',
        'Spring Drop Deadline Wave 1',
        'Spring Drop Deadline Wave 2',
        'Spring Drop Deadline Wave 3',
        'Fall Drop Deadline Wave 1',
        'Fall Drop Deadline Wave 2',
        'Fall Drop Deadline Wave 3',
    ];

    /**
     * CurrentChapterRosterReport constructor.
     */
    public function __construct()
    {
        $this->contact_search = new ContactSearch();
        $this->chapter_search = new ChapterSearch();
        $this->chapter_invitation_search = new ChapterInvitationSearch();
        $this->drop_search = new DropSearch();
        $this->progress = new ReportProgress();
    }

    /**
     * Creates the report and sends it to the browser
     * @param bool $sendToBrowser
     * @param bool $saveAsFile
     * @param Logger $logger
     * @param $report_id
     * @return mixed|void
     */
    public function generate($sendToBrowser = true, $saveAsFile = false, Logger $logger, $report_id)
    {
        $logger->write(sprintf("Counting Contacts For Report: %s...", $this->progress->get_total_steps()));
        $logger->write(sprintf("%s / %s contacts added to report", $this->progress->getCurrentStep(), $this->progress->get_total_steps()));

        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1,
        ];
        $contacts = get_posts($args);

        $rows = [];
        foreach($contacts as $contact) {
            $row = [];
            $contact = $this->contact_search->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $contact->ID);
            $row[] = isset($contact['first_name']) ? $contact['first_name'] : '';
            $row[] = isset($contact['last_name']) ? $contact['last_name'] : '';
            $row[] = isset($contact['permanent_address_1']) ? $contact['permanent_address_1'] : '';
            $row[] = isset($contact['permanent_city']) ? $contact['permanent_city'] : '';
            $row[] = isset($contact['permanent_state']) ? $contact['permanent_state'] : '';
            $row[] = isset($contact['permanent_zip']) ? $contact['permanent_zip'] : '';
            $row[] = isset($contact['permanent_country']) ? $contact['permanent_country'] : '';
            $row[] = isset($contact['current_address_1']) ? $contact['current_address_1'] : '';
            $row[] = isset($contact['current_city']) ? $contact['current_city'] : '';
            $row[] = isset($contact['current_state']) ? $contact['current_state'] : '';
            $row[] = isset($contact['current_zip']) ? $contact['current_zip'] : '';
            $row[] = isset($contact['current_country']) ? $contact['current_country'] : '';
            $row[] = isset($contact['invitation_code']) ? $contact['invitation_code'] : '';
            $row[] = isset($contact['p1_code']) ? $contact['p1_code'] : '';
            $row[] = isset($contact['p2_code']) ? $contact['p2_code'] : '';
            $row[] = isset($contact['p3_code']) ? $contact['p3_code'] : '';
            $row[] = isset($contact['p4_code']) ? $contact['p4_code'] : '';
            $row[] = isset($contact['s1_code']) ? $contact['s1_code'] : '';
            $row[] = isset($contact['s2_code']) ? $contact['s2_code'] : '';
            $row[] = isset($contact['s3_code']) ? $contact['s3_code'] : '';
            $row[] = isset($contact['s4_code']) ? $contact['s4_code'] : '';

            $account_name = !empty($contact['account_name']) ? $contact['account_name'] : null;
            $chapter = $this->chapter_search->get_post_with_meta_values_from_post_id(ChapterSearch::POST_TYPE, $account_name);
            $row[] = !empty($chapter['facebook_url']) ? $chapter['facebook_url'] : '';
            $row[] = !empty($chapter['induction_date']) ? $chapter['induction_date'] : '';
            $row[] = !empty($chapter['induction_location']) ? $chapter['induction_location'] : '';
            $row[] = !empty($chapter['induction_time']) ? $chapter['induction_time'] : '';

            // assume that the chapter invitation with the highest id is the most recent
            // todo this might need better logic. Reach out to nscs about this
            $most_recent_chapter_invitation_id = (!empty($chapter['chapter_invitations']) && is_array($chapter['chapter_invitations']) && count($chapter['chapter_invitations']) > 0) ? max($chapter['chapter_invitations']) : null;

            $chapter_invitation = $this->chapter_invitation_search->get_post_with_meta_values_from_post_id(ChapterInvitationSearch::POST_TYPE, $most_recent_chapter_invitation_id);
            $row[] = !empty($chapter_invitation['letterhead']) ? $chapter_invitation['letterhead'] : '';
            $row[] = !empty($chapter_invitation['signature_type']) ? $chapter_invitation['signature_type'] : '';
            $row[] = !empty($chapter_invitation['advisor_provided_electronic_signature']) ? $chapter_invitation['advisor_provided_electronic_signature'] : '';


            $chapter_officers = (!empty($chapter_invitation['chapter_officers']) && is_array($chapter_invitation['chapter_officers']) && count($chapter_invitation['chapter_officers']) > 0) ? $chapter_invitation['chapter_officers'] : [];
            $contact_info_string_array = [];
            foreach($chapter_officers as $chapter_officer_id) {
                $chapter_officer = $this->contact_search->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapter_officer_id);
                $contact_info_string_array[] = $this->format_contact($chapter_officer);
            }
            $row[] = implode(",",$contact_info_string_array);

            $chapter_presidents = (!empty($chapter_invitation['chapter_president']) && is_array($chapter_invitation['chapter_president']) && count($chapter_invitation['chapter_president']) > 0) ? $chapter_invitation['chapter_president'] : [];
            $contact_info_string_array = [];
            foreach($chapter_presidents as $chapter_president_id) {
                $chapter_president = $this->contact_search->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapter_president_id);
                $contact_info_string_array[] = $this->format_contact($chapter_president);
            }
            $row[] = implode(",",$contact_info_string_array);

            $advisors = (!empty($chapter_invitation['advisors']) && is_array($chapter_invitation['advisors']) && count($chapter_invitation['advisors']) > 0) ? $chapter_invitation['advisors'] : [];
            $contact_info_string_array = [];
            foreach($advisors as $advisor_id) {
                $advisor = $this->contact_search->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $advisor_id);
                $contact_info_string_array[] = $this->format_contact($advisor);
            }
            $row[] = implode(",",$contact_info_string_array);

            $summer_drop_id = !empty($chapter_invitation['summer_drop']) ? $chapter_invitation['summer_drop'] : null;
            $summer_drop = $this->drop_search->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $summer_drop_id);
            $row[] = !empty($summer_drop['deadline_-_wave_1']) ? date_create_from_format('Ymd', $summer_drop['deadline_-_wave_1'])->format("m/d/Y") : '';
            $row[] = !empty($summer_drop['deadline_-_wave_2']) ? date_create_from_format('Ymd', $summer_drop['deadline_-_wave_2'])->format("m/d/Y") : '';
            $row[] = !empty($summer_drop['deadline_-_wave_3']) ? date_create_from_format('Ymd', $summer_drop['deadline_-_wave_3'])->format("m/d/Y") : '';


            $spring_drop_id = !empty($chapter_invitation['spring_drop']) ? $chapter_invitation['spring_drop'] : null;
            $spring_drop = $this->drop_search->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $spring_drop_id);
            $row[] = !empty($spring_drop['deadline_-_wave_1']) ? date_create_from_format('Ymd', $spring_drop['deadline_-_wave_1'])->format("m/d/Y") : '';
            $row[] = !empty($spring_drop['deadline_-_wave_2']) ? date_create_from_format('Ymd', $spring_drop['deadline_-_wave_2'])->format("m/d/Y") : '';
            $row[] = !empty($spring_drop['deadline_-_wave_3']) ? date_create_from_format('Ymd', $spring_drop['deadline_-_wave_3'])->format("m/d/Y") : '';

            $fall_drop_id = !empty($chapter_invitation['fall_drop']) ? $chapter_invitation['fall_drop'] : null;
            $fall_drop = $this->drop_search->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $fall_drop_id);
            $row[] = !empty($fall_drop['deadline_-_wave_1']) ? date_create_from_format('Ymd', $fall_drop['deadline_-_wave_1'])->format("m/d/Y") : '';
            $row[] = !empty($fall_drop['deadline_-_wave_2']) ? date_create_from_format('Ymd', $fall_drop['deadline_-_wave_2'])->format("m/d/Y") : '';
            $row[] = !empty($fall_drop['deadline_-_wave_3']) ? date_create_from_format('Ymd', $fall_drop['deadline_-_wave_3'])->format("m/d/Y") : '';

            $rows[] = $row;
            $this->progress->complete_current_step();
            $logger->write(sprintf("%s / %s contacts added to report", $this->progress->getCurrentStep(), $this->progress->get_total_steps()));
        }

        array_unshift($rows, $this->column_names);
        $file_name = sprintf("Direct-Mailer-%s.csv", $this->generate_file_name());

        if($saveAsFile) {
            $logger->write(sprintf("Converting Report to file for later download"));
            $fh = fopen(sprintf("%s/reports/%s",
                CRMCFunctions::plugin_dir(),
                $file_name
                ), 'w');

            foreach($rows as $row) {
                fputcsv($fh, $row);
            }

            fclose($fh);
            update_post_meta($report_id, 'report_url', CRMCFunctions::plugin_url() . '/reports/' . $file_name);
            $logger->write(sprintf("Conversion complete."));
        }

        if($sendToBrowser) {
            $logger->write(sprintf("Converting Report to file for immediate download to browser"));
            $fh = fopen('php://output', 'w');

            foreach($rows as $row) {
                fputcsv($fh, $row);
            }

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$file_name.'.csv');

            fclose($fh);
            $logger->write(sprintf("Download to browser completed"));
        }
    }

    /**
     * @param $contact
     * @return string
     */
    private function format_contact($contact) {
        return sprintf("%s - %s",
            !empty($contact['full_name']) ? $contact['full_name'] : '',
            !empty($contact['email']) ? $contact['email'] : ''
        );
    }
}