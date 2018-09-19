<?php

namespace CRMConnector;

use CRMConnector\Database\ContactSearch;
use CRMConnector\Models\Contact;

/**
 * Duplicate Flag
 * Exact match on
 * Almost Certain Duplicate
 * School / Personal or School Email / First Name / Last Name
 * Very Likely Duplicate
 * School / Personal or School Email / Last Name
 * Needs Strong Review Duplicate
 * School / Perm or Current Address / Last Name
 * Potential Duplicate
 * School / First Name / Last Name
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

    /**
     * @param Contact $contact
     * @return bool
     */
    public function very_likely_duplicate(Contact $contact)
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


            $parts = explode(' ', $contact->full_name);
            $last_name = end($parts);

            $args[] = [
                'key' => 'full_name',
                'value' => $last_name,
                'compare' => 'LIKE'
            ];

            $search = new ContactSearch();

            if($search->get_from_args($args))
            {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @param Contact $contact
     * @return bool
     */
    public function needs_strong_review_duplicate(Contact $contact)
    {
        if($contact->email && $contact->current_address_1 && $contact->personal_address_1 && $contact->full_name)
        {
            $args = array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'current_address_1',
                        'value' => $contact->current_address_1,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'personal_address_1',
                        'value' => $contact->personal_address_1,
                        'compare' => '=',
                    )
                ),
                array(
                    'key' => 'account_name',
                    'value' => $contact->account_name,
                    'compare' => '=',
                )
            );


            $parts = explode(' ', $contact->full_name);
            $last_name = end($parts);

            $args[] = [
                'key' => 'full_name',
                'value' => $last_name,
                'compare' => 'LIKE'
            ];

            $search = new ContactSearch();

            if($search->get_from_args($args))
            {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @param Contact $contact
     * @return bool
     */
    public function potential_duplicate(Contact $contact)
    {
        if($contact->account_name && $contact->full_name)
        {
            $args = array(
                'relation' => 'AND',
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

}