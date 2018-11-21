<?php

namespace CRMConnector\Database;

use CRMConnector\Models\Chapter;
use CRMConnector\Models\ChapterInvitation;
use CRMConnector\Models\Collection;
use CRMConnector\Models\Contact;
use CRMConnector\Models\Drop;
use CRMConnector\Database\ChapterSearch;
use CRMConnector\Database\ChapterInvitationSearch;
use CRMConnector\Database\ContactSearch;

/**
 * Class Hydrator
 * @package CRMConnector\Database
 * @author Josh Crawmer <jcrawmer@edoutcome.com>
 *
 */
class Hydrator
{
    /**
     * @param array $array
     * @param $object
     * @param bool $build_relational_post_objects Whether or not you want this function to look for relational post objects and create them
     * @param array $suppress numbered array of which relationships to suppress. This prevents certain hydrations from being performed in an infinte loop
     * @return mixed
     * @throws \Exception
     */
    public static function toObject(array $array, $object, $build_relational_post_objects = false, $suppress = [] ) {

        if(!$object instanceof Hydratable) {
            throw new \Exception("Object must implement the Hydratable interface to by Hydrated.");
        }

        $object->fromArray($array);

        if($object instanceof Contact && $build_relational_post_objects) {

            if(!in_array(0, $suppress)) {
                // build the chapter
                $account_name = isset($array['account_name']) ? $array['account_name'] : '';
                $object->chapter = self::toObject(
                    (new ChapterSearch())->get_post_with_meta_values_from_post_id(ChapterSearch::POST_TYPE, $account_name),
                    new Chapter(),
                    true
                );
            }
        }

        if($object instanceof Chapter && $build_relational_post_objects) {

            if(!in_array(0, $suppress)) {
                // build the chapter Invitation
                $chapter_invitations = $object->get_chapter_invitations();
                $object->chapter_invitations = new Collection();
                foreach($chapter_invitations as $chapterInvitation) {
                    $object->chapter_invitations->addItem(
                        self::toObject(
                            (new ChapterInvitationSearch())->get_post_with_meta_values_from_post_id(ChapterInvitationSearch::POST_TYPE, $chapterInvitation),
                            new ChapterInvitation(),
                            true,
                            [0]
                        )
                    );
                }
            }

            if(!in_array(1, $suppress)) {
                // build the chapter leadership
                $chapter_officers = $object->get_chapter_officers();
                $object->chapter_officers = new Collection();
                foreach($chapter_officers as $chapterOfficer) {
                    $object->chapter_officers->addItem(
                        self::toObject(
                            (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterOfficer),
                            new Contact(),
                            false
                        )
                    );
                }
            }

            if(!in_array(2, $suppress)) {
                $chapter_presidents = $object->get_chapter_presidents();
                $object->chapter_presidents = new Collection();
                foreach($chapter_presidents as $chapterPresident) {
                    $object->chapter_presidents->addItem(
                        self::toObject(
                            (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterPresident),
                            new Contact(),
                            false
                        )
                    );
                }
            }

            if(!in_array(3, $suppress)) {
                $chapter_advisors = $object->get_chapter_advisors();
                $object->chapter_advisors = new Collection();
                foreach($chapter_advisors as $chapterAdvisor) {
                    $object->chapter_advisors->addItem(
                        self::toObject(
                            (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterAdvisor),
                            new Contact(),
                            false
                        )
                    );
                }
            }

            if(!in_array(4, $suppress)) {
                // build all the contacts that belong to this chapter
                $contact_search = new ContactSearch();
                $contacts = $contact_search->get_all_from_chapter($array['ID']);
                $object->contacts = new Collection();
                foreach($contacts as $contact) {
                    $object->contacts->addItem(
                        self::toObject(
                            (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $contact->ID),
                            new Contact(),
                            false
                        )
                    );
                }
            }
        }

        if($object instanceof ChapterInvitation && $build_relational_post_objects) {

            if(!in_array(0, $suppress)) {
                // build the chapter
                $chapter = isset($array['chapter']) ? $array['chapter'] : '';
                $object->chapter = self::toObject(
                    (new ChapterSearch())->get_post_with_meta_values_from_post_id(ChapterSearch::POST_TYPE, $chapter),
                    new Chapter(),
                    true,
                    [0]
                );
            }

            if(!in_array(1, $suppress)) {
                // Build the Drops
                $drop = isset($array['spring_drop']) ? $array['spring_drop'] : '';
                $object->spring_drop = self::toObject(
                    (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                    new Drop(),
                    false
                );
            }

            if(!in_array(2, $suppress)) {
                $drop = isset($array['summer_drop']) ? $array['summer_drop'] : '';
                $object->summer_drop = self::toObject(
                    (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                    new Drop(),
                    false
                );
            }

            if(!in_array(3, $suppress)) {
                $drop = isset($array['fall_drop']) ? $array['fall_drop'] : '';
                $object->fall_drop = self::toObject(
                    (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                    new Drop(),
                    false
                );
            }
        }

        if($object instanceof Drop && $build_relational_post_objects) {

            if(!in_array(0, $suppress)) {
                // build the chapter Invitation
                $chapter_invitations = $object->get_chapter_invitations();
                $object->chapter_invitations = new Collection();
                foreach($chapter_invitations as $chapterInvitation) {
                    $object->chapter_invitations->addItem(
                        self::toObject(
                            (new ChapterInvitationSearch())->get_post_with_meta_values_from_post_id(ChapterInvitationSearch::POST_TYPE, $chapterInvitation),
                            new ChapterInvitation(),
                            true,
                            []
                        )
                    );
                }
            }
        }

        return $object;
    }
}