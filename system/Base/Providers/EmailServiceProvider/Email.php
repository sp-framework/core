<?php

namespace System\Base\Providers\EmailServiceProvider;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use System\Base\Providers\EmailServiceProvider\EmailException;
use System\Base\Providers\EmailServiceProvider\EmailServices;

class Email
{
    protected $application;

    protected $domain;

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

    protected $emailSettings;

    public function __construct($application, $domain)
    {
        include (__DIR__ . '/vendor/autoload.php');

        $this->application = $application;

        $this->domain = $domain;
    }

    public function init()
    {
        $this->email = new PHPMailer(true);

        return $this;
    }

    public function getEmailSettings()
    {
        return $this->emailSettings;
    }

    public function setup($emailSettings = null)
    {
        $emailservices = new EmailServices;

        if ($emailSettings) {
            $this->emailSettings = $emailSettings;
        } else {
            if (isset($this->domain['settings']['applications'][$this->application['id']]['emailService'])) {

                $emailService = $this->domain['settings']['applications'][$this->application['id']]['emailService'];

                $this->emailSettings = $emailservices->init()->getById($emailService);

            } else {
                throw new EmailException(
                    'No Email Service Configured & attached to the Domain. Please setup email service and attach it to domain ' . $this->domain['name']
                );
            }
        }

        $this->email->isSMTP();

        if ($this->emailSettings['host'] === '' || $this->emailSettings['port'] === '') {
            throw new EmailException(
                'Email is enabled but host and port configuration missing. Cannot perform setup.'
            );
        } else {
            $this->email->Host          = $this->emailSettings['host'];
            $this->email->Port          = $this->emailSettings['port'];
        }

        if ($this->emailSettings['auth'] === '1' &&
            ($this->emailSettings['username'] === '' || $this->emailSettings['password'] === '')
        ) {
            throw new EmailException(
                'Email auth configuration missing. Cannot perform setup.'
            );
        } else {
            $this->email->SMTPAuth      = true;
            $this->email->Username      = $this->emailSettings['username'];
            $this->email->Password      = $this->emailSettings['password'];
        }

        $this->email->SMTPSecure =
            isset($this->emailSettings['encryption']) && $this->emailSettings['encryption'] === '1' ?
            PHPMailer::ENCRYPTION_SMTPS :
            '';

        $this->email->isHTML(
            isset($this->emailSettings['allow_html_body']) && $this->emailSettings['allow_html_body'] === '1' ?
            $this->emailSettings['allow_html_body'] :
            ''
        );

        return $this;
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
            var_dump($this->email->ErrorInfo);
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
        $this->email->isSMTP();
        $this->email->Host          = $emailSettings['host'];
        $this->email->Port          = $emailSettings['port'];
        $this->email->SMTPAuth      =
            $emailSettings['auth'] === 'true' ||
            $emailSettings['auth'] === '1' ?
            true :
            '';
        $this->email->Username      = $emailSettings['username'];
        $this->email->Password      = $emailSettings['password'];
        $this->email->SMTPSecure    =
            $emailSettings['encryption'] === 'true' ||
            $emailSettings['encryption'] === '1' ?
            PHPMailer::ENCRYPTION_SMTPS :
            '';
        $this->email->isHTML($emailSettings['allow_html_body']);
        $this->email->SMTPDebug     = SMTP::DEBUG_LOWLEVEL;
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