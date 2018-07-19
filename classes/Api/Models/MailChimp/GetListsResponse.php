<?php

namespace CRMConnector\Api\Models\MailChimp;

/**
 * Class GetListsResponse
 * @package CRMConnector\Api\Models\MailChimp
 */
class GetListsResponse
{
    private $lists = [];
    private $total_items;

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
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

}