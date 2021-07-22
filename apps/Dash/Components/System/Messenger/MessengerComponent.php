<?php

namespace Apps\Dash\Components\System\Messenger;

use Apps\Dash\Packages\System\Messenger\Messenger;
use System\Base\BaseComponent;

class MessengerComponent extends BaseComponent
{
    protected $messengerPackage;

    public function initialize()
    {
        $this->messengerPackage = $this->usePackage(Messenger::class);

        $this->accounts = $this->basepackages->accounts;

        $this->profile = $this->basepackages->profile;
    }

    public function viewAction()
    {
        //We can extend this to a full scale Messenger like Rocketchat.
        return false;
    }

    public function getMessagesAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->messengerPackage->getMessages($this->postData());

            $this->addResponse(
                $this->messengerPackage->packagesData->responseMessage,
                $this->messengerPackage->packagesData->responseCode,
                $this->messengerPackage->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function getUnreadMessagesCountAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->messengerPackage->getUnreadMessagesCount();

            $this->addResponse(
                $this->messengerPackage->packagesData->responseMessage,
                $this->messengerPackage->packagesData->responseCode,
                $this->messengerPackage->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function markAllMessagesReadAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->messengerPackage->markAllMessagesRead($this->postData());

            $this->addResponse(
                $this->messengerPackage->packagesData->responseMessage,
                $this->messengerPackage->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function addAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->messengerPackage->addMessage($this->postData());

            $this->addResponse(
                $this->messengerPackage->packagesData->responseMessage,
                $this->messengerPackage->packagesData->responseCode,
                $this->messengerPackage->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->messengerPackage->updateMessage($this->postData());

            $this->addResponse(
                $this->messengerPackage->packagesData->responseMessage,
                $this->messengerPackage->packagesData->responseCode,
                $this->messengerPackage->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function removeAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->messengerPackage->removeMessage($this->postData());

            $this->addResponse(
                $this->messengerPackage->packagesData->responseMessage,
                $this->messengerPackage->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function changeStatusAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->messengerPackage->changeStatus($this->postData());

            $this->addResponse(
                $this->messengerPackage->packagesData->responseMessage,
                $this->messengerPackage->packagesData->responseCode
            );

        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function changeSettingsAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->messengerPackage->changeSettings($this->postData());

            $this->addResponse(
                $this->messengerPackage->packagesData->responseMessage,
                $this->messengerPackage->packagesData->responseCode
            );

        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function searchAccountAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchAccounts = $this->accounts->searchAccountInternal($searchQuery);

                if ($searchAccounts) {
                    $currentAccount = $this->auth->account();

                    if ($currentAccount) {
                        $accounts = $this->accounts->packagesData->accounts;

                        foreach ($accounts as $accountKey => $account) {
                            if ($account['id'] == $currentAccount['id']) {
                                unset($accounts[$accountKey]);
                            }
                        }
                        // $accounts = array_values($accounts);

                        $this->addResponse(
                            $this->accounts->packagesData->responseMessage,
                            $this->accounts->packagesData->responseCode,
                            ['accounts' => $accounts]
                        );
                    } else {
                        $this->addResponse(
                            $this->accounts->packagesData->responseMessage,
                            $this->accounts->packagesData->responseCode,
                            ['accounts' => $this->accounts->packagesData->accounts]
                        );
                    }
                }
            } else {
                $this->addResponse('search query missing', 1);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}