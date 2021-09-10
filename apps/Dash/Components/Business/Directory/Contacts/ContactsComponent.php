<?php

namespace Apps\Dash\Components\Business\Directory\Contacts;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\Contacts\Contacts;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ContactsComponent extends BaseComponent
{
    use DynamicTable;

    protected $contacts;

    public function initialize()
    {
        $this->contacts = $this->usePackage(Contacts::class);

        $this->notes = $this->basepackages->notes;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->contactSources = $this->contacts->getContactSources();

            $this->view->portraitLink = '';

            if ($this->getData()['id'] != 0) {

                $contact = $this->contacts->getContactById($this->getData()['id']);


                $contact['notes'] = $this->notes->getNotes('contacts', $this->getData()['id']);

                $vendors = $this->usePackage(Vendors::class);

                $vendorArr[] = $vendors->getVendorById($contact['vendor_id']);

                $this->view->vendor = $vendorArr;

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
                    $contact['contact_manager_full_name'] = $this->contacts->getContactById($contact['contact_manager_id'])['full_name'];
                } else {
                    $contact['contact_manager_full_name'] = '';
                }

                if ($contact['contact_referrer_id'] && $contact['contact_referrer_id'] != 0) {
                    $contact['contact_referrer_full_name'] = $this->contacts->getContactById($contact['contact_referrer_id'])['full_name'];
                } else {
                    $contact['contact_referrer_full_name'] = '';
                }

                $this->view->contact = $contact;
            } else {
                $contact = [];
                $contact['address_ids'] = [];
                $this->view->contact = $contact;
                $this->view->vendor = [];
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

        if ($this->request->isPost()) {
            $replaceColumns =
                function ($dataArr) {
                    if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                        return $this->replaceColumns($dataArr);
                    }

                    return $dataArr;
                };
        } else {
            $replaceColumns = null;
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
            ['account_id', 'first_name', 'last_name', 'contact_phone', 'contact_mobile', 'account_email'],
            true,
            ['account_id', 'first_name', 'last_name', 'contact_phone', 'contact_mobile', 'account_email'],
            $controlActions,
            ['account_id'=>'account', 'account_email'=>'email', 'contact_mobile'=>'mobile'],
            $replaceColumns,
            'first_name'
        );

        $this->view->pick('contacts/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->generateAccountLink($dataKey, $data);
        }

        return $dataArr;
    }

    protected function generateAccountLink($rowId, $data)
    {
        if ($data['account_id'] && $data['account_id'] != '0') {
            $data['account_id'] =
                '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-access-' . $rowId . '" href="' .  $this->links->url('system/users/accounts/q/id/' . $data['account_id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="text-white btn btn-primary btn-xs rowAccess text-uppercase contentAjaxLink">
                    <i class="fas fa-fw fa-xs fa-external-link-alt"></i>
                </a>';
        } else {
            $data['account_id'] = '-';
        }

        return $data;
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

            $this->addResponse(
                $this->contacts->packagesData->responseMessage,
                $this->contacts->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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

            $this->addResponse(
                $this->contacts->packagesData->responseMessage,
                $this->contacts->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->contacts->removeContact($this->postData());

            $this->addResponse(
                $this->contacts->packagesData->responseMessage,
                $this->contacts->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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