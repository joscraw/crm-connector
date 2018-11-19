<?php

namespace CRMConnector\Models;

use CRMConnector\Database\Hydratable;

/**
 * Class Drop
 * @package CRMConnector\Models
 */
class Drop implements Hydratable
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

    public function get_deadline_wave_one() {
        return (!empty($this->deadline_wave_one) ? date_create_from_format('Ymd', $this->deadline_wave_one)->format("m/d/Y") : null);
    }

    public function get_deadline_wave_two() {
        return (!empty($this->deadline_wave_two) ? date_create_from_format('Ymd', $this->deadline_wave_two)->format("m/d/Y") : null);
    }

    public function get_deadline_wave_three() {
        return (!empty($this->deadline_wave_three) ? date_create_from_format('Ymd', $this->deadline_wave_three)->format("m/d/Y") : null);
    }


}