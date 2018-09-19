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
    use DeDuplicate;

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

    /**
     * @return bool
     */
    public function isDuplicate()
    {
        if($this->almost_certain_duplicate($this) ||
        $this->very_likely_duplicate($this) ||
        $this->needs_strong_review_duplicate($this) ||
        $this->potential_duplicate($this))
        {
            return true;
        }

        return false;
    }
}