<?php

namespace CRMConnector;

use CRMConnector\Database\ContactSearch;
use CRMConnector\Database\DatabaseAttributes;
use CRMConnector\Models\Contact;

/**
 * 1. Check if another email exists in the system. If it does then flag it. and add it to the flagged contacts post type
 * 2. Check if another user exists in the system with the same address. If it does then flag it and add it to the flagged contacts post type
 *
 * Class DeDuplicate
 * @package CRMConnector
 */
trait DeDuplicate
{
    /**
     * @param Contact $contact
     * @return bool
     */
    public function almost_certain_duplicate(Contact $contact)
    {
        if($contact->email && $contact->school_email && $contact->account_name && $contact->full_name)
        {
            $args = array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'email',
                            'value' => $contact->email,
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'school_email',
                            'value' => $contact->school_email,
                            'compare' => '=',
                        )
                    ),
                    array(
                        'key' => 'account_name',
                        'value' => $contact->account_name,
                        'compare' => '=',
                    )
                );

            foreach(explode(' ', $contact->full_name) as $part)
            {
                $args[] = [
                    'key' => 'full_name',
                    'value' => $part,
                    'compare' => 'LIKE'
                ];
            }

            $search = new ContactSearch();

            if($search->get_from_args($args))
            {
                return true;
            }

            return false;
        }

        return false;
    }


    public function very_likely_duplicate()
    {

    }

    public function needs_strong_review_duplicate()
    {

    }

    public function potential_duplicate()
    {

    }

}