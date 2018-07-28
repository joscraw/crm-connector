<?php

namespace CRMConnector\Service\WP;

use CRMConnector\Utils\CRMCFunctions;

/**
 * Class Hooks_Filters
 * @package CRMConnector\Service\ACF
 */
class WPHooksFilters
{

    public static function gettext($translation, $text, $domain)
    {
        global $post;
        if ($post->post_type == 'lists') {
            $translations = &get_translations_for_domain( $domain);
            if ( $text == 'Update') {
                return $translations->translate( 'Export List' );
            }
            if ( $text == 'Publish') {
                return $translations->translate( 'Export List' );
            }
        }
        return $translation;
    }

    public static function admin_head()
    {
        if(isset($_GET['action']) && $_GET['action'] === 'edit'):
            ?>
            <script>
                jQuery(function(){
                    jQuery("body.post-type-chapters .wrap h1").append('<a href="javascript:void(0)" class="page-title-action js-show-import-modal-button">Import Contacts</a>');
                });
            </script>

            <?php
        endif;
    }

    public static function insert_post_data($data, $post)
    {
        if(in_array($post['post_type'], ['chapters', 'contacts', 'drops', 'lists','chapters_invitations']))
        {
            if($post['acf'])
                $data['post_title'] = array_shift($post['acf']);
        }


        return $data;
    }

    public static function login_enqueue_scripts()
    {
        ?>
        <style type="text/css">
            body.login div#login h1 a {
                background-image: url(<?php echo CRMCFunctions::plugin_url() . '/assets/images/nscs-logo-v.svg'?>);
            }
        </style>
        <?php
    }
}