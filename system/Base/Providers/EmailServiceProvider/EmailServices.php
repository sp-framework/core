<?php

namespace System\Base\Providers\EmailServiceProvider;

use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\EmailServiceProvider\Model\EmailServices as EmailServicesModel;

class EmailServices extends BasePackage
{
    protected $modelToUse = EmailServicesModel::class;

    protected $packageName = 'emailservices';

    public $emailservices;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function addEmailService(array $data)
    {
        $add = $this->add($data);

        if ($add) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = $data['name'] . ' email service added';

            return true;
        }
        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Error adding email service ';

        return false;
    }

    public function updateEmailService(array $data)
    {
        $update = $this->update($data);

        if ($update) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = $data['name'] . ' email service updated';

            return true;
        }
        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Error updating email service ';

        return false;
    }

    public function removeEmailService(array $data)
    {
        $emailService = $this->getById($data['id']);

        if ($emailService) {
            $remove = $this->remove($emailService['id']);

            if ($remove) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = $emailService['name'] . ' email service removed';

                return true;
            }
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing email service';

            return false;
        }
        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Email Service with Id ' . $data['id'] . ' not found';

        return false;
    }

    protected function validateData(array $data)
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
        $validate = $this->validateData($data);

        if ($validate !== true) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $validate;

            return false;
        }

        $test = $this->email->sendTestEmail($data);

        if ($test) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = $this->email->getDebugOutput();
        }
    }
}