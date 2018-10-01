<?php

namespace CRMConnector;

use CRMConnector\Api\Models\Contact;
use CRMConnector\Database\ChapterSearch;
use CRMConnector\Database\ContactSearch;
use CRMConnector\Database\DatabaseQuery;
use CRMConnector\ReportGeneratorInterface;
use CRMConnector\Utils\CRMCFunctions;
use WP_Query;

/**
 * Class CurrentChapterRosterReport
 * @author Josh Crawmer <jcrawmer@edoutcome.com>
 */
class CurrentChapterRosterReport implements ReportGeneratorInterface
{
    use DatabaseQuery;
    use Fileable;

    /**
     * @var string
     */
    private $report_id;

    /**
     * @var array
     */
    private $columns = [
        'first_name',
        'last_name',
        'email',
        'contact_type',
        'join_year',
        'phone',
        'mobile',
        'account_name'
    ];

    /**
     * CurrentChapterRosterReport constructor.
     * @param string $report_id
     */
    public function __construct($report_id)
    {
        $this->report_id = $report_id;
    }

    /**
     * Creates the report and sends it to the browser
     */
    public function generate()
    {
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1,
        ];

        $contacts = get_posts($args);

        $args = [
            'post_type' => 'chapters',
            'posts_per_page' => -1,
        ];

        $chapters = get_posts($args);

        $rows = [];

        foreach($contacts as $contact)
        {
            $row = [];
            $row['first_name'] = !empty(explode(" ", get_post_meta($contact->ID, 'full_name', true))[0]) ? explode(" ", get_post_meta($contact->ID, 'full_name', true))[0] : "";
            $row['last_name'] = !empty(explode(" ", get_post_meta($contact->ID, 'full_name', true))[1]) ? explode(" ", get_post_meta($contact->ID, 'full_name', true))[1] : "";
            $row['email'] = get_post_meta($contact->ID, 'email', true);
            $row['contact_type'] = get_post_meta($contact->ID, 'contact_type', true);
            $row['join_year'] = get_post_meta($contact->ID, 'join_year', true);
            $row['phone'] = get_post_meta($contact->ID, 'phone', true);
            $row['mobile'] = get_post_meta($contact->ID, 'mobile', true);
            $row['account_name'] = "";

            foreach($chapters as $chapter)
            {
                if($chapter->ID == get_post_meta($contact->ID, 'account_name', true))
                {
                    $row['account_name'] = get_post_meta($chapter->ID, 'account_name', true);
                    break;
                }
            }
            $rows[] = array_values($row);
        }

        array_unshift($rows, $this->columns);

        $fh = fopen('php://output', 'w');

        foreach($rows as $row)
        {
            fputcsv($fh, $row);
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$this->generate_file_name().'.csv');

    }
}