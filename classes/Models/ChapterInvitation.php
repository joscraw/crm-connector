<?php

namespace CRMConnector\Models;

/**
 * Class ChapterInvitation
 * @package CRMConnector\Models
 */
class ChapterInvitation
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

    public function fromArray($arr)
    {
        foreach($arr as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function get_chapter_officer_ids() {

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

    public function get_chapter_president_ids() {

        if(!isset($this->chapter_president)) {
            return [];
        }

        if(!is_array($this->chapter_president)) {
            return [];
        }

        if(count($this->chapter_president) === 0) {
            return [];
        }

        return $this->chapter_president;
    }

    public function get_chapter_advisor_ids() {

        if(!isset($this->advisors)) {
            return [];
        }

        if(!is_array($this->advisors)) {
            return [];
        }

        if(count($this->advisors) === 0) {
            return [];
        }

        return $this->advisors;
    }
}