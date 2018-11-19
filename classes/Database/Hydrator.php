<?php

namespace CRMConnector\Database;

use CRMConnector\Models\Chapter;
use CRMConnector\Models\ChapterInvitation;
use CRMConnector\Models\Collection;
use CRMConnector\Models\Contact;
use CRMConnector\Models\Drop;
use CRMConnector\Database\ChapterSearch;
use CRMConnector\Database\ChapterInvitationSearch;

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
     * @return mixed
     * @throws \Exception
     */
    public static function toObject(array $array, $object, $build_relational_post_objects = false) {

        if(!$object instanceof Hydratable) {
            throw new \Exception("Object must implement the Hydratable interface to by Hydrated.");
        }

        $object->fromArray($array);

        if($object instanceof Contact && $build_relational_post_objects) {
                // build the chapter
                $account_name = isset($array['account_name']) ? $array['account_name'] : '';
                $object->chapter = self::toObject(
                    (new ChapterSearch())->get_post_with_meta_values_from_post_id(ChapterSearch::POST_TYPE, $account_name),
                    new Chapter(),
                    true
                );
        }

        if($object instanceof Chapter && $build_relational_post_objects) {
            // build the chapter Invitation
            $chapter_invitations = $object->get_chapter_invitations();
            $object->chapter_invitations = new Collection();
            foreach($chapter_invitations as $chapterInvitation) {
                    $object->chapter_invitations->addItem(
                        self::toObject(
                            (new ChapterInvitationSearch())->get_post_with_meta_values_from_post_id(ChapterInvitationSearch::POST_TYPE, $chapterInvitation),
                            new ChapterInvitation(),
                            true
                        )
                    );
            }

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

        if($object instanceof ChapterInvitation && $build_relational_post_objects) {

            // Build the Drops
            $drop = isset($array['spring_drop']) ? $array['spring_drop'] : '';
            $object->spring_drop = self::toObject(
                (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                new Drop(),
                true
            );

            $drop = isset($array['summer_drop']) ? $array['summer_drop'] : '';
            $object->summer_drop = self::toObject(
                    (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                    new Drop(),
                    true
            );

            $drop = isset($array['fall_drop']) ? $array['fall_drop'] : '';
            $object->fall_drop = self::toObject(
                (new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $drop),
                new Drop(),
                true
            );
        }

        return $object;
    }
}