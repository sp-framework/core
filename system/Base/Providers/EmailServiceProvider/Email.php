<?php

namespace System\Base\Providers\EmailServiceProvider;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Phalcon\Di\DiInterface;
use Phalcon\Helper\Json;

class Email
{
    private $container;

    protected $email;

    protected $fromEmailAddress;

    protected $fromName;

    protected $to = [];

    protected $cc = [];

    protected $bcc = [];

    protected $attachments = [];

    protected $subject;

    protected $body;

    protected $debugOutput = [];

    public function __construct(DiInterface $container)
    {
        include (__DIR__ . '/vendor/autoload.php');

        $this->container = $container;

        $this->logger = $container->getShared('logger');

        $this->application = $container->getShared('modules')->applications->getApplicationInfo();
    }

    public function init()
    {
        $this->email = new PHPMailer(true);

        return $this;
    }

    public function setup()
    {
        $emailSettings = Json::decode($this->application['settings'], true)['email'];

        if ($emailSettings['enabled'] === 'true') {
            $this->email->isSMTP();

            if ($emailSettings['host'] === '' || $emailSettings['port'] === '') {
                $this->logger->log->error(
                    'Email is enabled but host and port configuration missing. Cannot perform setup.'
                );

                return false;
            } else {
                $this->email->Host          = $emailSettings['host'];
                $this->email->Port          = $emailSettings['port'];
            }

            if ($emailSettings['auth'] === 'true' &&
                ($emailSettings['username'] === '' || $emailSettings['password'] == '')
            ) {
                $this->logger->log->error(
                    'Email auth configuration missing. Cannot perform setup.'
                );

                return false;
            } else {
                $this->email->SMTPAuth      = true;
                $this->email->Username      = $emailSettings['username'];
                $this->email->Password      = $emailSettings['password'];
            }

            $this->email->SMTPSecure =
                isset($emailSettings['encryption']) && $emailSettings['encryption'] === 'true' ?
                PHPMailer::ENCRYPTION_SMTPS :
                '';

            $this->email->isHTML(
                isset($emailSettings['allow_html_body']) ?
                $emailSettings['allow_html_body'] :
                ''
            );

            return $this;

        } else {
            $this->logger->log->error('Email is not enabled. Cannot perform setup.');

            return false;
        }

        var_dump($emailSettings);
        die();


    }

    public function sendNewEmail()
    {
        try {
            $this->email->setFrom($this->fromEmailAddress, $this->fromName);

            foreach ($this->to as $toKey => $toValue) {
                $this->email->addAddress($toValue['email'], $toValue['name']);
            }

            if (count($this->cc) > 0) {
                foreach ($this->cc as $ccKey => $ccValue) {
                    $this->email->addCC($ccValue['email'], $ccValue['name']);
                }
            }

            if (count($this->bcc) > 0) {
                foreach ($this->bcc as $bccKey => $bccValue) {
                    $this->email->addBCC($bccValue['email'], $bccValue['name']);
                }
            }

            if (count($this->attachments) > 0) {
                foreach ($this->attachments as $attachmentKey => $attachmentValue) {
                    $this->email->addAttachment($attachmentValue['file'], $attachmentValue['name']);
                }
            }

            $this->email->Subject = $this->subject;

            $this->email->Body = $this->body;

            $this->email->send();

            return true;
        } catch (Exception $e) {

            return $this->email->ErrorInfo;
        }

    }

    public function setSender($email, $name)
    {
        $this->fromEmailAddress = $email;
        $this->fromName = $name;

        return $this;
    }

    public function setRecipientTo($email, $name)
    {
        $this->to = array_merge($this->to, [['email' => $email, 'name' => $name]]);

        return $this;
    }

    public function setRecipientCC($email, $name)
    {
        $this->cc = array_merge($this->cc, [['email' => $email, 'name' => $name]]);

        return $this;
    }

    public function setRecipientBCC($email, $name)
    {
        $this->bcc = array_merge($this->bcc, [['email' => $email, 'name' => $name]]);

        return $this;
    }

    public function addAttachments($file, $name)
    {
        $this->attachments = array_merge($this->attachments, [['file' => $email, 'name' => $name]]);

        return $this;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function sendTestEmail(array $emailSettings)
    {
        $this->email->SMTPDebug     = SMTP::DEBUG_LOWLEVEL;
        $this->email->isSMTP();
        $this->email->Host          = $emailSettings['host'];
        $this->email->Port          = $emailSettings['port'];
        $this->email->SMTPAuth      = $emailSettings['auth'];
        $this->email->Username      = $emailSettings['username'];
        $this->email->Password      = $emailSettings['password'];
        $this->email->SMTPSecure    = $emailSettings['encryption'] === 'true' ? PHPMailer::ENCRYPTION_SMTPS : '';
        $this->email->isHTML($emailSettings['allow_html_body']);
        $this->email->Debugoutput   =
            function($str, $level) {
                array_push($this->debugOutput, $str);
            };

        $this->setSender(
            $emailSettings['username'],
            $emailSettings['username']
        );

        $this->setRecipientTo(
            $emailSettings['test_email_address'],
            $emailSettings['test_email_address']
        );

        $this->setSubject(
            'Testing SMTP Settings for host ' . $emailSettings['host']
        );

        $this->setBody('Test Successful!');

        return $this->sendNewEmail();
    }

    public function getDebugOutput()
    {
        return $this->debugOutput;
    }
}