<?php

namespace CRMConnector\Workflows\Sub;


use CRMConnector\Mailers\ChapterLeadershipChangedMailer;
use Roots\WPConfig\Config;

class AlertManagerOfChapterLeadershipChange implements SubscriberInterface
{
    /**
     * @param $args
     * @return mixed
     */
    public function update($args)
    {
        $changed_data = false;
        if(empty($args[0]['contact'][0])) {
            return;
        }
        if(empty($args[0]['chapter'][0])) {
            return;
        }
        if(empty($args[0]['type'][0])) {
            return;
        }
        if(empty($args[0]['position'][0])) {
            return;
        }
        if($args[0]['chapter'][0] !== $args['pre_save_meta']['chapter'][0]) {
            $changed_data = true;
        }
        if($args[0]['contact'][0] !== $args['pre_save_meta']['contact'][0]) {
            $changed_data = true;
        }
        if($args[0]['type'][0] !== $args['pre_save_meta']['type'][0]) {
            $changed_data = true;
        }
        if($args[0]['position'][0] !== $args['pre_save_meta']['position'][0]) {
            $changed_data = true;
        }
        if(!$changed_data) {
            return;
        }
        $chapter_data = sprintf("Full Name: %s Email: %s Type: %s Position: %s",
            get_post_meta($args[0]['contact'][0], 'full_name')[0],
            get_post_meta($args[0]['contact'][0], 'email')[0],
            $args[0]['type'][0],
            $args[0]['position'][0]
        );
        $chapter_operations_managers = get_post_meta($args[0]['chapter'][0], 'chapter_operations_manager');
        $chapter_operations_managers = array_filter($chapter_operations_managers, function($value) {
            $is_null = is_null($value);
            $is_empty = ($value == '');
            return !($is_empty || $is_null);
        });
        if(empty($chapter_operations_managers)) {
            set_transient('errors', [sprintf("There need to be chapter operation managers assigned to the chapter associated with this Chapter Leadership for Workflow %s to trigger",self::class)]);
            return;
        }
        $mailer = new ChapterLeadershipChangedMailer();
        $mail_args = [
            'Subject'   => 'Chapter Leadership Changed',

        ];
        global $CRMConnectorPlugin;
        $current_user = sprintf('%s %s %s',
            $CRMConnectorPlugin->data['current_user_email'],
            $CRMConnectorPlugin->data['current_user_first_name'],
            $CRMConnectorPlugin->data['current_user_last_name']
        );
        $view = sprintf("%s/plugins/crm-connector/views/mailers/alert_chapter_manager_of_file_changed.php", Config::get('WP_CONTENT_DIR'));
        ob_start();
        extract([
            'chapter_data'  =>  $chapter_data,
            'chapter'       =>  !empty(get_post_meta($args[0]['chapter'][0], 'account_name')[0]) ? get_post_meta($args[0]['chapter'][0], 'account_name')[0] : "",
            'current_user'  => $current_user
        ]);
        include $view;
        $body = ob_get_contents();
        ob_end_clean();
        $args['Body'] = $body;
        $mailer->initialize($mail_args);
        foreach($chapter_operations_managers as $chapterOperationsManager) {
            $email = get_post_meta($chapterOperationsManager[0], 'email', true);
            $full_name = get_post_meta($chapterOperationsManager[0], 'full_name', true);
            if(!empty($email))
            {
                $mailer->addAddress($email, $full_name);
            }
        }
        $mailer->send();
    }

}