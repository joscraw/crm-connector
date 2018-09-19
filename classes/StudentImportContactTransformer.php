<?php

namespace CRMConnector;

/**
 * Class StudentImportContactTransformer
 * @package CRMConnector
 */
trait StudentImportContactTransformer
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
            'full_name'             =>  trim($this->format_name($record)), // This should always have a value since a contact name is required
            'email'                 =>  isset($record['Personal Email']) ? trim($record['Personal Email']) : '',
            'school_email'          =>  isset($record['School Email']) ? trim($record['School Email']) : '',
            'contact_type'          =>  'Prospect',
            'permanent_address_1'   => isset($record['Permanent Address 1']) ? trim($record['Permanent Address 1']) : '',
            'permanent_address_2'   => isset($record['Permanent Address 2']) ? trim($record['Permanent Address 2']) : '',
            'permanent_address_3'   => isset($record['Permanent Address 3']) ? trim($record['Permanent Address 3']) : '',
            'permanent_city'        => isset($record['Permanent City']) ? trim($record['Permanent City']) : '',
            'permanent_state'       => isset($record['Permanent State']) ? trim($record['Permanent State']) : '',
            'permanent_zip'         => isset($record['Permanent Zip']) ? trim($record['Permanent Zip']) : '',
            'current_address_1'     => isset($record['Current Address 1']) ? trim($record['Current Address 1']) : '',
            'current_address_2'     => isset($record['Current Address 2']) ? trim($record['Current Address 2']) : '',
            'current_address_3'     => isset($record['Current Address 3']) ? trim($record['Current Address 3']) : '',
            'current_city'          => isset($record['Current City']) ? trim($record['Current City']) : '',
            'current_state'         => isset($record['Current State']) ? trim($record['Current State']) : '',
            'current_zip'           => isset($record['Current Zip']) ? trim($record['Current Zip']) : '',
        ];
    }

    /**
     * @param $record
     * @return string
     */
    private function format_name($record)
    {
        return sprintf("%s %s %s %s",
            isset($record['Prefix']) ? trim($record['Prefix']) : '',
            isset($record['First Name']) ? trim($record['First Name']) : '',
            isset($record['Last Name']) ? trim($record['Last Name']) : '',
            isset($record['Suffix']) ? trim($record['Suffix']) : '');

    }

}