<?php

namespace CRMConnector\Api\Models\MailChimp;


/**
 * Class Template
 * @package CRMConnector\Api\Models\MailChimp
 */
class Template
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $html;

    /**
     * @var array
     */
    private $errors;

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
            $this->name,
            $this->html,
        ];
    }

    /**
     * Converts the data to an array used in the view
     */
    public function to_array_for_view()
    {
        return [
            'name' => $this->name,
            'template_html' => $this->html,
        ];
    }

    /**
     * The /templates API call requires the data
     * gets passed up as a object. Normally I would use the entire
     * template object but I don't want non necessary properties to be passed up
     * such as $errors
     *
     * @return \stdClass
     */
    public function toObject()
    {
        $obj = new \stdClass();
        $obj->name = $this->name;
        $obj->html = $this->html;

        return $obj;
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     *
     * @param bool $validate_children
     * @return bool
     */
    public function is_valid($validate_children = true)
    {
        $this->validate_name()
            ->validate_html();

        return count($this->errors) === 0;
    }

    public function handle_request($request)
    {
        if(isset($request['template_name']))
        {
            $this->name = $request['template_name'];
        }

        if(isset($request['template_html']))
        {
            $this->html = stripslashes($request['template_html']);
        }
    }

    private function validate_name()
    {
        if(empty($this->name))
        {
            $this->errors['template_name'][] = 'You must enter a Template Name.';
        }
        return $this;
    }

    private function validate_html()
    {
        if(empty($this->html))
        {
            $this->errors['template_html'][] = 'You must enter in some html for your email';
        }
        return $this;
    }


}