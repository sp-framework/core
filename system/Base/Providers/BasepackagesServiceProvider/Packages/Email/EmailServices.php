<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Email;

use Phalcon\Filter\Validation\Validator\Between;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email\BasepackagesEmailServices;

class EmailServices extends BasePackage
{
    protected $modelToUse = BasepackagesEmailServices::class;

    protected $packageName = 'emailServices';

    public $emailServices;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        parent::init();

        return $this;
    }

    public function addEmailService(array $data)
    {
        $data = $this->encryptPass($data);

        $validate = $this->validateServiceData($data);

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if ($this->add($data)){
            $this->addActivityLog($data);

            $this->addResponse('Added new email service ' . $data['name'], 0, null, true);
        } else {
            $this->addResponse('Error adding new email service.', 1, []);
        }
    }

    public function updateEmailService(array $data)
    {
        $data = $this->encryptPass($data);

        $validate = $this->validateServiceData($data);

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        $emailService = $this->getById($data['id']);

        $emailService = array_merge($emailService, $data);

        if ($this->update($emailService)) {
            $this->addActivityLog($data, $emailService);

            $this->addResponse('Updated email service ' . $data['name']);
        } else {
            $this->addResponse('Error updating email service.', 1);
        }
    }

    public function removeEmailService(array $data)
    {
        $emailService = $this->getById($data['id']);

        //Check relations before removing.
        if ($this->remove($emailService['id'])) {
            $this->addResponse('Removed email service ' . $emailService['name']);
        } else {
            $this->addResponse('Error removing email service.', 1);
        }
    }

    /**
     * @notification(name=error)
     * @notification_allowed_methods(email)
     */
    public function errorEmailService($messageTitle = null, $messageDetails = null, $id = null)
    {
        if (!$messageTitle) {
            $messageTitle = 'Email Service has errors, contact administrator!';
        }

        $this->addToNotification('error', $messageTitle, $messageDetails, 'EmailServices', $id);
    }

    protected function validateServiceData(array $data)
    {
        $this->validation->init()->add('from_address', PresenceOf::class, ["message" => "Enter valid from email address."]);
        $this->validation->add('from_address', Email::class, ["message" => "Enter valid from email address."]);
        $this->validation->add('port', Between::class,
            [
                "minimum" => 1,
                "maximum" => 65535,
                "message" => "Enter port number between 1 and 65535."
            ]
        );

        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }
            return $messages;
        } else {
            return true;
        }
    }

    protected function validateTestData(array $data)
    {
        $this->validation->add('test_email_address', PresenceOf::class, ["message" => "Enter valid test email address."]);
        $this->validation->add('test_email_address', Email::class, ["message" => "Enter valid test email address."]);

        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }
            return $messages;
        } else {
            return true;
        }
    }

    public function testEmailService(array $data)
    {
        $validate = $this->validateTestData($data);

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        $test = $this->basepackages->email->sendTestEmail($data);

        if ($test) {
            $this->addResponse($this->basepackages->email->getDebugOutput());
        }
    }

    public function getById(int $id, bool $resetCache = false, bool $enableCache = true)
    {
        $emailService = parent::getById($id, $resetCache, $enableCache);

        if ($emailService) {
            return $this->decryptPass($emailService);
        }

        return false;
    }

    protected function decryptPass(array $data)
    {
        if ($data['password'] && $data['password'] != '') {
            $data['password'] = $this->crypt->decryptBase64($data['password'], $this->secTools->getSigKey());
        }

        return $data;
    }

    protected function encryptPass(array $data)
    {
        if ($data['password'] != '') {
            $data['password'] = $this->crypt->encryptBase64($data['password'], $this->secTools->getSigKey());
        }

        return $data;
    }
}