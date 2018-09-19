<?php

namespace CRMConnector\Database;


use CRMConnector\Models\Contact;
use CRMConnector\Database\ChapterSearch;

trait DatabaseAttributes
{
    /**
     * @param Contact $contact
     * @return bool|\WP_Query
     */
    public function get_chapter_from_contact(Contact $contact)
    {
        $search = new ChapterSearch();
        $chapter = $search->get_chapter_from_contact($contact);
        return $chapter;
    }

    /**
     * @return array
     */
    public function get_custom_post_type_fields()
    {
        $search = new ContactSearch();
        return $search->get_contact_fields();
    }

    /**
     * @param $email
     * @return ContactSearch
     */
    public function get_contact_from_email($email)
    {
        $search = new ContactSearch();
        $search->get_from_email($email);
        return $search;
    }
}