<?php

namespace CRMConnector\Models;

use CRMConnector\Database\DatabaseAttributes;
use CRMConnector\Database\Hydratable;


/**
 * Class Contact
 * @package CRMConnector\Models
 */
class Contact implements Hydratable
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

    public function fromArray(array $array)
    {
        foreach($array as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function is_prospect() {

        if(!isset($this->contact_record_type)) {
            return false;
        }

        if(stripos($this->contact_record_type, 'prospect') !== null) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function do_not_mail() {

        if(!isset($this->do_not_mail)) {
            return false;
        }

        if($this->do_not_mail === "") {
            return false;
        }

        if((bool) $this->do_not_mail === true) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function has_valid_address() {

        $permanent_address_valid = true;
        $current_address_valid = true;

        if(!isset($this->permanent_address_1)) {
            $permanent_address_valid = false;
        }

        if(isset($this->permanent_address_1) && (bool) $this->permanent_address_bad === true) {
            $permanent_address_valid = false;
        }

        if(!isset($this->current_address_1)) {
            $current_address_valid = false;
        }

        if(isset($this->current_address_bad) && (bool) $this->current_address_bad === true) {
            $current_address_valid = false;
        }

        return $permanent_address_valid || $current_address_valid;
    }

    public function get_prospect_load_date() {

        if(!empty($this->prospect_load_date)) {
            $datetime = new \DateTime();
            return $datetime->createFromFormat('!Ymd', $this->prospect_load_date);
        }

        if(!empty($this->post_date)) {
            return new \DateTime($this->post_date);
        }

        return false;
    }
}