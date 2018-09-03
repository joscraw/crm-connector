<?php
/**
 * Template Tag Functions For The Website
 *
 * Display functions (template-tags) for use in WordPress templates.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

function render_chapter_rsvp_confirmation_form()
{
    global $crmConnectorFrontend;

    $current_user_id = $crmConnectorFrontend->data['current_user_id'];
    $current_user_roles = $crmConnectorFrontend->data['current_user_roles'];

    if($current_user_id === 0 || empty($current_user_roles))
    {
        return '';
    }

    if (in_array( 'administrator', $current_user_roles ) ||
        in_array( 'chapter_adviser', $current_user_roles ) ||
        in_array( 'chapter_officer', $current_user_roles ) ||
        in_array( 'honor_society_admin', $current_user_roles ))
    {
        ob_start();
        ?>
        <a href="<?php get_page_link(30200); ?>" class="tribe-button">RSVP Confirmations</a>
        <?php
        $html = ob_get_clean();

        return $html;
    }


}