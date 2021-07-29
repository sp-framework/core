<?php

namespace Apps\Dash\Components\Business\Directory\Contacts;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\Contacts\Contacts;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ContactsComponent extends BaseComponent
{
    use DynamicTable;

    protected $contacts;

    public function initialize()
    {
        $this->contacts = $this->usePackage(Contacts::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->portraitLink = '';

            if ($this->getData()['id'] != 0) {

                $contact = $this->contacts->getById($this->getData()['id']);

                if ($contact['address_ids'] && $contact['address_ids'] !== '') {
                    $contact['address_ids'] = Json::decode($contact['address_ids'], true);

                    foreach ($contact['address_ids'] as $addressTypeKey => $addressType) {
                        if (is_array($addressType) && count($addressType) > 0) {
                            foreach ($addressType as $addressKey => $address) {
                                $contact['address_ids'][$addressTypeKey][$addressKey] =
                                    $this->basepackages->addressbook->getById($address);
                            }
                        }
                        $contact['address_ids'][$addressTypeKey] =
                            msort($contact['address_ids'][$addressTypeKey], 'is_primary', SORT_REGULAR, SORT_DESC);
                    }
                }
                $storages = $this->basepackages->storages;

                if ($contact['portrait'] && $contact['portrait'] !== '') {
                    $this->view->portraitLink = $this->links->url('system/storages/q/uuid/' . $contact['portrait'] . '/w/200');
                }

                if ($contact['contact_manager_id'] && $contact['contact_manager_id'] != 0) {
                    $contact['contact_manager_full_name'] = $this->contacts->getById($contact['contact_manager_id'])['full_name'];
                } else {
                    $contact['contact_manager_full_name'] = '';
                }

                if ($contact['contact_referrer_id'] && $contact['contact_referrer_id'] != 0) {
                    $contact['contact_referrer_full_name'] = $this->contacts->getById($contact['contact_referrer_id'])['full_name'];
                } else {
                    $contact['contact_referrer_full_name'] = '';
                }

                $this->view->contact = $contact;
            } else {
                $contact = [];
                $contact['address_ids'] = [];
                $this->view->contact = $contact;
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->isEnabled()) {
                $this->view->geo = true;
            } else {
                $this->view->geo = false;
            }

            $storages = $this->basepackages->storages->getAppStorages();

            if ($storages && isset($storages['private'])) {
                $this->view->storages = $storages['private'];
            } else {
                $this->view->storages = [];
            }

            $this->view->pick('contacts/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'business/directory/contacts',
                    'remove'    => 'business/directory/contacts/remove'
                ]
            ];

        $this->generateDTContent(
            $this->contacts,
            'business/directory/contacts/view',
            null,
            ['full_name', 'contact_phone', 'contact_mobile', 'account_email'],
            true,
            ['full_name', 'contact_phone', 'contact_mobile', 'account_email'],
            $controlActions,
            ['account_email'=>'email','contact_phone'=>'phone', 'contact_mobile'=>'mobile'],
            null,
            'full_name'
        );

        $this->view->pick('contacts/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->contacts->addContact($this->postData());

            $this->view->responseCode = $this->contacts->packagesData->responseCode;

            $this->view->responseMessage = $this->contacts->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->contacts->updateContact($this->postData());

            $this->view->responseCode = $this->contacts->packagesData->responseCode;

            $this->view->responseMessage = $this->contacts->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->contacts->removeContact($this->postData());

            $this->view->responseCode = $this->contacts->packagesData->responseCode;

            $this->view->responseMessage = $this->contacts->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchContactEmailAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchContact = $this->contacts->searchByEmail($searchQuery);

                if ($searchContact) {
                    $this->view->responseCode = $this->contacts->packagesData->responseCode;

                    $this->view->contacts = $this->contacts->packagesData->contacts;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    public function searchContactFullNameAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchContact = $this->contacts->searchByFullName($searchQuery);

                if ($searchContact) {
                    $this->view->responseCode = $this->contacts->packagesData->responseCode;

                    $this->view->contacts = $this->contacts->packagesData->contacts;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    public function searchContactIdAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['id']) {
                $searchContact = $this->contacts->searchById($this->postData()['id']);

                $this->view->responseCode = $this->contacts->packagesData->responseCode;

                $this->view->contact = $this->contacts->packagesData->contact;
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search id missing';
            }
        }
    }
}