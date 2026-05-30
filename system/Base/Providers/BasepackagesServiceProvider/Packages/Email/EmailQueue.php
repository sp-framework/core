<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Email;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email\BasepackagesEmailQueue;

class EmailQueue extends BasePackage
{
    protected $modelToUse = BasepackagesEmailQueue::class;

    protected $packageName = 'emailQueue';

    public $emailQueue;

    protected $priorityToProcess = null;

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

        $queueEmailSettings = $this->basepackages->email->getEmailSettings();

        if (!isset($data['from_address'])) {
            $data['from_address'] = $queueEmailSettings['from_address'];
            $data['from_name'] = $queueEmailSettings['from_address'];
        } else if (!isset($data['from_name'])) {
            $data['from_name'] = $queueEmailSettings['from_address'];
        }

        if ($this->add($data)){
            $this->addResponse('Added email to queue with ID ' . $this->packagesData->last['id'], 0, null, true);

            $task = $this->basepackages->workers->tasks->findByCallArgs($data['priority'], "priority", 'processemailqueue');

            if ($task && $task['force_next_run'] === null) {
                $this->basepackages->workers->tasks->forceNextRun(['id' => $task['id']]);
            }

            if (!$this->basepackages->email->setup(null, $this->domains->getDomain()['id'], $this->apps->getAppInfo()['id'])) {
                $this->basepackages->emailservices->errorEmailService(
                    'Email was added to the queue but, there is no email service associated with app: ' . $this->apps->getAppInfo()['name'] . '. Please add a new service ' .
                    'and assign it to the app via domains.'
                );
            }

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

    public function processQueue($processPriority = 0, $confidential = false, $id = null)
    {
        if ($this->queueLock === true && $processPriority === $this->priorityToProcess) {
            $this->addResponse('Another process is clearing the queue, please wait...', 1);

            return;
        }
        if ($processPriority != 0) {
            $this->priorityToProcess = (int) $processPriority;
        } else {
            $this->priorityToProcess = self::PRIORITY_LOW;
        }

        $this->queueLock = true;

        if ($this->config->databasetype === 'db') {
            if ($id) {
                $conditions =
                    [
                        'conditions'    => 'id = :id:',
                        'bind'          =>
                            [
                                'id'    => $id,
                            ]
                    ];
            } else if ($confidential) {
                $conditions =
                    [
                        'conditions'    => 'status = :status: AND confidential = :confidential:',
                        'bind'          =>
                            [
                                'status'        => self::STATUS_IN_QUEUE,
                                'confidential'  => 1
                            ]
                    ];
            } else {
                $conditions =
                    [
                        'conditions'    => 'status = :status: AND priority = :priority:',
                        'bind'          =>
                            [
                                'status'    => self::STATUS_IN_QUEUE,
                                'priority'  => $this->priorityToProcess
                            ]
                    ];
            }
        } else {
            if ($id) {
                $conditions = ['conditions' => ['id', '=', (int) $id]];
            } else {
                $conditions = ['conditions' => [['status', '=', self::STATUS_IN_QUEUE], ['priority', '=', $this->priorityToProcess]]];
            }
        }

        $queue = $this->getByParams($conditions, true, false);

        $queueProcessedIds = [];
        $queueErrorIds = [];

        if ($queue && is_array($queue) && count($queue) > 0) {
            foreach ($queue as $key => $queueEmail) {
                if (!$this->basepackages->email->setup(null, $queueEmail['domain_id'], $queueEmail['app_id'])) {
                    $queueEmail['status'] = self::STATUS_ERROR;
                    $queueEmail['logs'] = 'Email Service is not configured or assigned to a domain, please configure email service and try again.';

                    $this->update($queueEmail);

                    array_push($queueErrorIds, $queueEmail['id']);
                } else {
                    $queueEmailSettings = $this->basepackages->email->getEmailSettings();

                    //Set Sender
                    if (isset($queueEmail['from_address']) && isset($queueEmail['from_name']) &&
                        $queueEmail['from_address'] !== '' && $queueEmail['from_name'] !== ''
                    ) {
                        $fromAddress = $queueEmail['from_address'];
                        $fromName = $queueEmail['from_name'];
                    } else {
                        $fromAddress = $queueEmailSettings['from_address'];

                        if (!isset($queueEmailSettings['from_name']) || (isset($queueEmailSettings['from_name']) && $queueEmailSettings['from_name'] === '')) {
                            $fromName = $queueEmailSettings['from_name'] = $queueEmailSettings['from_address'];
                        } else {
                            $fromName = $queueEmailSettings['from_name'];
                        }
                    }
                    $this->basepackages->email->setSender($fromAddress, $fromName);

                    //Set To
                    if (is_string($queueEmail['to_addresses'])) {
                        $queueEmail['to_addresses'] = $this->helper->decode($queueEmail['to_addresses'], true);
                    }
                    foreach ($queueEmail['to_addresses'] as $toAddress) {
                        if (is_array($toAddress) && (isset($toAddress['email']) && isset($toAddress['name']))) {
                            $this->basepackages->email->setRecipientTo($toAddress['email'], $toAddress['name']);
                        } else {
                            $this->basepackages->email->setRecipientTo($toAddress, $toAddress);
                        }
                    }

                    //Set CC
                    if (is_string($queueEmail['cc_addresses'])) {
                        $queueEmail['cc_addresses'] = $this->helper->decode($queueEmail['cc_addresses'], true);
                    }
                    foreach ($queueEmail['cc_addresses'] as $ccAddress) {
                        if (is_array($ccAddress) && (isset($ccAddress['email']) && isset($ccAddress['name']))) {
                            $this->basepackages->email->setRecipientCc($ccAddress['email'], $ccAddress['name']);
                        } else {
                            $this->basepackages->email->setRecipientCc($ccAddress, $ccAddress);
                        }
                    }

                    //Set BCC
                    if (is_string($queueEmail['bcc_addresses'])) {
                        $queueEmail['bcc_addresses'] = $this->helper->decode($queueEmail['bcc_addresses'], true);
                    }
                    foreach ($queueEmail['bcc_addresses'] as $bccAddress) {
                        if (is_array($bccAddress) && (isset($bccAddress['email']) && isset($bccAddress['name']))) {
                            $this->basepackages->email->setRecipientBcc($bccAddress['email'], $bccAddress['name']);
                        } else {
                            $this->basepackages->email->setRecipientBcc($bccAddress, $bccAddress);
                        }
                    }

                    //Set Subject
                    $this->basepackages->email->setSubject($queueEmail['subject']);

                    //Set encryption
                    if (isset($queueEmail['confidential']) && $queueEmail['confidential'] == 1) {
                        $queueEmail = $this->decryptBody($queueEmail);
                        $this->basepackages->email->setBody($queueEmail['body']);
                        $queueEmail = $this->encryptBody($queueEmail);
                    } else {
                        $this->basepackages->email->setBody($queueEmail['body']);
                    }

                    //Set Attachments
                    if (isset($queueEmail['attachments']) && is_array($queueEmail['attachments']) && count($queueEmail['attachments']) > 0) {
                        foreach ($queueEmail['attachments'] as $attachment) {
                            $file = $this->basepackages->storages->getFileInfo($attachment);

                            if ($file) {
                                $path = $this->basepackages->storages->getAbsolutePath($file);

                                if ($path) {
                                    $this->basepackages->email->addAttachments($path, $file['org_file_name']);
                                }
                            }
                        }
                    }

                    //Set Logs
                    $logs = $this->basepackages->email->sendNewEmail();

                    if ($logs === true) {
                        $queueEmail['status'] = self::STATUS_SENT;
                        $queueEmail['logs'] = 'Sent successfully!';
                        $queueEmail['sent_on'] = date("F j, Y, g:i a");

                        array_push($queueProcessedIds, $queueEmail['id']);

                        $this->update($queueEmail);
                    }
                }
            }
        }

        $this->queueLock = false;

        if (count($queueErrorIds) > 0) {
            $this->addResponse('Queue processed with some errors. Please check the email queue for details.', 1, ['queueErrorIds' => $queueErrorIds]);

            return false;
        }

        $this->addResponse('Queue processed successfully.', 0, ['queueProcessedIds' => $queueProcessedIds]);
    }

    public function requeue(array $data)
    {
        $email = $this->getById($data['id']);

        $email['status'] = 1;
        $email['logs'] = '';

        if ($this->update($email)) {
            $task = $this->basepackages->workers->tasks->findByCallArgs($email['priority'], "priority", 'processemailqueue');

            if ($task && $task['force_next_run'] === null) {
                $this->basepackages->workers->tasks->forceNextRun(['id' => $task['id']]);
            }

            $this->addResponse('Re-queued');

            return;
        }

        $this->addResponse('Error re-queuing message');
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