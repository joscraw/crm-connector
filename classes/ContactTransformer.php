<?php


namespace CRMConnector;

/**
 * Class ContactTransformer
 * @package CRMConnector
 */
trait ContactTransformer
{
    /**
     * Transform the record into a format used by the Custom Post Type to insert a record
     *
     * @param array $record
     * @return array
     */
    public function transform_record(array $record)
    {
        return [
            // default key value pairs for the contact mapping from salesforce into our CRM
        ];

    }
}