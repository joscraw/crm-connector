<?php

namespace CRMConnector\Workflows\Sub;

use CRMConnector\Utils\CRMCFunctions;

/**
 * Class SetContactCodes
 * @package CRMConnector\Workflows\Sub
 */
class SetContactCodes implements SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {
        $contact_id = $args[1];

        if(empty($args[0]) || empty($args[0]['first_name'][0]) || empty($args[0]['last_name'][0])) {
            return;
        }

        $invitation_code = !empty($args[0]['invitation_code'][0]) ? $args[0]['invitation_code'][0] : sprintf("%s%s%s",
            substr($args[0]['first_name'][0], 0, 1),
            substr($args[0]['last_name'][0], 0, 2),
            CRMCFunctions::generateRandomString(10));

        update_post_meta($contact_id, 'invitation_code', $invitation_code);
        update_post_meta($contact_id, 'p1_code', $invitation_code . 'P1C');
        update_post_meta($contact_id, 'p2_code', $invitation_code . 'P2C');
        update_post_meta($contact_id, 'p3_code', $invitation_code . 'P3C');
        update_post_meta($contact_id, 'p4_code', $invitation_code . 'P4C');
        update_post_meta($contact_id, 's1_code', $invitation_code . 'S1C');
        update_post_meta($contact_id, 's2_code', $invitation_code . 'S2C');
        update_post_meta($contact_id, 's3_code', $invitation_code . 'S3C');
        update_post_meta($contact_id, 's4_code', $invitation_code . 'S4C');
    }
}