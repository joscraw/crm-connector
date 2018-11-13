<?php

namespace CRMConnector;

/**
 * Class ReportProgress
 * @package CRMConnector
 */
class ReportProgress
{
    /**
     * @var int
     */
    private $steps_completed = 0;

    /**
     * @var string
     */
    private $message = '';

    /**
     * @return int
     */
    public function get_total_steps() {
        $args = [
            'post_type' => 'contacts',
            'posts_per_page' => -1,
        ];
        $contacts = get_posts($args);
        return count($contacts);
    }

    /**
     * @return int
     */
    public function getCurrentStep()
    {
        return $this->steps_completed + 1;
    }

    public function complete_current_step() {
        $this->steps_completed++;
    }

    /**
     * @param $message
     */
    public function set_message($message) {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function get_message() {
        return $this->message;
    }
}