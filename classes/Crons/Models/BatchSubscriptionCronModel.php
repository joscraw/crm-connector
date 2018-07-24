<?php

namespace CRMConnector\Crons\Models;

/**
 * Class BatchSubscriptionCron
 */
class BatchSubscriptionCronModel
{

    /**
     * @var int
     */
    private $import_id;

    /**
     * @var int
     */
    private $export_id;

    /**
     * @var int
     */
    private $list_id;

    /**
     * @var string
     */
    private $type;

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
    public function getImportId()
    {
        return $this->import_id;
    }

    /**
     * @param mixed $import_id
     */
    public function setImportId($import_id)
    {
        $this->import_id = $import_id;
    }

    /**
     * @return mixed
     */
    public function getExportId()
    {
        return $this->export_id;
    }

    /**
     * @param mixed $export_id
     */
    public function setExportId($export_id)
    {
        $this->export_id = $export_id;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }



    /**
     * @return bool
     */
    public function is_valid()
    {
        $this->validate_list_id()
            ->validate_export_import_id()
            ->validate_type();

        return count($this->errors) === 0;
    }

    public function handle_request($request)
    {
        if(isset($request['list_id']))
        {
            $this->list_id = sanitize_text_field($request['list_id']);
        }

        if(isset($request['import_id']))
        {
            $this->import_id = sanitize_text_field($request['import_id']);
        }

        if(isset($request['export_id']))
        {
            $this->export_id = sanitize_text_field($request['export_id']);
        }

        if(isset($request['type']))
        {
            $this->type = sanitize_text_field($request['type']);
        }

    }

    private function validate_list_id()
    {
        if(empty($this->list_id))
        {
            $this->errors['main'][] = 'Invalid form submission.';
        }
        return $this;
    }

    private function validate_export_import_id()
    {
        if(empty($this->import_id) && empty($this->export_id))
        {
            $this->errors['main'][] = 'Invalid form submission.';
        }
        return $this;
    }

    private function validate_type()
    {
        if(empty($this->type) || !in_array($this->type, ['subscribed', 'unsubscribed']))
        {
            $this->errors['main'][] = 'Invalid form submission.';
        }
        return $this;
    }


}