<?php

namespace CRMConnector\Mailers;

use CRMConnector\Utils\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class AbstractBaseMailer
 * @package CRMConnector\Mailers
 */
class AbstractBaseMailer
{
    const API_KEY = 'BzUSl3jPtCI7Jn5Q-fbNZw';

    const TEST_API_KEY = 'JqYF3mjn1DUpCxXMGjN9VQ';

    const FROM_ADDRESS = 'info@nscs.com';

    /**
     * @var PHPMailer
     */
    protected $mail;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
    }

    /**
     * @return PHPMailer
     */
    public function get_mail() {
        return $this->mail;
    }

    /**
     * Initialize with default data. Also override data with optional args array
     *
     * @param array $args
     */
    public function initialize($args = [])
    {
        $mail = $this->mail;
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = 'smtp.mandrillapp.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'Honor Society Student Management';
        $mail->Password = self::TEST_API_KEY;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $this->setFrom(self::FROM_ADDRESS, 'NSCS');
        $mail->isHTML(true);
        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        foreach($args as $key => $value) {
            $this->mail->$key = $value;
        }
    }

    /**
     * @param $address
     * @param string $name
     */
    public function setFrom($address, $name = '')
    {
        $this->mail->setFrom($address, $name);
    }

    /**
     * @param $address
     * @param string $name
     */
    public function addAddress($address, $name = '')
    {
        $this->mail->addAddress($address, $name);
    }

    /**
     * @param $address
     * @param string $name
     */
    public function addReplyTo($address, $name = '')
    {
        $this->mail->addReplyTo($address, $name);
    }

    /**
     * @param $address
     * @param string $name
     */
    public function addCC($address, $name = '')
    {
        $this->mail->addCC($address, $name);
    }

    /**
     * @param $address
     * @param string $name
     */
    public function addBCC($address, $name = '')
    {
        $this->mail->addBCC($address, $name);
    }

    /**
     * Send the email
     */
    public function send()
    {
        $this->mail->send();
    }

    /**
     * @param $path
     * @param string $name
     * @param string $encoding
     * @param string $type
     * @param string $disposition
     */
    public function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment')
    {
        $this->mail->addAttachment($path, $name, $encoding, $type, $disposition);
    }
}