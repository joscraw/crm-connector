<?php

namespace CRMConnector\Api\Models;


/**
 * Class Contact
 * @package CRMConnector\Api\Models
 */
class Contact
{

    /**
     * The company name for the list.
     * @var string
     */
    private $company;

    /**
     * The street address for the list contact.
     * @var string
     */
    private $address1;

    /**
     * The street address for the list contact.
     * @var string
     */
    private $address2;

    /**
     * The city for the list contact.
     * @var string
     */
    private $city;

    /**
     * The state for the list contact.
     * @var string
     */
    private $state;

    /**
     * The postal or zip code for the list contact.
     * @var string
     */
    private $zip;

    /**
     * A two-character ISO3166 country code. Defaults to US if invalid.
     * @var string
     */
    private $country;

    /**
     *The phone number for the list contact.
     * @var string
     */
    private $phone;

    /**
     * Array or errors which are attached when is_valid() gets called
     * @var array
     */
    private $errors = [];

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
     * @return array
     */
    public function toArray()
    {
        return [
            'company'   => $this->company,
            'address1'  => $this->address1,
            'address2'  => $this->address2,
            'city'      => $this->city,
            'state'     => $this->state,
            'zip'       => $this->zip,
            'country'   => $this->country,
            'phone'     => $this->phone,

        ];
    }

    /**
     * @return bool
     */
    public function is_valid()
    {
        $this->validate_company()
            ->validate_address_one()
            ->validate_city()
            ->validate_state()
            ->validate_zip()
            ->validate_country();
        return count($this->errors) === 0;
    }

    public function handle_request($request)
    {
        if(isset($request['company']))
        {
            $this->company = $request['company'];
        }

        if(isset($request['address1']))
        {
            $this->address1 = $request['address1'];
        }

        if(isset($request['address2']))
        {
            $this->address2 = $request['address2'];
        }

        if(isset($request['city']))
        {
            $this->city = $request['city'];
        }

        if(isset($request['state']))
        {
            $this->state = $request['state'];
        }

        if(isset($request['zip']))
        {
            $this->zip = $request['zip'];
        }

        if(isset($request['country']))
        {
            $this->country = $request['country'];
        }

        if(isset($request['phone']))
        {
            $this->phone = $request['phone'];
        }

    }

    private function validate_company()
    {
        if(empty($this->company))
        {
            $this->errors['company'][] = 'You must enter a Company.';
        }
        return $this;
    }

    private function validate_address_one()
    {
        if(empty($this->address1))
        {
            $this->errors['address1'][] = 'You must enter an Address.';
        }
        return $this;
    }

    private function validate_city()
    {
        if(empty($this->city))
        {
            $this->errors['city'][] = 'You must enter a City.';
        }
        return $this;
    }

    private function validate_state()
    {
        if(empty($this->state))
        {
            $this->errors['state'][] = 'You must enter a State.';
        }
        return $this;
    }

    private function validate_zip()
    {
        if(empty($this->zip))
        {
            $this->errors['zip'][] = 'You must enter a Zip.';
        }
        return $this;
    }

    private function validate_country()
    {
        if(empty($this->country))
        {
            $this->errors['country'][] = 'You must enter a Country.';
        }
        return $this;
    }


}