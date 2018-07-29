<?php

namespace CRMConnector\Crons\Models;

/**
 * Class BatchListExportCronModel
 * @package CRMConnector\Crons\Models
 */
class BatchListExportCronModel
{

    /**
     * @var int
     */
    private $list_id;

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
     * @return mixed
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * @param mixed $list_id
     */
    public function setListId($list_id)
    {
        $this->list_id = $list_id;
    }

    /**
     * @return bool
     */
    public function is_valid()
    {
        $this->validate_list_id()
            ->validate_mailchimp_creds();

        return count($this->errors) === 0;
    }

    private function validate_list_id()
    {
        if(empty($this->list_id))
        {
            $this->errors[] = 'Invalid form submission.';
        }

        return $this;
    }

    private function validate_mailchimp_creds()
    {
        $settings = get_option( 'crmc_settings' );

        if(!isset($settings['mailchimp_username']) || !isset($settings['mailchimp_api_key']) || $settings['mailchimp_username'] === '' || $settings['mailchimp_api_key'] === '')
        {
            $this->errors[] = 'Your MailChimp Username and API Key Must Be Set Before This List Gets Synced To MailChimp! Add Those and Then Come Back and Re-Export This List!';
        }

        return $this;
    }
}