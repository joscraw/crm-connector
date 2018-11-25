<?php

namespace CRMConnector\Database;

use CRMConnector\Models\Chapter;
use CRMConnector\Models\ChapterInvitation;
use CRMConnector\Models\Collection;
use CRMConnector\Models\Contact;
use CRMConnector\Models\Drop;

/**
 * Class ChapterHydrator
 * @package CRMConnector\Database
 */
class ChapterHydrator
{
    /**
     * @param array $array
     * @param $object
     * @param bool $hydrate_contacts BE CAREFUL WHEN PADDING $hydrate_contacts = true as
     * this can be super expensive if a chapter has a lot of contacts
     * @return mixed
     */
    public static function toObject(array $array = [], $object, $hydrate_contacts = false) {

        if(!$object instanceof Chapter) {
            return $object;
        }

        $object->fromArray($array);

        if(!isset($object->chapter_invitations)) {
            $object->chapter_invitations = new Collection();
        }
        if(!is_array($object->chapter_invitations)) {
            $object->chapter_invitations = new Collection();
        }
        $chapter_invitations = new Collection();
        foreach($object->chapter_invitations as $chapterInvitation) {
            $chapterInvitationArray = (new ChapterInvitationSearch())->get_post_with_meta_values_from_post_id(ChapterInvitationSearch::POST_TYPE, $chapterInvitation);
            $chapterInvitation = new ChapterInvitation();
            $chapterInvitation->fromArray($chapterInvitationArray);
            $chapter_invitations->addItem($chapterInvitation);
        }
        $object->chapter_invitations = $chapter_invitations;

        if(!isset($object->chapter_officers)) {
            $object->chapter_officers = new Collection();
        }
        if(!is_array($object->chapter_officers)) {
            $object->chapter_officers = new Collection();
        }
        $chapter_officers = new Collection();
        foreach($object->chapter_officers as $chapterOfficer) {
            $chapterOfficerArray = (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterOfficer);
            $chapterOfficer = new Contact();
            $chapterOfficer->fromArray($chapterOfficerArray);
            $chapter_officers->addItem($chapterOfficer);
        }
        $object->chapter_officers = $chapter_officers;

        if(!isset($object->chapter_presidents)) {
            $object->chapter_presidents = new Collection();
        }
        if(!is_array($object->chapter_presidents)) {
            $object->chapter_presidents = new Collection();
        }
        $chapter_presidents = new Collection();
        foreach($object->chapter_presidents as $chapterPresident) {
            $chapterPresidentArray = (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterPresident);
            $chapterPresident = new Contact();
            $chapterPresident->fromArray($chapterPresidentArray);
            $chapter_presidents->addItem($chapterPresident);
        }
        $object->chapter_presidents = $chapter_presidents;

        if(!isset($object->chapter_advisors)) {
            $object->chapter_advisors = new Collection();
        }
        if(!is_array($object->chapter_advisors)) {
            $object->chapter_advisors = new Collection();
        }
        $chapter_advisors = new Collection();
        foreach($object->chapter_advisors as $chapterAdvisor) {
            $chapterAdvisorArray = (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterAdvisor);
            $chapterAdvisor = new Contact();
            $chapterAdvisor->fromArray($chapterAdvisorArray);
            $chapter_advisors->addItem($chapterAdvisor);
        }
        $object->chapter_advisors = $chapter_advisors;

        if(!isset($object->ID)) {
            $object->contacts = new Collection();
        }

        if($object->ID === '') {
            $object->contacts = new Collection();
        }

        if($hydrate_contacts) {
            $contact_search = new ContactSearch();
            $contacts = $contact_search->get_all_from_chapter($object->ID);
            $object->contacts = new Collection();
            foreach($contacts as $contact) {
                $contactArray = (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $contact->ID);
                $contact = new Contact();
                $contact->fromArray($contactArray);
                $object->contacts->addItem($contact);
            }
        }

        return $object;

    }
}