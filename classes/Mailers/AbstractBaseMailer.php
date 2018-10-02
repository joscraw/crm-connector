<?php

namespace CRMConnector\Mailers;

use Mandrill;

/**
 * Class AbstractBaseMailer
 * @package CRMConnector\Mailers
 */
class AbstractBaseMailer
{
    const API_KEY = 'BzUSl3jPtCI7Jn5Q-fbNZw';

    /**
     * @var Mandrill
     */
    protected $mandrill;

    public function __construct() {
        $this->mandrill = new Mandrill(self::API_KEY);
    }
}