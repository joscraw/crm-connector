<?php

namespace CRMConnector;

use CRMConnector\Api\Models\Contact;
use CRMConnector\Database\ChapterSearch;
use CRMConnector\Database\ContactSearch;
use CRMConnector\Database\DatabaseQuery;
use CRMConnector\ReportGeneratorInterface;
use WP_Query;

/**
 * Class CurrentChapterRosterReport
 * @author Josh Crawmer <jcrawmer@edoutcome.com>
 */
class CurrentChapterRosterReport implements ReportGeneratorInterface
{
    use DatabaseQuery;

    /**
     * @var string
     */
    private $report_id;

    /**
     * CurrentChapterRosterReport constructor.
     * @param string $report_id
     */
    public function __construct($report_id)
    {
        $this->report_id = $report_id;
    }

    /**
     * @return mixed
     */
    public function generate()
    {
        $search = new ContactSearch();
        $contacts = $search->get_all_contacts_normalized();

        $chapter_search = new ChapterSearch();
        $chapters = $chapter_search->get_all_chapters_normalized();

        $rows = [];

        foreach($contacts as $contact)
        {
            $row = [];
            $row['first_name'] = !empty(explode(" ", $contact->full_name)[0]) ? explode(" ", $contact->full_name)[0] : "";
            $row['last_name'] = !empty(explode(" ", $contact->full_name)[1]) ? explode(" ", $contact->full_name)[1] : "";
            $row['email'] = !empty($contact->email) ? $contact->email : "";
            $row['contact_type'] = !empty($contact->contact_type) ? $contact->contact_type : "";
            $rows[] = $row;
        }
        $name = "Josh";
    }
}