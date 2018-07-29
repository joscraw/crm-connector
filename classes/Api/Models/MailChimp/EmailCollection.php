<?php

namespace CRMConnector\Api\Models\MailChimp;


class EmailCollection
{

    private $emails = [];


    public function addEmail($email_address)
    {
        $this->emails[] = (object) ['email_address' => $email_address, 'status' => 'subscribed'];

        return $this;
    }

    public function getEmails()
    {
        return $this->emails;
    }

    public function de_duplicate()
    {
        $this->emails = array_unique($this->emails, SORT_REGULAR);

        return $this->emails;
    }

    public function remove_by_email($email_address)
    {
        foreach($this->emails as $key => $email)
        {
            if($email->email_address === $email_address)
                unset($this->emails[$key]);
        }
        return $this->emails;
    }

}