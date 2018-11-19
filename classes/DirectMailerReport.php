<?php

namespace CRMConnector;

ini_set('max_execution_time', 0);


use CRMConnector\Database\ChapterSearch;
use CRMConnector\Database\ContactSearch;
use CRMConnector\Database\ChapterInvitationSearch;
use CRMConnector\Database\DropSearch;
use CRMConnector\Database\DatabaseQuery;
use CRMConnector\AbstractReportGenerator;
use CRMConnector\Models\ChapterInvitation;
use CRMConnector\Utils\CRMCFunctions;
use CRMConnector\Utils\Logger;
use WP_Query;
use CRMConnector\Models\Contact;
use CRMConnector\Models\Chapter;
use CRMConnector\Models\Drop;
use CRMConnector\Database\ReportSearch;
use CRMConnector\Database\Hydrator;

/**
 * Class CurrentChapterRosterReport
 * @author Josh Crawmer <jcrawmer@edoutcome.com>
 */
class DirectMailerReport extends AbstractReportGenerator
{
    use DatabaseQuery;
    use Fileable;

    const NUM_OF_CHAPTER_LEADERSHIP = 6;

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
        'Chapter Officer1 Name',
        'Chapter Officer1 Email',
        'Chapter Officer2 Name',
        'Chapter Officer2 Email',
        'Chapter Officer3 Name',
        'Chapter Officer3 Email',
        'Chapter Officer4 Name',
        'Chapter Officer4 Email',
        'Chapter Officer5 Name',
        'Chapter Officer5 Email',
        'Chapter Officer6 Name',
        'Chapter Officer6 Email',
        'Chapter President1 Name',
        'Chapter President1 Email',
        'Chapter President2 Name',
        'Chapter President2 Email',
        'Chapter President3 Name',
        'Chapter President3 Email',
        'Chapter President4 Name',
        'Chapter President4 Email',
        'Chapter President5 Name',
        'Chapter President5 Email',
        'Chapter President6 Name',
        'Chapter President6 Email',
        'Chapter Adviser1 Name',
        'Chapter Adviser1 Email',
        'Chapter Adviser2 Name',
        'Chapter Adviser2 Email',
        'Chapter Adviser3 Name',
        'Chapter Adviser3 Email',
        'Chapter Adviser4 Name',
        'Chapter Adviser4 Email',
        'Chapter Adviser5 Name',
        'Chapter Adviser5 Email',
        'Chapter Adviser6 Name',
        'Chapter Adviser6 Email',
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
     * @param $report_id
     */
    public function __construct($report_id)
    {
        $this->contact_search = new ContactSearch();
        $this->chapter_search = new ChapterSearch();
        $this->chapter_invitation_search = new ChapterInvitationSearch();
        $this->drop_search = new DropSearch();
        $this->progress = new ReportProgress();

        parent::__construct($report_id);
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
        $i = 0;
        foreach($contacts as $contact) {

            $row = [];
            $contactArray = $this->contact_search->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $contact->ID);
            $contact = Hydrator::toObject($contactArray, new Contact(), true);

         /*   if(!$this->is_prospect($contact)) {
                continue;
            }

            if($this->do_not_mail($contact)) {
                continue;
            }

            if($this->has_invalid_address($contact)) {
                continue;
            }*/

            /* if($this->chapter_does_not_match($account_name)) {
                continue;
            }
            */

            /*  if(!$this->is_invitation_approved($chapter_invitation)) {
                  continue;
              }

              if(!$this->is_invitation_ready_to_print($chapter_invitation)) {
                  continue;
              }*/

            $row[] = !empty($contact->first_name) ? $contact->first_name : '';
            $row[] = !empty($contact->last_name) ? $contact->last_name : '';
            $row[] = !empty($contact->permanent_address_1) ? $contact->permanent_address_1 : '';
            $row[] = !empty($contact->permanent_city) ? $contact->permanent_city : '';
            $row[] = !empty($contact->permanent_state) ? $contact->permanent_state : '';
            $row[] = !empty($contact->permanent_zip) ? $contact->permanent_zip : '';
            $row[] = !empty($contact->permanent_country) ? $contact->permanent_country : '';
            $row[] = !empty($contact->current_address_1) ? $contact->current_address_1 : '';
            $row[] = !empty($contact->current_city) ? $contact->current_city : '';
            $row[] = !empty($contact->current_state) ? $contact->current_state : '';
            $row[] = !empty($contact->current_zip) ? $contact->current_zip : '';
            $row[] = !empty($contact->current_country) ? $contact->current_country : '';
            $row[] = !empty($contact->invitation_code) ? $contact->invitation_code : '';
            $row[] = !empty($contact->p1_code) ? $contact->p1_code : '';
            $row[] = !empty($contact->p2_code) ? $contact->p2_code : '';
            $row[] = !empty($contact->p3_code) ? $contact->p3_code : '';
            $row[] = !empty($contact->p4_code) ? $contact->p4_code : '';
            $row[] = !empty($contact->s1_code) ? $contact->s1_code : '';
            $row[] = !empty($contact->s2_code) ? $contact->s2_code : '';
            $row[] = !empty($contact->s3_code) ? $contact->s3_code : '';
            $row[] = !empty($contact->s4_code) ? $contact->s4_code : '';
            $row[] = !empty($contact->chapter->facebook_url) ? $contact->chapter->facebook_url : '';
            $row[] = !empty($contact->chapter->induction_date) ? $contact->chapter->induction_date : '';
            $row[] = !empty($contact->chapter->induction_location) ? $contact->chapter->induction_location: '';
            $row[] = !empty($contact->chapter->induction_time) ? $contact->chapter->induction_time : '';

            // todo not sure about this part right here..... Where should I pull the chapter invitation from. Or which one am I grabbing.
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->letterhead) ? $contact->chapter->get_most_recent_chapter_invitation()->letterhead : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->signature_type) ? $contact->chapter->get_most_recent_chapter_invitation()->signature_type : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->advisor_provided_electronic_signature) ? $contact->chapter->get_most_recent_chapter_invitation()->advisor_provided_electronic_signature : '';

            $row[] = !empty($contact->chapter->chapter_officers->getItem(0)->full_name) ? $contact->chapter->chapter_officers->getItem(0)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(0)->email) ? $contact->chapter->chapter_officers->getItem(0)->email : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(1)->full_name) ? $contact->chapter->chapter_officers->getItem(1)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(1)->email) ? $contact->chapter->chapter_officers->getItem(1)->email : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(2)->full_name) ? $contact->chapter->chapter_officers->getItem(2)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(2)->email) ? $contact->chapter->chapter_officers->getItem(2)->email : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(3)->full_name) ? $contact->chapter->chapter_officers->getItem(3)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(3)->email) ? $contact->chapter->chapter_officers->getItem(3)->email : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(4)->full_name) ? $contact->chapter->chapter_officers->getItem(4)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(4)->email) ? $contact->chapter->chapter_officers->getItem(4)->email : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(5)->full_name) ? $contact->chapter->chapter_officers->getItem(5)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(5)->email) ? $contact->chapter->chapter_officers->getItem(5)->email : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(6)->full_name) ? $contact->chapter->chapter_officers->getItem(6)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_officers->getItem(6)->email) ? $contact->chapter->chapter_officers->getItem(6)->email : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(0)->full_name) ? $contact->chapter->chapter_presidents->getItem(0)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(0)->email) ? $contact->chapter->chapter_presidents->getItem(0)->email : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(1)->full_name) ? $contact->chapter->chapter_presidents->getItem(1)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(1)->email) ? $contact->chapter->chapter_presidents->getItem(1)->email : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(2)->full_name) ? $contact->chapter->chapter_presidents->getItem(2)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(2)->email) ? $contact->chapter->chapter_presidents->getItem(2)->email : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(3)->full_name) ? $contact->chapter->chapter_presidents->getItem(3)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(3)->email) ? $contact->chapter->chapter_presidents->getItem(3)->email : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(4)->full_name) ? $contact->chapter->chapter_presidents->getItem(4)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(4)->email) ? $contact->chapter->chapter_presidents->getItem(4)->email : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(5)->full_name) ? $contact->chapter->chapter_presidents->getItem(5)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(5)->email) ? $contact->chapter->chapter_presidents->getItem(5)->email : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(6)->full_name) ? $contact->chapter->chapter_presidents->getItem(6)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_presidents->getItem(6)->email) ? $contact->chapter->chapter_presidents->getItem(6)->email : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(0)->full_name) ? $contact->chapter->chapter_advisors->getItem(0)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(0)->email) ? $contact->chapter->chapter_advisors->getItem(0)->email : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(1)->full_name) ? $contact->chapter->chapter_advisors->getItem(1)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(1)->email) ? $contact->chapter->chapter_advisors->getItem(1)->email : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(2)->full_name) ? $contact->chapter->chapter_advisors->getItem(2)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(2)->email) ? $contact->chapter->chapter_advisors->getItem(2)->email : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(3)->full_name) ? $contact->chapter->chapter_advisors->getItem(3)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(3)->email) ? $contact->chapter->chapter_advisors->getItem(3)->email : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(4)->full_name) ? $contact->chapter->chapter_advisors->getItem(4)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(4)->email) ? $contact->chapter->chapter_advisors->getItem(4)->email : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(5)->full_name) ? $contact->chapter->chapter_advisors->getItem(5)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(5)->email) ? $contact->chapter->chapter_advisors->getItem(5)->email : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(6)->full_name) ? $contact->chapter->chapter_advisors->getItem(6)->full_name : '';
            $row[] = !empty($contact->chapter->chapter_advisors->getItem(6)->email) ? $contact->chapter->chapter_advisors->getItem(6)->email : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->summer_drop->deadline_wave_one) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->summer_drop->deadline_wave_one)->format("m/d/Y") : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->summer_drop->deadline_wave_two) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->summer_drop->deadline_wave_two)->format("m/d/Y") : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->summer_drop->deadline_wave_three) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->summer_drop->deadline_wave_three)->format("m/d/Y") : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->spring_drop->deadline_wave_one) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->spring_drop->deadline_wave_one)->format("m/d/Y") : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->spring_drop->deadline_wave_two) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->spring_drop->deadline_wave_two)->format("m/d/Y") : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->spring_drop->deadline_wave_three) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->spring_drop->deadline_wave_three)->format("m/d/Y") : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->fall_drop->deadline_wave_one) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->fall_drop->deadline_wave_one)->format("m/d/Y") : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->fall_drop->deadline_wave_two) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->fall_drop->deadline_wave_two)->format("m/d/Y") : '';
            $row[] = !empty($contact->chapter->get_most_recent_chapter_invitation()->fall_drop->deadline_wave_three) ? date_create_from_format('Ymd', $contact->chapter->get_most_recent_chapter_invitation()->fall_drop->deadline_wave_three)->format("m/d/Y") : '';

            $rows[] = $row;
            $this->progress->complete_current_step();

            $logger->write(sprintf("%s / %s contacts added to report", $this->progress->getCurrentStep(), $this->progress->get_total_steps()));

            $i++;

            if($i == 20) {
                break;
            }
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
     * @param $chapter_leadership_ids
     * @param $row
     */
    private function set_chapter_leadership($chapter_leadership_ids, &$row) {
        $starting_array_index = count($chapter_leadership_ids);
        $num_values_to_insert = (self::NUM_OF_CHAPTER_LEADERSHIP - $starting_array_index) >= 0 ? (self::NUM_OF_CHAPTER_LEADERSHIP - $starting_array_index) : 0;
        $chapter_leaders = $chapter_leadership_ids + array_fill($starting_array_index, $num_values_to_insert, null);
        foreach( $chapter_leaders as $chapter_leader_id) {
            $contact = $this->contact_search->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapter_leader_id);
            $row[] = isset($contact['full_name']) ? $contact['full_name'] : '';
            $row[] = isset($contact['email']) ? $contact['email'] : '';
        }
    }

    /**
     * @param $contact
     * @return bool
     */
    private function is_prospect($contact) {

        if(!isset($contact['contact_record_type'])) {
            return false;
        }

        if(stripos($contact['contact_record_type'], 'prospect') !== null) {
            return true;
        }

        return false;
    }

    /**
     * @param $contact
     * @return bool
     */
    private function do_not_mail($contact) {

        if(!isset($contact['do_not_mail'])) {
            return false;
        }

        if($contact['do_not_mail'] === "") {
            return false;
        }

        if((bool) $contact['do_not_mail'] === true) {
            return true;
        }

        return false;
    }

    /**
     * @param $chapter_invitation
     * @return bool
     */
    private function is_invitation_approved($chapter_invitation) {

        //todo ask if this is the proper way to check this
        if(empty($chapter_invitation['invitation_approval_date'])) {
            return false;
        }

        return true;
    }

    /**
     * @param $chapter_invitation
     * @return bool
     */
    private function is_invitation_ready_to_print($chapter_invitation) {

        if((bool) $chapter_invitation['invitation_ready_to_print'] === false) {
            return false;
        }

        return true;
    }

    private function has_invalid_address($contact) {

        $permanent_address_bad = false;
        $current_address_bad = false;

        if(!isset($contact['permanent_address_1'])) {
            $permanent_address_bad = true;
        }

        if(isset($contact['permanent_address_bad']) && (bool) $contact['permanent_address_bad'] === true) {
            $permanent_address_bad = true;
        }

        if(!isset($contact['current_address_1'])) {
            $current_address_bad = true;
        }

        if(isset($contact['current_address_bad']) && (bool) $contact['current_address_bad'] === true) {
            $current_address_bad = true;
        }

        return $permanent_address_bad && $current_address_bad;
    }

    /**
     * @param $account_name
     * @return bool
     */
    private function chapter_does_not_match($account_name) {

        $report = $this->get_report();
        $chapter = isset($report['chapter']) ? $report['chapter'] : '';

        if($chapter && $chapter !== $account_name) {
            return true;
        }

        return false;
    }
}