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

    const STATUS_IN_QUEUE = 1;
    const STATUS_SENT = 2;
    const STATUS_ERROR = 3;

    public function init(bool $resetCache = false)
    {
        // $this->getAll($resetCache);

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

    public function processQueue($processPriority = 0)
    {
        if ($this->queueLock === true) {
            $this->addResponse('Another process is clearing the queue, please wait...', 1);

            return;
        }

        $this->queueLock = true;

        $hadErrors = false;

        if ($processPriority !== 0) {
            $priorityToProcess = $processPriority;
        } else {
            $priorityToProcess = self::PRIORITY_LOW;
        }

        for ($priority = self::PRIORITY_HIGH; $priority <= $priorityToProcess; $priority++) {
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
                foreach ($queue as $key => $queueEmail) {
                    if (!$this->basepackages->email->setup(null, $queueEmail['app_id'])) {
                        $queueEmail['status'] = self::STATUS_ERROR;
                        $queueEmail['logs'] = 'Email Service is not configured or assigned to a domain, please configure email service and try again.';

                        $this->update($queueEmail);

                        $hadErrors = true;
                    } else {
                        $queueEmailSettings = $this->basepackages->email->getEmailSettings();

                        $this->basepackages->email->setSender($queueEmailSettings['from_address'], $queueEmailSettings['from_address']);

                        $queueEmail['to_addresses'] = Json::decode($queueEmail['to_addresses'], true);
                        if (count($queueEmail['to_addresses']) > 1) {
                            foreach ($queueEmail['to_addresses'] as $key => $toAddress) {
                                $this->basepackages->email->setRecipientTo($toAddress, $toAddress);
                            }
                        } else {
                            $this->basepackages->email->setRecipientTo(Arr::first($queueEmail['to_addresses']), Arr::first($queueEmail['to_addresses']));
                        }
                        $queueEmail['to_addresses'] = Json::encode($queueEmail['to_addresses']);

                        $this->basepackages->email->setSubject($queueEmail['subject']);

                        if (isset($queueEmail['confidential']) && $queueEmail['confidential'] == 1) {
                            $queueEmail = $this->decryptBody($queueEmail);
                            $this->basepackages->email->setBody($queueEmail['body']);
                            $queueEmail = $this->encryptBody($queueEmail);
                        } else {
                            $this->basepackages->email->setBody($queueEmail['body']);
                        }

                        $logs = $this->basepackages->email->sendNewEmail();

                        if ($logs === true) {
                            $queueEmail['status'] = self::STATUS_SENT;
                            $queueEmail['logs'] = 'Sent';
                            $queueEmail['sent_on'] = date("F j, Y, g:i a");

                            $this->update($queueEmail);
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

    public function requeue(array $data)
    {
        $email = $this->getById($data['id']);

        $email['status'] = 1;
        $email['logs'] = '';

        if ($this->update($email)) {
            $this->addResponse('Requeued');

            return;
        }

        $this->addResponse('Error requeuing message');
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