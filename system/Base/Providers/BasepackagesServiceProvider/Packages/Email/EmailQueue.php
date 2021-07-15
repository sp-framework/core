<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Email;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email\BasepackagesEmailQueue;

class EmailQueue extends BasePackage
{
    protected $modelToUse = BasepackagesEmailQueue::class;

    protected $packageName = 'emailQueue';

    public $emailQueue;

    public $queueLock = false;

    const PRIORITY_HIGH = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_LOW = 3;

    const STATUS_IN_QUEUE = 0;
    const STATUS_SENT = 1;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function addToQueue(array $data)
    {
        if (isset($data['confidential']) && $data['confidential'] == 1) {
            $data = $this->encryptBody($data);
        }

        if ($this->add($data)){
            $this->addResponse('Added email to queue with ID ' . $this->packagesData->last['id'], 0, null, true);

            return true;
        } else {
            $this->addResponse('Error adding email to queue.', 1, []);

            return false;
        }
    }

    public function getLock()
    {
        return $this->queueLock;
    }

    public function processQueue()
    {
        if ($this->queueLock === true) {
            $this->addResponse('Another process is clearing the queue, please wait...', 1);

            return;
        }

        $this->queueLock = true;

        $hadErrors = false;

        for ($priority = self::PRIORITY_HIGH; $priority <= self::PRIORITY_LOW; $priority++) {
            $conditions =
                [
                    'conditions'    => 'status = :status: AND priority = :priority:',
                    'bind'          =>
                        [
                            'status'    => self::STATUS_IN_QUEUE,
                            'priority'  => $priority
                        ]
                ];

            $queue = $this->getByParams($conditions);

            if ($queue && is_array($queue) && count($queue) > 0) {
                foreach ($queue as $key => $email) {
                    if (!$this->basepackages->email->setup(null, $email['app_id'])) {
                        $email['status'] = 2;
                        $email['logs'] = 'Email Service is not configured or assigned to a domain, please configure email service and try again.';

                        $this->update($email);

                        $hadErrors = true;
                    } else {
                        $emailSettings = $this->basepackages->email->getEmailSettings();

                        $this->basepackages->email->setSender($emailSettings['from_address'], $emailSettings['from_address']);

                        $email['to_addresses'] = Json::decode($email['to_addresses'], true);
                        if (count($email['to_addresses']) > 1) {
                            foreach ($email['to_addresses'] as $key => $toAddress) {
                                $this->basepackages->email->setRecipientTo($toAddress, $toAddress);
                            }
                        } else {
                            $this->basepackages->email->setRecipientTo(Arr::first($email['to_addresses']), Arr::first($email['to_addresses']));
                        }
                        $email['to_addresses'] = Json::encode($email['to_addresses']);

                        $this->basepackages->email->setSubject($email['subject']);

                        if (isset($email['confidential']) && $email['confidential'] == 1) {
                            $email = $this->decryptBody($email);
                        }

                        $this->basepackages->email->setBody($email['body']);

                        $logs = $this->basepackages->email->sendNewEmail();

                        if ($logs === true) {
                            $email['status'] = 1;
                            $email['logs'] = 'Sent';
                            $email['sent_on'] = date("F j, Y, g:i a");

                            $this->update($email);
                        }
                    }
                }
            }
        }

        if ($hadErrors) {
            $this->addResponse('Queue processed with some errors. Please check the email queue for details.', 1);

            return;
        }

        $this->addResponse('Queue processes successfully.');
    }

    protected function sendEmail()
    {

        //     $emailSettings = $this->basepackages->email->getEmailSettings();

        //     $this->basepackages->email->setSender($emailSettings['from_address'], $emailSettings['from_address']);
        //     $this->basepackages->email->setRecipientTo($this->account['email'], $this->account['email']);
        //     $this->basepackages->email->setSubject('Verification Code for ' . $this->domains->getDomain()['name']);
        //     $this->basepackages->email->setBody($verificationCode);

        //     return $this->basepackages->email->sendNewEmail();
        // } else {
        //     return false;
        // }
    }

    public function removeFromQueue(array $data)
    {
        //
    }

    public function changePriority(array $data)
    {
        //
    }

    protected function decryptBody(array $data)
    {
        if ($data['body'] && $data['body'] != '') {
            $data['body'] = $this->crypt->decryptBase64($data['body'], $this->secTools->getSigKey());
        }

        return $data;
    }

    protected function encryptBody(array $data)
    {
        if ($data['body'] != '') {
            $data['body'] = $this->crypt->encryptBase64($data['body'], $this->secTools->getSigKey());
        }

        return $data;
    }
}