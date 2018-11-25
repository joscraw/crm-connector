<?php
/**
 * Created by PhpStorm.
 * User: joshcrawmer
 * Date: 11/25/18
 * Time: 8:23 AM
 */

namespace CRMConnector\Database;

use CRMConnector\Models\ChapterInvitation;
use CRMConnector\Models\Collection;
use CRMConnector\Models\Drop;

/**
 * Class DropHydrator
 * @package CRMConnector\Database
 */
class DropHydrator
{
    /**
     * @param array $array
     * @param $object
     * @return mixed
     */
    public static function toObject(array $array = [], $object) {

        if(!$object instanceof Drop) {
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

        return $object;

    }
}