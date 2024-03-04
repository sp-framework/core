<?php

namespace System\Base\Providers\LoggerServiceProvider\Email;

use Phalcon\Logger\Adapter\AbstractAdapter;
use Phalcon\Logger\Item;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Email\EmailException;
use System\Base\Providers\EmailServiceProvider\Email;

class Adapter extends AbstractAdapter
{
    protected $email;

    protected $logsConfig;

    protected $helper;

    protected $messages = [];

    public function __construct($email, $logsConfig, $helper)
    {
        $this->email = $email;

        $this->logsConfig = $logsConfig;

        $this->helper = $helper;
    }

    public function process(Item $item): void
    {
        if ($item->getLevel() === 0) {
            $this->messages['emergency'] = $this->helper->decode($this->formatter->format($item), true);
        } else if ($item->getLevel() === 1) {
            $this->messages['critical'] = $this->helper->decode($this->formatter->format($item), true);
        } else if ($item->getLevel() === 2) {
            $this->messages['alert'] = $this->helper->decode($this->formatter->format($item), true);
        }
    }

    public function close(): bool
    {
        return true;
    }

    public function sendEmail()
    {
        if (!$this->logsConfig->emergencyLogsEmail) {
            throw new EmailException('Email not enabled');
        }

        if ($this->logsConfig->emergencyLogsEmailAddresses !== '') {
            if ($this->email->setup()) {
                $emailSettings = $this->email->getEmailSettings();

                $this->email->setSender(
                    $emailSettings['username'],
                    $emailSettings['username']
                );

                foreach (explode(',', $this->logsConfig->emergencyLogsEmailAddresses) as $key => $emailAddress) {
                    $this->email->setRecipientTo(trim($emailAddress), trim($emailAddress));
                }

                $this->email->setSubject('System generated an critical error. Need immediate attention!');

                $this->email->setBody($this->buildMessageBody());

                $sendNewEmail = $this->email->sendNewEmail();

                if ($sendNewEmail !== true) {
                    throw new EmailException($sendNewEmail);
                }

                $this->messages['emailSent'] = true;
            }
        }

        return $this->messages;
    }

    protected function buildMessageBody()
    {
        $body = '';

        foreach ($this->messages as $key => $message) {
            if ($message['type'] === 0) {
                $body .= $this->buildEmergencyMessageBody($message) . '<br>';
            } else if ($message['type'] === 1) {
                $body .= $this->buildCriticalMessageBody($message) . '<br>';
            } else if ($message['type'] === 2) {
                $body .= $this->buildAlertMessageBody($message) . '<br>';
            }
        }

        return $body;
    }

    protected function buildEmergencyMessageBody($message)
    {
        return $this->buildMessage($message, "#fe0000");
    }

    protected function buildCriticalMessageBody($message)
    {
        return $this->buildMessage($message, "#ffcc67");
    }

    protected function buildAlertMessageBody($message)
    {
        return $this->buildMessage($message, "#f8ff00");
    }

    protected function buildMessage($message, $color)
    {
        return '<style type="text/css">
                .tg' . $message['type'] . ' {border-collapse:collapse;border-spacing:0;width:100%;}
                .tg' . $message['type'] . ' td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
                  overflow:hidden;padding:10px 5px;word-break:normal;}
                .tg' . $message['type'] . ' th{border-color:black;background-color:' . $color . '; border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
                  font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
                .tg' . $message['type'] . ' .tg-6kns{border-color:#333333;text-align:center;vertical-align:top}
                .tg' . $message['type'] . ' .tg-orf0{font-family:"Arial Black", Gadget, sans-serif !important;;text-align:left;vertical-align:top}
                .tg' . $message['type'] . ' .tg-0lax{text-align:left;vertical-align:top}
            </style>
            <table class="tg' . $message['type'] . '">
                <thead>
                <tr>
                    <th class="tg-6kns">
                        <span style="font-weight:bold; text-align:center;">' .
                            strtoupper($message['typeName']) .
                        '</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Session ID: ' . $message['session'] .
                        ' Connection ID: ' . $message['connection'] .
                        ' Client IP:' . $message['client_ip'] .
                        ' Timestamp: ' . $message['timestamp'] .
                        ' Mseconds: ' . $message['mseconds'] .
                    '</td>
                </tr>
                <tr>
                    <td>' . $message['message'] .
                    '</td>
                </tr>
            </tbody>';
    }
}