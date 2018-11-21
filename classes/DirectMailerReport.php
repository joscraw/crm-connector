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
        'Drop Deadline Wave 1',
        'Drop Deadline Wave 2',
        'Drop Deadline Wave 3',
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
        $rows = [];
        $report = $this->get_report();
        $dropArray = $this->drop_search->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $report['drop']);
        $drop = Hydrator::toObject($dropArray, new Drop(), true);

        foreach($drop->chapter_invitations as $chapter_invitation) {

            if(!isset($chapter_invitation->chapter)) {
                $logger->write(sprintf("Skipping Chapter Invitation with ID %s and Title %s - missing assigned chapter.", $chapter_invitation->ID, $chapter_invitation->chapter_invitation_title));
                continue;
            }

            $chapter = $chapter_invitation->chapter;

            $logger->write(sprintf("Found %s Contacts for Chapter Invitation %s...", $chapter->contacts->length(), $chapter_invitation->chapter_invitation_title));

            $i = 1;
            foreach($chapter->contacts as $contact) {

                $row = [];
                $row[] = isset($contact->first_name) ? $contact->first_name : '';
                $row[] = isset($contact->last_name) ? $contact->last_name : '';
                $row[] = isset($contact->permanent_address_1) ? $contact->permanent_address_1 : '';
                $row[] = isset($contact->permanent_city) ? $contact->permanent_city : '';
                $row[] = isset($contact->permanent_state) ? $contact->permanent_state : '';
                $row[] = isset($contact->permanent_zip) ? $contact->permanent_zip : '';
                $row[] = isset($contact->permanent_country) ? $contact->permanent_country : '';
                $row[] = isset($contact->current_address_1) ? $contact->current_address_1 : '';
                $row[] = isset($contact->current_city) ? $contact->current_city : '';
                $row[] = isset($contact->current_state) ? $contact->current_state : '';
                $row[] = isset($contact->current_zip) ? $contact->current_zip : '';
                $row[] = isset($contact->current_country) ? $contact->current_country : '';
                $row[] = isset($contact->invitation_code) ? $contact->invitation_code : '';
                $row[] = isset($contact->p1_code) ? $contact->p1_code : '';
                $row[] = isset($contact->p2_code) ? $contact->p2_code : '';
                $row[] = isset($contact->p3_code) ? $contact->p3_code : '';
                $row[] = isset($contact->p4_code) ? $contact->p4_code : '';
                $row[] = isset($contact->s1_code) ? $contact->s1_code : '';
                $row[] = isset($contact->s2_code) ? $contact->s2_code : '';
                $row[] = isset($contact->s3_code) ? $contact->s3_code : '';
                $row[] = isset($contact->s4_code) ? $contact->s4_code : '';
                $row[] = isset($chapter->facebook_url) ? $chapter->facebook_url : '';
                $row[] = isset($chapter->induction_date) ? $chapter->induction_date : '';
                $row[] = isset($chapter->induction_location) ? $chapter->induction_location : '';
                $row[] = isset($chapter->induction_time) ? $chapter->induction_time : '';
                $row[] = isset($chapter_invitation->letterhead) ? $chapter_invitation->letterhead : '';
                $row[] = isset($chapter_invitation->signature_type) ? $chapter_invitation->signature_type : '';
                $row[] = isset($chapter_invitation->advisor_provided_electronic_signature) ? $chapter_invitation->advisor_provided_electronic_signature : '';
                $row[] = !empty($chapter->chapter_officers->getItem(0)->full_name) ? $chapter->chapter_officers->getItem(0)->full_name : '';
                $row[] = !empty($chapter->chapter_officers->getItem(0)->email) ? $chapter->chapter_officers->getItem(0)->email : '';
                $row[] = !empty($chapter->chapter_officers->getItem(1)->full_name) ? $chapter->chapter_officers->getItem(1)->full_name : '';
                $row[] = !empty($chapter->chapter_officers->getItem(1)->email) ? $chapter->chapter_officers->getItem(1)->email : '';
                $row[] = !empty($chapter->chapter_officers->getItem(2)->full_name) ? $chapter->chapter_officers->getItem(2)->full_name : '';
                $row[] = !empty($chapter->chapter_officers->getItem(2)->email) ? $chapter->chapter_officers->getItem(2)->email : '';
                $row[] = !empty($chapter->chapter_officers->getItem(3)->full_name) ? $chapter->chapter_officers->getItem(3)->full_name : '';
                $row[] = !empty($chapter->chapter_officers->getItem(3)->email) ? $chapter->chapter_officers->getItem(3)->email : '';
                $row[] = !empty($chapter->chapter_officers->getItem(4)->full_name) ? $chapter->chapter_officers->getItem(4)->full_name : '';
                $row[] = !empty($chapter->chapter_officers->getItem(4)->email) ? $chapter->chapter_officers->getItem(4)->email : '';
                $row[] = !empty($chapter->chapter_officers->getItem(5)->full_name) ? $chapter->chapter_officers->getItem(5)->full_name : '';
                $row[] = !empty($chapter->chapter_officers->getItem(5)->email) ? $chapter->chapter_officers->getItem(5)->email : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(0)->full_name) ? $chapter->chapter_presidents->getItem(0)->full_name : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(0)->email) ? $chapter->chapter_presidents->getItem(0)->email : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(1)->full_name) ? $chapter->chapter_presidents->getItem(1)->full_name : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(1)->email) ? $chapter->chapter_presidents->getItem(1)->email : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(2)->full_name) ? $chapter->chapter_presidents->getItem(2)->full_name : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(2)->email) ? $chapter->chapter_presidents->getItem(2)->email : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(3)->full_name) ? $chapter->chapter_presidents->getItem(3)->full_name : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(3)->email) ? $chapter->chapter_presidents->getItem(3)->email : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(4)->full_name) ? $chapter->chapter_presidents->getItem(4)->full_name : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(4)->email) ? $chapter->chapter_presidents->getItem(4)->email : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(5)->full_name) ? $chapter->chapter_presidents->getItem(5)->full_name : '';
                $row[] = !empty($chapter->chapter_presidents->getItem(5)->email) ? $chapter->chapter_presidents->getItem(5)->email : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(0)->full_name) ? $chapter->chapter_advisors->getItem(0)->full_name : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(0)->email) ? $chapter->chapter_advisors->getItem(0)->email : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(1)->full_name) ? $chapter->chapter_advisors->getItem(1)->full_name : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(1)->email) ? $chapter->chapter_advisors->getItem(1)->email : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(2)->full_name) ? $chapter->chapter_advisors->getItem(2)->full_name : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(2)->email) ? $chapter->chapter_advisors->getItem(2)->email : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(3)->full_name) ? $chapter->chapter_advisors->getItem(3)->full_name : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(3)->email) ? $chapter->chapter_advisors->getItem(3)->email : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(4)->full_name) ? $chapter->chapter_advisors->getItem(4)->full_name : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(4)->email) ? $chapter->chapter_advisors->getItem(4)->email : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(5)->full_name) ? $chapter->chapter_advisors->getItem(5)->full_name : '';
                $row[] = !empty($chapter->chapter_advisors->getItem(5)->email) ? $chapter->chapter_advisors->getItem(5)->email : '';
                $row[] = !empty($drop->deadline_wave_one) ? date_create_from_format('Ymd', $drop->deadline_wave_one)->format("m/d/Y") : '';
                $row[] = !empty($drop->deadline_wave_two) ? date_create_from_format('Ymd', $drop->deadline_wave_two)->format("m/d/Y") : '';
                $row[] = !empty($drop->deadline_wave_three) ? date_create_from_format('Ymd', $drop->deadline_wave_three)->format("m/d/Y") : '';



                // Perform validation to see whether or not you should add this contact to the report
                $prospect_load_date = $contact->get_prospect_load_date();

                if($report['desired_prospect_load_date']) {

                    $desired_prospect_load_date = new \DateTime();
                    $desired_prospect_load_date = $desired_prospect_load_date->createFromFormat('!Ymd', $report['desired_prospect_load_date']);

                    switch($report['date_comparison']) {
                        case '<':
                            if($prospect_load_date >= $desired_prospect_load_date) {
                                continue;
                            }
                            break;

                        case '>':
                            if($prospect_load_date <= $desired_prospect_load_date) {
                                continue;
                            }
                            break;

                        case '=':
                            if($desired_prospect_load_date !== $prospect_load_date) {
                                continue;
                            }
                            break;
                    }
                }

                if(!$contact->is_prospect()) {
                    continue;
                }

                if($contact->do_not_mail()) {
                    continue;
                }

                if(!$contact->has_valid_address()) {
                    continue;
                }

                if(empty($chapter_invitation->invitation_approved)) {
                   continue;
                }

                if(empty($chapter_invitation->invitation_ready_to_print)) {
                    continue;
                }

                if(!empty($report['chapter']) && $report['chapter'] !== $chapter->ID) {
                    continue;
                }

                $rows[] = $row;

                $logger->write(sprintf("%s / %s contacts added to report", $i, $chapter->contacts->length()));
                $i++;
            }
        }

        /* if($this->chapter_does_not_match($account_name)) {
            continue;
        }
        */

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