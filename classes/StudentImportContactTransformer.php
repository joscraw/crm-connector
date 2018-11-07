<?php

namespace CRMConnector;

/**
 * Class StudentImportContactTransformer
 * @package CRMConnector
 */
class StudentImportContactTransformer
{
    /**
     * @var array
     */
    public static $default_columns = [
            'first_name',
            'last_name',
            'full_name',
            'email',
            'school_email',
            'contact_type',
            'permanent_address_1',
            'permanent_address_2',
            'permanent_address_3',
            'permanent_city',
            'permanent_state',
            'permanent_zip',
            'current_address_1',
            'current_address_2',
            'current_address_3',
            'current_city',
            'current_state',
            'current_zip',
        ];

    /**
     * Transform the record into a format used by the Custom Post Type to insert a record
     *
     * @param array $record
     * @return array
     */
    public function transform_record(array $record)
    {
        return [
            'first_name'            =>  isset($record['first_name']) ? trim($record['first_name']) : '',
            'last_name'             =>  isset($record['last_name']) ? trim($record['last_name']) : '',
            'full_name'             =>  trim($this->format_name($record)), // This should always have a value since a contact name is required
            'email'                 =>  isset($record['email']) ? trim($record['email']) : '',
            'school_email'          =>  isset($record['school_email']) ? trim($record['school_email']) : '',
            'contact_type'          =>  'Prospect',
            'permanent_address_1'   => isset($record['permanent_address_1']) ? trim($record['permanent_address_1']) : '',
            'permanent_address_2'   => isset($record['permanent_address_2']) ? trim($record['permanent_address_2']) : '',
            'permanent_address_3'   => isset($record['permanent_address_3']) ? trim($record['permanent_address_3']) : '',
            'permanent_city'        => isset($record['permanent_city']) ? trim($record['permanent_city']) : '',
            'permanent_state'       => isset($record['permanent_state']) ? trim($record['permanent_state']) : '',
            'permanent_zip'         => isset($record['permanent_zip']) ? trim($record['permanent_zip']) : '',
            'current_address_1'     => isset($record['current_address_1']) ? trim($record['current_address_1']) : '',
            'current_address_2'     => isset($record['current_address_2']) ? trim($record['current_address_2']) : '',
            'current_address_3'     => isset($record['current_address_3']) ? trim($record['current_address_3']) : '',
            'current_city'          => isset($record['current_city']) ? trim($record['current_city']) : '',
            'current_state'         => isset($record['current_state']) ? trim($record['current_state']) : '',
            'current_zip'           => isset($record['current_zip']) ? trim($record['current_zip']) : '',
        ];
    }

    /**
     * @param $record
     * @return string
     */
    private function format_name($record)
    {
        return sprintf("%s %s %s %s",
            isset($record['prefix']) ? trim($record['prefix']) : '',
            isset($record['first_name']) ? trim($record['first_name']) : '',
            isset($record['last_name']) ? trim($record['last_name']) : '',
            isset($record['suffix']) ? trim($record['suffix']) : '');

    }

}