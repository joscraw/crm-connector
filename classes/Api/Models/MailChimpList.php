<?php

namespace CRMConnector\Api\Models;


/**
 * Class MailChimpList
 * @package CRMConnector\Api\Models
 */
class MailChimpList implements \JsonSerializable
{

    /**
     * The name of the list.
     * @var string
     */
    private $name;

    /**
     * Contact information displayed in campaign footers
     * to comply with international spam laws.
     * @see https://kb.mailchimp.com/lists/growth/about-the-required-email-footer-content?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs&_ga=2.18906616.1157657947.1531760872-769419101.1517934903
     * @var Contact
     */
    private $contact;

    /**
     * The permission reminder for the list.
     * @see https://kb.mailchimp.com/accounts/compliance-tips/edit-the-permission-reminder?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs&_ga=2.18906616.1157657947.1531760872-769419101.1517934903
     * @var string
     */
    private $permission_reminder;

    /**
     * Whether campaigns for this list use the Archive Bar in archives by default.
     * @see https://kb.mailchimp.com/campaigns/archives/about-the-archive-bar?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs&_ga=2.34763489.1157657947.1531760872-769419101.1517934903
     * @var boolean
     */
    private $use_archive_bar;

    /**
     * Default values for campaigns created for this list.
     * @see https://kb.mailchimp.com/campaigns/design/set-up-email-subject-from-name-and-from-email-address-on-a-campaign?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs&_ga=2.227676637.1157657947.1531760872-769419101.1517934903
     * @var CampaignDefaults
     */
    private $campaign_defaults;

    /**
     * The email address to send subscribe notifications to.
     * @see https://kb.mailchimp.com/lists/managing-subscribers/change-subscribe-and-unsubscribe-notifications?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs&_ga=2.23689594.1157657947.1531760872-769419101.1517934903
     * @var string
     */
    private $notify_on_subscribe;

    /**
     * The email address to send unsubscribe notifications to.
     * @see https://kb.mailchimp.com/lists/managing-subscribers/change-subscribe-and-unsubscribe-notifications?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs&_ga=2.23689594.1157657947.1531760872-769419101.1517934903
     * @var string
     */
    private $notify_on_unsubscribe;

    /**
     * Whether the list supports multiple formats for emails. When set to true, subscribers can choose whether they want to receive HTML or plain-text emails. When set to false, subscribers will receive HTML emails, with a plain-text alternative backup.
     * @see https://kb.mailchimp.com/lists/growth/how-to-change-list-name-and-defaults?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs&_ga=2.220936408.1157657947.1531760872-769419101.1517934903#Change-Subscription-Settings
     * @var boolean
     */
    private $email_type_option = true;

    /**
     * Whether this list is public or private. Possible Values: pub|prv
     * @var string
     */
    private $visibilty;

    /**
     * Array or errors which are attached when is_valid() gets called
     * @var array
     */
    private $errors = [];

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
            'name'                  =>  $this->name,
            'permission_reminder'   =>  $this->permission_reminder,
            'email_type_option'     =>  $this->email_type_option,
            'contact'               =>  $this->contact->toArray(),
            'campaign_defaults'     =>  $this->campaign_defaults->toArray(),
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
            'name'                  =>  $this->name,
            'permission_reminder'   =>  $this->permission_reminder,
            'email_type_option'     =>  $this->email_type_option,
            'contact'               =>  $this->contact,
            'campaign_defaults'     =>  $this->campaign_defaults,
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
     *
     * @param bool $validate_children
     * @return bool
     */
    public function is_valid($validate_children = true)
    {
        // validate any children objects
        if($validate_children)
        {
            if(!$this->contact->is_valid())
                $this->errors = ($this->errors + $this->contact->errors());

            if(!$this->campaign_defaults->is_valid())
                $this->errors = ($this->errors + $this->campaign_defaults->errors());
        }
        $this->validate_name()
            ->validate_permission_reminder();

        return count($this->errors) === 0;
    }

    public function handle_request($request)
    {
        if(isset($request['list_name']))
        {
            $this->name = $request['list_name'];
        }

        if(isset($request['permission_reminder']))
        {
            $this->permission_reminder = $request['permission_reminder'];
        }

        if($this->contact)
        {
            $this->contact->handle_request($request);
        }

        if($this->campaign_defaults)
        {
            $this->campaign_defaults->handle_request($request);
        }
    }

    private function validate_name()
    {
        if(empty($this->name))
        {
            $this->errors['list_name'][] = 'You must enter a List Name.';
        }
        return $this;
    }

    private function validate_permission_reminder()
    {
        if(empty($this->permission_reminder))
        {
            $this->errors['permission_reminder'][] = 'You must enter in a permission reminder';
        }
        return $this;
    }
}