<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class SetChapterAdvisor
 * @package CRMConnector\Workflows\Sub
 */
class SetChapterAdvisor implements SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {
        if(empty($args[0]['contact'][0])) {
            return;
        }
        if(empty($args[0]['chapter'][0])) {
            return;
        }
        if(empty($args[0]['type'][0])) {
            return;
        }
        if(empty($args[0]['position'][0])) {
            return;
        }
        $contact = $args[0]['contact'][0];
        $chapter = $args[0]['chapter'][0];
        $type = $args[0]['type'][0];
        $position = $args[0]['position'][0];

        if(stripos($type, 'Advisor') !== false && stripos($position, 'Advisor') !== false) {
            update_post_meta($chapter, 'chapter_advisor_', $contact);
            return;
        }
    }
}