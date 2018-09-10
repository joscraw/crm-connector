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
            'full_name'     =>  trim($this->format_name($record)), // This should always have a value since a contact name is required
            'email'         =>  isset($record['Personal Email']) ? trim($record['Personal Email']) : '',
            'school_email'         =>  isset($record['School Email']) ? trim($record['School Email']) : '',
            'contact_type'  =>  'Prospect',
        ];

        //School Name,Contact Type,Chapter Invitation ID,Prospect Load Date,Prefix,Suffix,Current Address 1,Current Address 2,Current Address 3,Current City,Current State,Current Zip,Current Country,Permanent Address 1,Permanent Address 2,Permanent Address 3,Permanent City,Permanent State,Permanent Zip,Permanent Country,Personal Email,School Email,Expected Graduation Date,GPA,Major,Minor,Mobile,Phone,College/University ID,International Student,Nationality,Join Date

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