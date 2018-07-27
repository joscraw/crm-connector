<?php

namespace CRMConnector\Api\Models;

/**
 * Class CampaignDefaults
 * @package CRMConnector\Api\Models
 */
class CampaignDefaults implements \JsonSerializable
{

    /**
     * The default from name for campaigns sent to this list.
     * @var string
     */
    private $from_name;

    /**
     * The default from email for campaigns sent to this list.
     * @var string
     */
    private $from_email;

    /**
     * The default subject line for campaigns sent to this list.
     * @var string
     */
    private $subject;

    /**
     * The default language for this lists’s forms.
     * @var string
     */
    private $language = "English";

    /**
     * Array or errors which are attached when is_valid() gets called
     * @var array
     */
    private $errors = [];



    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'from_name'     => $this->from_name,
            'from_email'    => $this->from_email,
            'subject'       => $this->subject,
            'language'      => $this->language,
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'from_name'     => $this->from_name,
            'from_email'    => $this->from_email,
            'subject'       => $this->subject,
            'language'      => $this->language,
        ];
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

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
            $this->$property = $value;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function is_valid()
    {
        $this->validate_from_name()
            ->validate_from_email()
            ->validate_subject();
        return count($this->errors) === 0;
    }

    public function handle_request($request)
    {
        if(isset($request['from_name']))
        {
            $this->from_name = $request['from_name'];
        }

        if(isset($request['from_email']))
        {
            $this->from_email = $request['from_email'];
        }

        if(isset($request['subject']))
        {
            $this->subject = $request['subject'];
        }

    }

    private function validate_from_name()
    {
        if(empty($this->from_name))
        {
            $this->errors['from_name'][] = 'You must enter a from name.';
        }

        if(strpos($this->from_name, '@'))
        {
            $this->errors['from_name'][] = 'Can\'t contain @ symbol in from name';
        }

        return $this;
    }

    private function validate_from_email()
    {
        if(empty($this->from_email))
        {
            $this->errors['from_email'][] = 'You must enter a from email.';
        }
        return $this;
    }

    private function validate_subject()
    {
        if(empty($this->subject))
        {
            $this->errors['subject'][] = 'You must enter a subject.';
        }
        return $this;
    }
}