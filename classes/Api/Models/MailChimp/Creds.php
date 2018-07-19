<?php

namespace CRMConnector\Api\Models\MailChimp;

/**
 * Class Creds
 * @package CRMConnector\Api\Models\MailChimp
 */
class Creds
{

    /**
     * @var string
     */
    private $api_key;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $data_center;

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    /**
     * @param $property
     * @param $value
     * @return $this
     */
    public function __set($property, $value) {
        if (property_exists($this, $property)) {

            if($property === 'api_key')
                $this->data_center = substr($value, strpos($value, "-") + 1);

            $this->$property = $value;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            $this->username,
            $this->api_key,
        ];
    }


}