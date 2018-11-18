<?php

namespace CRMConnector\Models;

use CRMConnector\Database\DatabaseAttributes;
use CRMConnector\DeDuplicate;

/**
 * Class Contact
 * @package CRMConnector\Models
 */
class Contact
{
    use DatabaseAttributes;

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

    public function is_prospect() {

        if(!isset($contact['contact_record_type'])) {
            return false;
        }

        if(stripos($contact['contact_record_type'], 'prospect') !== null) {
            return true;
        }

        return false;
    }
}