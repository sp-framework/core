<?php

namespace Apps\Dash\Components\Crms\Customers;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Finances\Taxes\Taxes;
use Apps\Dash\Packages\Crms\CustomerGroups\CustomerGroups;
use Apps\Dash\Packages\Crms\Customers\Customers;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class CustomersComponent extends BaseComponent
{
    use DynamicTable;

    protected $customers;

    public function initialize()
    {
        $this->customers = $this->usePackage(Customers::class);

        $this->notes = $this->basepackages->notes;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->customergroups = $this->usePackage(CustomerGroups::class)->getAll()->customergroups;

            //This should be moved to payments package
            $this->view->paymentTerms = $this->customers->getPaymentTerms();

            $this->view->contactSources = $this->customers->getContactSources();

            $this->view->portraitLink = '';

            $this->view->taxes = $this->usePackage(Taxes::class)->getAll()->taxes;

            if ($this->getData()['id'] != 0) {

                $customer = $this->customers->getCustomerById($this->getData()['id']);

                $customer['activityLogs'] = $this->customers->getActivityLogs($this->getData()['id']);

                $customer['notes'] = $this->notes->getNotes('customers', $this->getData()['id']);

                if ($customer['address_ids'] && $customer['address_ids'] !== '') {
                    $customer['address_ids'] = Json::decode($customer['address_ids'], true);

                    foreach ($customer['address_ids'] as $addressTypeKey => $addressType) {
                        if (is_array($addressType) && count($addressType) > 0) {
                            foreach ($addressType as $addressKey => $address) {
                                $customer['address_ids'][$addressTypeKey][$addressKey] =
                                    $this->basepackages->addressbook->getById($address);
                            }
                        }
                        $customer['address_ids'][$addressTypeKey] =
                            msort($customer['address_ids'][$addressTypeKey], 'is_primary', SORT_REGULAR, SORT_DESC);
                    }
                }

                $storages = $this->basepackages->storages;

                if ($customer['portrait'] && $customer['portrait'] !== '') {
                    $this->view->portraitLink = $storages->getPublicLink($customer['portrait'], 200);
                }

                if ($customer['contact_referrer_id'] && $customer['contact_referrer_id'] != 0) {
                    $customer['contact_referrer_full_name'] = $this->customers->getContactById($customer['contact_referrer_id'])['full_name'];
                } else {
                    $customer['contact_referrer_full_name'] = '';
                }

                $this->view->customer = $customer;
            } else {
                $customer = [];

                $customer['address_ids'] = [];

                $this->view->customer = $customer;
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->isEnabled()) {
                $this->view->geo = true;
            } else {
                $this->view->geo = false;
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->currencyEnabled()) {
                $this->view->currency = true;
            } else {
                $this->view->currency = false;
            }

            $this->view->currencies = $this->basepackages->geoCountries->currencyEnabled(true);

            $storages = $this->basepackages->storages->getAppStorages();

            if ($storages && isset($storages['private'])) {
                $this->view->storages = $storages['private'];
            } else {
                $this->view->storages = [];
            }

            $this->view->pick('customers/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'crms/customers',
                    'remove'    => 'crms/customers/remove'
                ]
            ];

        $this->generateDTContent(
            $this->customers,
            'crms/customers/view',
            null,
            ['first_name', 'last_name', 'contact_mobile', 'account_email'],
            true,
            ['first_name', 'last_name', 'contact_mobile', 'account_email'],
            $controlActions,
            ['account_email'=>'email', 'contact_mobile'=>'mobile'],
            null,
            'first_name'
        );

        $this->view->pick('customers/list');
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

            $this->customers->addCustomer($this->postData());

            $this->addResponse(
                $this->customers->packagesData->responseMessage,
                $this->customers->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
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

            $this->customers->updateCustomer($this->postData());

            $this->addResponse(
                $this->customers->packagesData->responseMessage,
                $this->customers->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->customers->removeCustomer($this->postData());

            $this->addResponse(
                $this->customers->packagesData->responseMessage,
                $this->customers->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }

    public function searchCustomerEmailAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchCustomer = $this->customers->searchByEmail($searchQuery);

                if ($searchCustomer) {
                    $this->view->responseCode = $this->customers->packagesData->responseCode;

                    $this->view->customers = $this->customers->packagesData->customers;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    public function searchCustomerFullNameAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchCustomer = $this->customers->searchByFullName($searchQuery);

                if ($searchCustomer) {
                    $this->view->responseCode = $this->customers->packagesData->responseCode;

                    $this->view->customers = $this->customers->packagesData->customers;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    public function searchCustomerIdAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['customerId']) {
                $customerId = $this->postData()['customerId'];

                $searchCustomer = $this->customers->searchByCustomerId($customerId);

                if ($searchCustomer) {
                    $this->view->responseCode = $this->customers->packagesData->responseCode;

                    $this->view->customer = $this->customers->packagesData->customer;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'customer id missing';
            }
        }
    }
}