<?php

namespace CRMConnector\Api\Exceptions;

/**
 * Class MessageParser
 * @package CRMConnector\Api\Exceptions\
 */
class MessageParser
{

    /**
     * Looks at a mailchimp exception message and determines
     * The best User Friendly Response to Render
     *
     * @param $message
     * @return mixed
     */
    public static function ParseMailChimpMessage($message)
    {
        $message_map = [
            ['search_for' => ['Property must have type set'], 'message' =>  'Whoops! You forgot to set a Data Type one one or more of your Properties!'],
            ['search_for' => ['API Key Invalid'], 'message' =>  'Whoops, You need to enter in a valid API Key!'],
            ['search_for' => ['api.mailchimp.com', 'Could not resolve host'], 'message' =>  'Whoops, You need to enter in a valid API Key!'],
        ];

        foreach($message_map as $key => $map)
        {
            $matches = 0;
            foreach($map['search_for'] as $search_term)
            {
                if(strpos($message, $search_term) !== false){
                    $matches++;
                }
            }
            if(count($map['search_for']) === $matches)
            {
                return $map['message'];
            }
        }

        return 'System Error';

    }


}