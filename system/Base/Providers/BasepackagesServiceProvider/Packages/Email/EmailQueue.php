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
        return $this;
    }

    public function addQueue(array $data)
    {
        if (isset($data['confidential']) && $data['confidential'] == 1) {
            $data = $this->encryptBody($data);
        }

        $queueEmailSettings = $this->basepackages->email->getEmailSettings();

        if (!isset($data['from'])) {
            $data['from'] = $queueEmailSettings['from_address'];

            if (isset($queueEmailSettings['from_name']) && $queueEmailSettings['from_name'] !== '') {
                $data['from'] = $data['from'] . '|' . $queueEmailSettings['from_name'];
            } else {
                $data['from'] = $queueEmailSettings['from_address'] . '|' . $queueEmailSettings['from_name'];
            }
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

    public function updateQueue(array $data)
    {
        $email = $this->getById($data['id']);

        if (!$email) {
            $this->addResponse('Email with ID not found', 1);

            return false;
        }

        $email['status'] = $data['status'];
        $email['priority'] = $data['priority'];

        if ($this->update($email)) {
            $this->addResponse('Updated');

            return;
        }

        $this->addResponse('Error removing email.', 1);
    }

    public function getLock()
    {
        return $this->queueLock;
    }

    public function processQueue($processPriority = 0, $confidential = false, $id = null)
    {
        $emailServices = $this->basepackages->emailservices->getAll()->emailServices;

        if (count($emailServices) === 0) {
            $this->addResponse('No email service available!', 1);

            return;
        }
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
                    if (isset($queueEmail['from']) && $queueEmail['from'] !== '') {
                        $queueEmail['from'] = str_replace(' ', '', $queueEmail['from']);//Trim
                        $fromAddressArr = explode('|', $queueEmail['from']);

                        if (count($fromAddressArr) === 2) {
                            if ($fromAddressArr[0] !== '' && $fromAddressArr[1] !== '') {
                                $this->basepackages->email->setSender($fromAddressArr[0], $fromAddressArr[1]);
                            }
                        } else if (count($fromAddressArr) === 1) {
                            if ($fromAddressArr[0] !== '') {
                                $this->basepackages->email->setSender($fromAddressArr[0], $fromAddressArr[0]);
                            }
                        }
                    } else {
                        $fromAddress = $queueEmailSettings['from_address'];

                        if (!isset($queueEmailSettings['from_name']) || (isset($queueEmailSettings['from_name']) && $queueEmailSettings['from_name'] === '')) {
                            $fromName = $queueEmailSettings['from_name'] = $queueEmailSettings['from_address'];
                        } else {
                            $fromName = $queueEmailSettings['from_name'];
                        }

                        $this->basepackages->email->setSender($fromAddress, $fromName);
                    }

                    //Set To
                    if (is_string($queueEmail['to_addresses'])) {
                        $queueEmail['to_addresses'] = $this->helper->decode($queueEmail['to_addresses'], true);
                    }
                    foreach ($queueEmail['to_addresses'] as $toAddress) {
                        $toAddress = str_replace(' ', '', $toAddress);//Trim
                        $toAddressArr = explode('|', $toAddress);

                        if (count($toAddressArr) === 2) {
                            if ($toAddressArr[0] !== '' && $toAddressArr[1] !== '') {
                                $this->basepackages->email->setRecipientTo($toAddressArr[0], $toAddressArr[1]);
                            }
                        } else if (count($toAddressArr) === 1) {
                            if ($toAddressArr[0] !== '') {
                                $this->basepackages->email->setRecipientTo($toAddressArr[0], $toAddressArr[0]);
                            }
                        }
                    }

                    //Set CC
                    if (isset($queueEmail['cc_addresses'])) {
                        if (is_string($queueEmail['cc_addresses'])) {
                            $queueEmail['cc_addresses'] = $this->helper->decode($queueEmail['cc_addresses'], true);
                        }
                        foreach ($queueEmail['cc_addresses'] as $ccAddress) {
                            $ccAddress = str_replace(' ', '', $ccAddress);//Trim
                            $ccAddressArr = explode('|', $ccAddress);

                            if (count($ccAddressArr) === 2) {
                                if ($ccAddressArr[0] !== '' && $ccAddressArr[1] !== '') {
                                    $this->basepackages->email->setRecipientCc($ccAddressArr[0], $ccAddressArr[1]);
                                }
                            } else if (count($ccAddressArr) === 1) {
                                if ($ccAddressArr[0] !== '') {
                                    $this->basepackages->email->setRecipientCc($ccAddressArr[0], $ccAddressArr[0]);
                                }
                            }
                        }
                    }

                    //Set BCC
                    if (isset($queueEmail['bcc_addresses'])) {
                        if (is_string($queueEmail['bcc_addresses'])) {
                            $queueEmail['bcc_addresses'] = $this->helper->decode($queueEmail['bcc_addresses'], true);
                        }
                        foreach ($queueEmail['bcc_addresses'] as $bccAddress) {
                            $bccAddress = str_replace(' ', '', $bccAddress);//Trim
                            $bccAddressArr = explode('|', $bccAddress);

                            if (count($bccAddressArr) === 2) {
                                if ($bccAddressArr[0] !== '' && $bccAddressArr[1] !== '') {
                                    $this->basepackages->email->setRecipientBcc($bccAddressArr[0], $bccAddressArr[1]);
                                }
                            } else if (count($bccAddressArr) === 1) {
                                if ($bccAddressArr[0] !== '') {
                                    $this->basepackages->email->setRecipientBcc($bccAddressArr[0], $bccAddressArr[0]);
                                }
                            }
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

                    } else {
                        $queueEmail['status'] = self::STATUS_ERROR;

                        $queueEmail['logs'] = $logs;
                    }

                    array_push($queueProcessedIds, $queueEmail['id']);

                    $this->update($queueEmail);
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

    public function reQueue(array $data)
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

        $this->addResponse('Error re-queuing message', 1);
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