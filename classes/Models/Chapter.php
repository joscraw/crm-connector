<?php

namespace CRMConnector\Models;

use ArrayIterator;
use CRMConnector\Database\Hydratable;

/**
 * Class Chapter
 * @package CRMConnector\Models
 */
class Chapter implements Hydratable
{
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {

        $this->$property = $value;

        return $this;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function fromArray(array $array)
    {
        foreach($array as $key => $value)
        {
            $this->$key = $value;
        }
    }

    // todo I don't know how they want me to grab the chapter invitation
    public function get_most_recent_chapter_invitation() {

       /* if(!isset($this->chapter_invitations)) {
            return null;
        }

        if(!is_array($this->chapter_invitations)) {
            return null;
        }

        if(count($this->chapter_invitations) === 0) {
            return null;
        }*/

        $iter = $this->chapter_invitations->getIterator();
        $array = iterator_to_array($iter);

        usort($array, function ($a, $b) {
            return $a->ID < $b->ID;
        });

        return isset($array[0]) ? $array[0] : null;
    }

    public function get_chapter_invitations() {

        if(!isset($this->chapter_invitations)) {
            return [];
        }

        if(!is_array($this->chapter_invitations)) {
            return [];
        }

        return $this->chapter_invitations;

    }

    public function get_chapter_officers() {

        if(!isset($this->chapter_officers)) {
            return [];
        }

        if(!is_array($this->chapter_officers)) {
            return [];
        }

        if(count($this->chapter_officers) === 0) {
            return [];
        }

        return $this->chapter_officers;
    }

    public function get_chapter_presidents() {

        if(!isset($this->chapter_presidents)) {
            return [];
        }

        if(!is_array($this->chapter_presidents)) {
            return [];
        }

        if(count($this->chapter_presidents) === 0) {
            return [];
        }

        return $this->chapter_presidents;
    }

    public function get_chapter_advisors() {

        if(!isset($this->chapter_advisors)) {
            return [];
        }

        if(!is_array($this->chapter_advisors)) {
            return [];
        }

        if(count($this->chapter_advisors) === 0) {
            return [];
        }

        return $this->chapter_advisors;
    }
}