<?php


namespace CRMConnector\Service\CustomPostType\Contact;

/**
 * Class ContactTransformer
 * @package CRMConnector\Service\CustomPostType\Contact
 */
class ContactTransformer
{

    private function __construct(){}

    /**
     * Transform the record into a format used by the Custom Post Type to insert a record
     *
     * @param array $record
     * @return array
     */
    public static function transform_record(array $record)
    {
        return [
            'full_name'     =>  self::format_name($record), // This should always have a value since a contact name is required
            'account_name'  =>  isset($record['chapter_id']) ? $record['chapter_id'] : '', // This should always have a value since a contact name is required on the front end
            'email'         =>  isset($record['Personal Email']) ? $record['Personal Email'] : '', // This should always have a value since a contact email is required
        ];

        //School Name,Contact Type,Chapter Invitation ID,Prospect Load Date,Prefix,Suffix,Current Address 1,Current Address 2,Current Address 3,Current City,Current State,Current Zip,Current Country,Permanent Address 1,Permanent Address 2,Permanent Address 3,Permanent City,Permanent State,Permanent Zip,Permanent Country,Personal Email,School Email,Expected Graduation Date,GPA,Major,Minor,Mobile,Phone,College/University ID,International Student,Nationality,Join Date

    }

    /**
     * @param $record
     * @return string
     */
    private static function format_name($record)
    {
        return sprintf("%s %s %s %s",
            isset($record['Prefix']) ? $record['Prefix'] : '',
            isset($record['First Name']) ? $record['First Name'] : '',
            isset($record['Last Name']) ? $record['Last Name'] : '',
            isset($record['Suffix']) ? $record['Suffix'] : '');

    }
}