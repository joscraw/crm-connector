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
     * @param bool $hydrate_contact_chapter
     * @param bool $hydrate_chapter_chapter_invitations
     * @param bool $hydrate_chapter_contacts
     * @param $hydrate_chapter_chapter_officers
     * @param $hydrate_chapter_chapter_presidents
     * @param $hydrate_chapter_chapter_advisors
     * @param $hydrate_chapter_invitation_chapter
     * @param $hydrate_chapter_invitation_spring_drop
     * @param $hydrate_chapter_invitation_summer_drop
     * @param $hydrate_chapter_invitation_fall_drop
     * @param $hydrate_drop_chapter_invitations
     * @return mixed
     * @throws \Exception
     *
     */
    public static function toObject(
        array $array,
        $object,
        $hydrate_contact_chapter = false,
        $hydrate_chapter_chapter_invitations = false,
        $hydrate_chapter_contacts = false,
        $hydrate_chapter_chapter_officers = false,
        $hydrate_chapter_chapter_presidents = false,
        $hydrate_chapter_chapter_advisors = false,
        $hydrate_chapter_invitation_chapter = false,
        $hydrate_chapter_invitation_spring_drop = false,
        $hydrate_chapter_invitation_summer_drop = false,
        $hydrate_chapter_invitation_fall_drop = false,
        $hydrate_drop_chapter_invitations = false
    ) {


        if(!$object instanceof Hydratable) {
            throw new \Exception("Object must implement the Hydratable interface to by Hydrated.");
        }

        if(empty($array)) {
            return $object;
        }

        $object->fromArray($array);
        if($hydrate_contact_chapter) {
        if($object instanceof Contact) {

                // build the chapter
                $account_name = isset($array['account_name']) ? $array['account_name'] : '';
                $object->chapter = self::toObject(
                    (new ChapterSearch())->get_post_with_meta_values_from_post_id(ChapterSearch::POST_TYPE, $account_name),
                    new Chapter(),
                    $hydrate_contact_chapter,
                    $hydrate_chapter_chapter_invitations,
                    $hydrate_chapter_contacts,
                    $hydrate_chapter_chapter_officers,
                    $hydrate_chapter_chapter_presidents,
                    $hydrate_chapter_chapter_advisors,
                    $hydrate_chapter_invitation_chapter,
                    $hydrate_chapter_invitation_spring_drop,
                    $hydrate_chapter_invitation_summer_drop,
                    $hydrate_chapter_invitation_fall_drop,
                    $hydrate_drop_chapter_invitations
                );
            }
        }

        if($object instanceof Chapter) {

            if($hydrate_chapter_chapter_invitations) {
                // build the chapter Invitation
                $chapter_invitations = $object->get_chapter_invitations();
                $object->chapter_invitations = new Collection();
                foreach($chapter_invitations as $chapterInvitation) {
                    $object->chapter_invitations->addItem(
                        self::toObject(
                            (new ChapterInvitationSearch())->get_post_with_meta_values_from_post_id(ChapterInvitationSearch::POST_TYPE, $chapterInvitation),
                            new ChapterInvitation(),
                            $hydrate_contact_chapter,
                            $hydrate_chapter_chapter_invitations,
                            $hydrate_chapter_contacts,
                            $hydrate_chapter_chapter_officers,
                            $hydrate_chapter_chapter_presidents,
                            $hydrate_chapter_chapter_advisors,
                            $hydrate_chapter_invitation_chapter,
                            $hydrate_chapter_invitation_spring_drop,
                            $hydrate_chapter_invitation_summer_drop,
                            $hydrate_chapter_invitation_fall_drop,
                            $hydrate_drop_chapter_invitations
                        )
                    );
                }
            }

            if($hydrate_chapter_chapter_officers) {
                // build the chapter leadership
                $chapter_officers = $object->get_chapter_officers();
                $object->chapter_officers = new Collection();
                foreach($chapter_officers as $chapterOfficer) {
                    $object->chapter_officers->addItem(
                        self::toObject(
                            (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterOfficer),
                            new Contact(),
                            $hydrate_contact_chapter,
                            $hydrate_chapter_chapter_invitations,
                            $hydrate_chapter_contacts,
                            $hydrate_chapter_chapter_officers,
                            $hydrate_chapter_chapter_presidents,
                            $hydrate_chapter_chapter_advisors,
                            $hydrate_chapter_invitation_chapter,
                            $hydrate_chapter_invitation_spring_drop,
                            $hydrate_chapter_invitation_summer_drop,
                            $hydrate_chapter_invitation_fall_drop,
                            $hydrate_drop_chapter_invitations
                        )
                    );
                }
            }

            if($hydrate_chapter_chapter_presidents) {
                $chapter_presidents = $object->get_chapter_presidents();
                $object->chapter_presidents = new Collection();
                foreach($chapter_presidents as $chapterPresident) {
                    $object->chapter_presidents->addItem(
                        self::toObject(
                            (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterPresident),
                            new Contact(),
                            $hydrate_contact_chapter,
                            $hydrate_chapter_chapter_invitations,
                            $hydrate_chapter_contacts,
                            $hydrate_chapter_chapter_officers,
                            $hydrate_chapter_chapter_presidents,
                            $hydrate_chapter_chapter_advisors,
                            $hydrate_chapter_invitation_chapter,
                            $hydrate_chapter_invitation_spring_drop,
                            $hydrate_chapter_invitation_summer_drop,
                            $hydrate_chapter_invitation_fall_drop,
                            $hydrate_drop_chapter_invitations
                        )
                    );
                }
            }

            if($hydrate_chapter_chapter_advisors) {
                $chapter_advisors = $object->get_chapter_advisors();
                $object->chapter_advisors = new Collection();
                foreach($chapter_advisors as $chapterAdvisor) {
                    $object->chapter_advisors->addItem(
                        self::toObject(
                            (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $chapterAdvisor),
                            new Contact(),
                            $hydrate_contact_chapter,
                            $hydrate_chapter_chapter_invitations,
                            $hydrate_chapter_contacts,
                            $hydrate_chapter_chapter_officers,
                            $hydrate_chapter_chapter_presidents,
                            $hydrate_chapter_chapter_advisors,
                            $hydrate_chapter_invitation_chapter,
                            $hydrate_chapter_invitation_spring_drop,
                            $hydrate_chapter_invitation_summer_drop,
                            $hydrate_chapter_invitation_fall_drop,
                            $hydrate_drop_chapter_invitations
                        )
                    );
                }
            }

            if($hydrate_chapter_contacts) {
                // build all the contacts that belong to this chapter
                $contact_search = new ContactSearch();
                $contacts = $contact_search->get_all_from_chapter($array['ID']);
                $object->contacts = new Collection();
                foreach($contacts as $contact) {
                    $object->contacts->addItem(
                        self::toObject(
                            (new ContactSearch())->get_post_with_meta_values_from_post_id(ContactSearch::POST_TYPE, $contact->ID),
                            new Contact(),
                            $hydrate_contact_chapter,
                            $hydrate_chapter_chapter_invitations,
                            $hydrate_chapter_contacts,
                            $hydrate_chapter_chapter_officers,
                            $hydrate_chapter_chapter_presidents,
                            $hydrate_chapter_chapter_advisors,
                            $hydrate_chapter_invitation_chapter,
                            $hydrate_chapter_invitation_spring_drop,
                            $hydrate_chapter_invitation_summer_drop,
                            $hydrate_chapter_invitation_fall_drop,
                            $hydrate_drop_chapter_invitations
                        )
                    );
                }
            }
        }

        if($object instanceof ChapterInvitation) {

            if($hydrate_chapter_invitation_chapter) {
                // build the chapter
                $chapter = isset($array['chapter']) ? $array['chapter'] : '';
                $object->chapter = self::toObject(
                    (new ChapterSearch())->get_post_with_meta_values_from_post_id(ChapterSearch::POST_TYPE, $chapter),
                    new Chapter(),
                    $hydrate_contact_chapter,
                    $hydrate_chapter_chapter_invitations,
                    $hydrate_chapter_contacts,
                    $hydrate_chapter_chapter_officers,
                    $hydrate_chapter_chapter_presidents,
                    $hydrate_chapter_chapter_advisors,
                    $hydrate_chapter_invitation_chapter,
                    $hydrate_chapter_invitation_spring_drop,
                    $hydrate_chapter_invitation_summer_drop,
                    $hydrate_chapter_invitation_fall_drop,
                    $hydrate_drop_chapter_invitations
                );
            }

            if($hydrate_chapter_invitation_spring_drop) {
                // Build the Drops
                $drop = isset($array['spring_drop']) ? $array['spring_drop'] : '';
                $object->spring_drop = self::toObject(
                    (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                    new Drop(),
                    $hydrate_contact_chapter,
                    $hydrate_chapter_chapter_invitations,
                    $hydrate_chapter_contacts,
                    $hydrate_chapter_chapter_officers,
                    $hydrate_chapter_chapter_presidents,
                    $hydrate_chapter_chapter_advisors,
                    $hydrate_chapter_invitation_chapter,
                    $hydrate_chapter_invitation_spring_drop,
                    $hydrate_chapter_invitation_summer_drop,
                    $hydrate_chapter_invitation_fall_drop,
                    $hydrate_drop_chapter_invitations
                );
            }

            if($hydrate_chapter_invitation_summer_drop) {
                $drop = isset($array['summer_drop']) ? $array['summer_drop'] : '';
                $object->summer_drop = self::toObject(
                    (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                    new Drop(),
                    $hydrate_contact_chapter,
                    $hydrate_chapter_chapter_invitations,
                    $hydrate_chapter_contacts,
                    $hydrate_chapter_chapter_officers,
                    $hydrate_chapter_chapter_presidents,
                    $hydrate_chapter_chapter_advisors,
                    $hydrate_chapter_invitation_chapter,
                    $hydrate_chapter_invitation_spring_drop,
                    $hydrate_chapter_invitation_summer_drop,
                    $hydrate_chapter_invitation_fall_drop,
                    $hydrate_drop_chapter_invitations
                );
            }

            if($hydrate_chapter_invitation_fall_drop) {
                $drop = isset($array['fall_drop']) ? $array['fall_drop'] : '';
                $object->fall_drop = self::toObject(
                    (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                    new Drop(),
                    $hydrate_contact_chapter,
                    $hydrate_chapter_chapter_invitations,
                    $hydrate_chapter_contacts,
                    $hydrate_chapter_chapter_officers,
                    $hydrate_chapter_chapter_presidents,
                    $hydrate_chapter_chapter_advisors,
                    $hydrate_chapter_invitation_chapter,
                    $hydrate_chapter_invitation_spring_drop,
                    $hydrate_chapter_invitation_summer_drop,
                    $hydrate_chapter_invitation_fall_drop,
                    $hydrate_drop_chapter_invitations
                );
            }
        }

/*        if($object instanceof Drop) {

                // build the chapter Invitation
                $chapter_invitations = $object->get_chapter_invitations();
                $object->chapter_invitations = new Collection();
                foreach($chapter_invitations as $chapterInvitation) {
                    $object->chapter_invitations->addItem(
                        self::toObject(
                            (new ChapterInvitationSearch())->get_post_with_meta_values_from_post_id(ChapterInvitationSearch::POST_TYPE, $chapterInvitation),
                            new ChapterInvitation(),
                            $hydrate_contact_chapter,
                            $hydrate_chapter_chapter_invitations,
                            $hydrate_chapter_contacts,
                            $hydrate_chapter_chapter_officers,
                            $hydrate_chapter_chapter_presidents,
                            $hydrate_chapter_chapter_advisors,
                            $hydrate_chapter_invitation_chapter,
                            $hydrate_chapter_invitation_spring_drop,
                            $hydrate_chapter_invitation_summer_drop,
                            $hydrate_chapter_invitation_fall_drop,
                            false
                        )
                    );
                }
            }
        }*/

        return $object;
    }
}