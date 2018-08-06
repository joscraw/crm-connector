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

        if ($post->post_type == 'contacts') {
            $translations = &get_translations_for_domain( $domain);
            if ( $text == 'Update') {
                return $translations->translate( 'Edit Contact' );
            }
            if ( $text == 'Publish') {
                return $translations->translate( 'Create Contact' );
            }
        }

        if ($post->post_type == 'chapters_invitations') {
            $translations = &get_translations_for_domain( $domain);
            if ( $text == 'Update') {
                return $translations->translate( 'Edit Chapter Invitation' );
            }
            if ( $text == 'Publish') {
                return $translations->translate( 'Create Chapter Invitation' );
            }
        }

        if ($post->post_type == 'drops') {
            $translations = &get_translations_for_domain( $domain);
            if ( $text == 'Update') {
                return $translations->translate( 'Edit Drop' );
            }
            if ( $text == 'Publish') {
                return $translations->translate( 'Create Drop' );
            }
        }

        if ($post->post_type == 'chapter_summaries') {
            $translations = &get_translations_for_domain( $domain);
            if ( $text == 'Update') {
                return $translations->translate( 'Edit Chapter Summary' );
            }
            if ( $text == 'Publish') {
                return $translations->translate( 'Create Chapter Summary' );
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

    public static function manage_imports_posts_columns($columns)
    {
        if(is_array($columns))
        {
            unset($columns['date']);
            unset($columns['title']);

            if(!isset( $columns['date_started'] ))
                $columns['date_started'] = __( 'Date Started' );

            if(!isset( $columns['date_completed'] ))
                $columns['date_completed'] = __( 'Date Completed' );

            if(!isset( $columns['log_file'] ))
                $columns['log_file'] = __( 'Log File' );

            if(!isset( $columns['status'] ))
                $columns['status'] = __( 'Status' );
        }

        return $columns;
    }

    public static function manage_imports_posts_custom_column($column_name, $post_id)
    {
        $data = get_post_custom($post_id);

        if ( $column_name == 'date_started')
            printf( '%s', isset($data['date_started']) ? $data['date_started'][0] : '');

        if ( $column_name == 'date_completed')
            printf( '%s', isset($data['date_completed']) ? $data['date_completed'][0] : '');

        if ( $column_name == 'log_file')
            printf( '<a target="_blank" href="%s">Log File</a>', isset($data['log_file']) ? CRMCFunctions::plugin_url() . '/logs/' . $data['log_file'][0] : '');

        if ( $column_name == 'status')
            printf( '%s', isset($data['status']) ? $data['status'][0] : '');
    }

    public static function manage_exports_posts_columns($columns)
    {
        if(is_array($columns))
        {
            unset($columns['date']);
            unset($columns['title']);

            if(!isset( $columns['date_started'] ))
                $columns['date_started'] = __( 'Date Started' );

            if(!isset( $columns['date_completed'] ))
                $columns['date_completed'] = __( 'Date Completed' );

            if(!isset( $columns['log_file'] ))
                $columns['log_file'] = __( 'Log File' );

            if(!isset( $columns['status'] ))
                $columns['status'] = __( 'Status' );
        }

        return $columns;
    }

    public static function manage_exports_posts_custom_column($column_name, $post_id)
    {
        $data = get_post_custom($post_id);

        if ( $column_name == 'date_started')
            printf( '%s', isset($data['date_started']) ? $data['date_started'][0] : '');

        if ( $column_name == 'date_completed')
            printf( '%s', isset($data['date_completed']) ? $data['date_completed'][0] : '');

        if ( $column_name == 'log_file')
            printf( '<a target="_blank"  href="%s">Log File</a>', isset($data['log_file']) ? CRMCFunctions::plugin_url() . '/logs/' . $data['log_file'][0] : '');

        if ( $column_name == 'status')
            printf( '%s', isset($data['status']) ? $data['status'][0] : '');
    }

    public static function update_messages($msg)
    {
        $msg[ 'lists' ] = array (
            0 => '', // Unused. Messages start at index 1.
            1 => "List Successfully Updated",
            // or simply "Actor updated.",
            // natural language "The actor's profile has been updated successfully.",
            // or what you need "Actor updated, so remember to check also <strong>the films list</strong>."


            2 => 'Custom field updated.',  // Probably better do not touch
            3 => 'Custom field deleted.',  // Probably better do not touch

            4 => "List Successfully Updated",
            5 => "List restored to revision",
            6 => "List's Successfully Created",
            // you can use the kind of messages that better fits with your needs
            // 6 => "Good boy, one more... so, 4,999,999 are to reach IMDB.",
            // 6 => "This actor is already on the website.",
            // 6 => "Congratulations, a new Actor's profile has been published.",

            7 => "List Successfully Created",
            8 => "List Successfully Created",
            9 => "List Successfully Created",
            10 => "List Successfully Created",
        );
        return $msg;
    }

    public static function admin_notices()
    {
        $screen = get_current_screen();
        $errors = get_transient('errors');
        $notice = get_transient('notice');

        if('lists' == $screen->post_type && in_array($screen->base, ['edit', 'post'])){

            if (!empty($errors)){?>
                <div class="error">
                <p><?php echo implode(",", $errors); ?></p>
                </div>
                <?php
            }

            if($notice)
            {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo $notice; ?></p>
                </div>
                <?php
            }

        }

        delete_transient('notice');
        delete_transient('errors');
    }

    public static function mailchimp_settings_page()
    {
        register_setting( 'pluginPage', 'crmc_settings' );

        add_settings_section(
            'crmc_pluginPage_section',
            __( 'MailChimp API Settings', 'wordpress' ),
            function(){},
            'pluginPage'
        );

        add_settings_field(
            'mailchimp_username',
            __( 'MailChimp Username', 'wordpress' ),
            function()
            {
                $options = get_option( 'crmc_settings' );
                ?>
                <input type='text' name='crmc_settings[mailchimp_username]' value='<?php echo $options['mailchimp_username']; ?>'>
                <?php
            },
            'pluginPage',
            'crmc_pluginPage_section'
        );

        add_settings_field(
            'mailchimp_api_key',
            __( 'MailChimp API Key', 'wordpress' ),
            function()
            {
                $options = get_option( 'crmc_settings' );
                ?>
                <input type='text' name='crmc_settings[mailchimp_api_key]' value='<?php echo $options['mailchimp_api_key']; ?>'>
                <?php
            },
            'pluginPage',
            'crmc_pluginPage_section'
        );
    }
}