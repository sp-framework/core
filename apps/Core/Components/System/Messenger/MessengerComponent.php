<?php

namespace Apps\Core\Components\System\Messenger;

use System\Base\BaseComponent;

class MessengerComponent extends BaseComponent
{
    public function initialize()
    {
        $this->accounts = $this->basepackages->accounts;
    }

    public function viewAction()
    {
        //We can extend this to a full scale Messenger like Rocketchat.
        return false;
    }

    public function getMessagesAction()
    {
        $this->requestIsPost();

        $this->basepackages->messenger->getMessages($this->postData());

        $this->addResponse(
            $this->basepackages->messenger->packagesData->responseMessage,
            $this->basepackages->messenger->packagesData->responseCode,
            $this->basepackages->messenger->packagesData->responseData
        );
    }

    public function getUnreadMessagesCountAction()
    {
        $this->requestIsPost();

        if ($this->basepackages->messenger->getUnreadMessagesCount()) {
            $this->addResponse(
                $this->basepackages->messenger->packagesData->responseMessage,
                $this->basepackages->messenger->packagesData->responseCode,
                $this->basepackages->messenger->packagesData->responseData
            );
        }
    }

    public function markAllMessagesReadAction()
    {
        $this->requestIsPost();

        $this->basepackages->messenger->markAllMessagesRead($this->postData());

        $this->addResponse(
            $this->basepackages->messenger->packagesData->responseMessage,
            $this->basepackages->messenger->packagesData->responseCode
        );
    }

    public function addAction()
    {
        $this->requestIsPost();

        $this->basepackages->messenger->addMessage($this->postData());

        $this->addResponse(
            $this->basepackages->messenger->packagesData->responseMessage,
            $this->basepackages->messenger->packagesData->responseCode,
            $this->basepackages->messenger->packagesData->responseData
        );
    }

    public function updateAction()
    {
        $this->requestIsPost();

        $this->basepackages->messenger->updateMessage($this->postData());

        $this->addResponse(
            $this->basepackages->messenger->packagesData->responseMessage,
            $this->basepackages->messenger->packagesData->responseCode,
            $this->basepackages->messenger->packagesData->responseData
        );
    }

    public function removeAction()
    {
        $this->requestIsPost();

        $this->basepackages->messenger->removeMessage($this->postData());

        $this->addResponse(
            $this->basepackages->messenger->packagesData->responseMessage,
            $this->basepackages->messenger->packagesData->responseCode
        );
    }

    public function changeStatusAction()
    {
        $this->requestIsPost();

        $this->basepackages->messenger->changeStatus($this->postData());

        $this->addResponse(
            $this->basepackages->messenger->packagesData->responseMessage,
            $this->basepackages->messenger->packagesData->responseCode
        );
    }

    public function changeSettingsAction()
    {
        $this->requestIsPost();

        $this->basepackages->messenger->changeSettings($this->postData());

        $this->addResponse(
            $this->basepackages->messenger->packagesData->responseMessage,
            $this->basepackages->messenger->packagesData->responseCode
        );
    }

    public function searchAccountAction()
    {
        $this->requestIsPost();

        if ($this->postData()['search']) {
            $searchQuery = $this->postData()['search'];

            if (strlen($searchQuery) < 3) {
                return;
            }

            $searchAccounts = $this->accounts->searchAccountInternal($searchQuery);

            if ($searchAccounts) {
                $currentAccount = $this->access->auth->account();

                if ($currentAccount) {
                    $accounts = $this->accounts->packagesData->accounts;

                    foreach ($accounts as $accountKey => &$account) {
                        if ($account['id'] == $currentAccount['id']) {
                            unset($accounts[$accountKey]);
                            continue;
                        }

                        $profile = $this->basepackages->profiles->getProfile($account['id']);

                        $account['name'] = $profile['full_name'];
                        $account['portrait'] = $profile['portrait'];
                        if (isset($profile['settings']['messenger']['status'])) {
                            $account['status'] = $profile['settings']['messenger']['status'];
                        } else {
                            $account['status'] = 4;
                        }
                        $account['user'] = $account['id'];
                    }

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
            } else {
                $this->addResponse(
                    $this->accounts->packagesData->responseMessage,
                    $this->accounts->packagesData->responseCode,
                    ['accounts' => []]
                );
            }
        } else {
            $this->addResponse('search query missing', 1);
        }
    }

    public function addUserToMembersUsersAction()
    {
        $this->requestIsPost();

        $this->basepackages->profiles->addUserToMembersUsers($this->postData());

        $this->addResponse(
            $this->basepackages->profiles->packagesData->responseMessage,
            $this->basepackages->profiles->packagesData->responseCode
        );
    }
}