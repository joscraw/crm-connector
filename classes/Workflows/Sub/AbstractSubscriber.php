<?php

namespace CRMConnector\Workflows\Sub;

/**
 * Class AbstractSubscriber
 * @package CRMConnector\Workflows\Sub
 */
class AbstractSubscriber
{
    /**
     * @var boolean
     */
    protected $has_errors = false;

    /**
     * @param $has_errors
     */
    public function set_has_errors($has_errors) {
        $this->has_errors = $has_errors;
    }

    /**
     * @return bool
     */
    public function has_errors() {
        return $this->has_errors;
    }
}