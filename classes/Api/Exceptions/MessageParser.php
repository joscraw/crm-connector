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
     * @param \Exception $exception
     * @return mixed
     */
    public static function ParseMailChimpMessage(\Exception $exception)
    {

        $response = json_decode($exception->getResponse()->getBody()->getContents(), true);

        $errors = $response['errors'];

        $message = "";
        foreach ($errors as $error)
        {
            $message = isset($error['field']) ? $error['field'] . "<br>" : "";
            $message .= isset($error['message']) ? $error['message'] . "<br>" : "";
        }

        $message = empty($message) ? "System Error" : $message;

        return $message;
    }


}