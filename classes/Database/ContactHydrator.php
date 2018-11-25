<?php

namespace CRMConnector\Database;

use CRMConnector\Models\Chapter;
use CRMConnector\Models\ChapterInvitation;
use CRMConnector\Models\Collection;
use CRMConnector\Models\Contact;
use CRMConnector\Models\Drop;

/**
 * Class ContactHydrator
 * @package CRMConnector\Database
 */
class ContactHydrator
{
    /**
     * @param array $array
     * @param $object
     * @return mixed
     */
    public static function toObject(array $array = [], $object) {

        if(!$object instanceof Contact) {
            return $object;
        }

        $object->fromArray($array);

        if(isset($object->account_name) && $object->account_name !== '') {
            $chapter = new Chapter();
            $chapter->fromArray((new ChapterSearch())->get_post_with_meta_values_from_post_id(ChapterSearch::POST_TYPE, $object->account_name));
            $object->chapter = $chapter;
        }

        return $object;
    }
}