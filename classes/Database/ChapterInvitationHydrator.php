<?php
/**
 * Created by PhpStorm.
 * User: joshcrawmer
 * Date: 11/25/18
 * Time: 8:23 AM
 */

namespace CRMConnector\Database;

use CRMConnector\Models\Chapter;
use CRMConnector\Models\ChapterInvitation;
use CRMConnector\Models\Collection;
use CRMConnector\Models\Drop;

/**
 * Class ChapterInvitationHydrator
 * @package CRMConnector\Database
 */
class ChapterInvitationHydrator
{
    /**
     * @param array $array
     * @param $object
     * @return mixed
     */
    public static function toObject(array $array = [], $object) {

        if(!$object instanceof ChapterInvitation) {
            return $object;
        }

        $object->fromArray($array);

        if(isset($object->chapter) && $object->chapter !== '') {
            $chapter = new Chapter();
            $chapter->fromArray((new ChapterSearch())->get_post_with_meta_values_from_post_id(ChapterSearch::POST_TYPE, $object->chapter));
            $object->chapter = $chapter;
        } else {
            $object->chapter = null;
        }

        if(isset($object->spring_drop) && $object->spring_drop !== '') {
            $drop = new Drop();
            $drop->fromArray((new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $object->spring_drop));
            $object->spring_drop = $drop;
        } else {
            $object->spring_drop = null;
        }

        if(isset($object->summer_drop) && $object->summer_drop !== '') {
            $drop = new Drop();
            $drop->fromArray((new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $object->summer_drop));
            $object->summer_drop = $drop;
        } else {
            $object->summer_drop = null;
        }

        if(isset($object->fall_drop) && $object->fall_drop !== '') {
            $drop = new Drop();
            $drop->fromArray((new DropSearch())->get_post_with_meta_values_from_post_id(DropSearch::POST_TYPE, $object->fall_drop));
            $object->fall_drop = $drop;
        } else {
            $object->fall_drop = null;
        }

        return $object;
    }
}