<?php

namespace Apps\Dash\Packages\Crms\Customers;

use Apps\Dash\Packages\Crms\Customers\Model\CrmsCustomers;
use Apps\Dash\Packages\Crms\Customers\Model\CrmsCustomersFinancialDetails;
use Apps\Dash\Packages\Hrms\Employees\Employees;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Customers extends BasePackage
{
    protected $modelToUse = CrmsCustomers::class;

    protected $packageName = 'customers';

    public $customers;

    public function getCustomerById(int $id)
    {
        $customerModel = new $this->modelToUse;

        $customerObj = $customerModel::findFirstById($id);

        $customer = $customerObj->toArray();

        $financialDetailsObj = $customerObj->getFinancial_details();

        if ($financialDetailsObj) {
            $financialDetails = $financialDetailsObj->toArray();

            unset($financialDetails['id']);
            unset($financialDetails['cc_details']);

            $customer = array_merge($customer, $financialDetails);
        }

        return $customer;
    }

    /**
     * @notification(name=add)
     */
    public function addCustomer(array $data)
    {
        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];

        if (isset($data['address_ids'])) {
            $data = $this->updateAddresses($data);
        }

        if ($this->add($data)) {
            if (isset($data['portrait'])) {
                $this->basepackages->storages->changeOrphanStatus($data['portrait']);
            }

            $data['account_id'] = $this->addUpdateAccount($this->packagesData->last);

            $data['id'] = $this->packagesData->last['id'];

            $this->update($data);

            $this->addFinancialDetails($data);

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addResponse('Added ' . $data['full_name'] . ' customer');

            $this->addToNotification('add', 'Added new customer ' . $data['full_name']);
        } else {
            $this->addResponse('Error adding new customer.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateCustomer(array $data)
    {
        $customer = $this->getById($data['id']);

        $data['account_id'] = $customer['account_id'];

        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];

        if (isset($data['address_ids'])) {
            $data = $this->updateAddresses($data);
        }

        if (isset($data['delete_address_ids']) && $data['delete_address_ids'] !== '') {
            $data['delete_address_ids'] = Json::decode($data['delete_address_ids'], true);
            if (count($data['delete_address_ids']) > 0) {
                $this->deleteAddresses($data['delete_address_ids']);
            }
        }

        if ($this->update($data)) {
            if (isset($data['portrait'])) {
                $this->basepackages->storages->changeOrphanStatus($data['portrait'], $customer['portrait']);
            }

            $this->updateFinancialDetails($data);

            $data['account_id'] = $this->addUpdateAccount($data);

            $this->update($data);

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addResponse('Updated ' . $data['full_name'] . ' customer');

            $this->addToNotification('update', 'Updated customer ' . $data['full_name']);
        } else {
            $this->addResponse('Error updating customer.', 1);
        }
    }

    /**
     * @notification(name=remove)
     */
    public function removeCustomer(array $data)
    {
        $customer = $this->getById($data['id']);
        //Check relations before removing.
        //Remove Address

        if ($this->remove($data['id'])) {
            if ($customer['portrait'] !== '') {
                $this->basepackages->storages->changeOrphanStatus(null, $customer['portrait']);
            }

            $this->basepackages->accounts->removeAccount(['id' => $customer['account_id']]);

            $this->addResponse('Removed customer');
        } else {
            $this->addResponse('Error removing customer.', 1);
        }
    }

    protected function addUpdateAccount($data)
    {
        $data['package_name'] = 'customers';
        $data['package_row_id'] = $data['id'];

        unset($data['id']);

        $data['email'] = $data['account_email'];

        if (isset($data['account_id']) &&
            $data['account_id'] != '' &&
            $data['account_id'] != '0'
        ) {
            $data['id'] = $data['account_id'];

            try {
                $this->basepackages->accounts->updateAccount($data);

                return $this->basepackages->accounts->packagesData->packagesData['last']['id'];
            } catch (\Exception $e) {
                $this->addResponse('Error adding/updating customer account. Please contact administrator', 1);
            }
        } else {
            $data['role_id'] = '0';
            $data['override_role'] = '0';
            $data['permissions'] = '[]';
            $data['can_login'] = '';

            try {
                $this->basepackages->accounts->addAccount($data);

                return $this->basepackages->accounts->packagesData->packagesData['last']['id'];
            } catch (\Exception $e) {
                $this->addResponse('Error adding/updating customer account. Please contact administrator', 1);
            }
        }
    }

    protected function addFinancialDetails(array $data)
    {
        $this->modelToUse = CrmsCustomersFinancialDetails::class;

        unset($data['id']);

        $this->add($data);

        $this->modelToUse = CrmsCustomers::class;
    }

    protected function updateFinancialDetails(array $data)
    {
        $this->modelToUse = CrmsCustomersFinancialDetails::class;

        $financialDetailsModel = $this->modelToUse::findFirst(
            [
                'conditions'    => 'customer_id = :vid:',
                'bind'          => [
                    'vid'       => $data['id']
                ]
            ]
        );

        if ($financialDetailsModel) {
            unset($data['id']);

            $financialDetails = $financialDetailsModel->toArray();

            $financialDetails = array_merge($financialDetails, $data);

            $this->update($financialDetails);
        }

        $this->modelToUse = CrmsCustomers::class;
    }

    protected function checkCustomerDuplicate($name)
    {
        return $this->modelToUse::findFirst(
            [
                'conditions'    => 'business_name = :name:',
                'bind'          =>
                [
                    'name'      => $name
                ]
            ]
        );
    }

    protected function updateAddresses($data)
    {
        if ($data['address_ids'] !== '') {
            $data['address_ids'] = Json::decode($data['address_ids'], true);

            $addressesIds = [];
            if (count($data['address_ids']) > 0) {
                foreach ($data['address_ids'] as $addressTypeKey => $addressType) {
                    $addressesIds[$addressTypeKey] = [];

                    if (is_array($addressType) && count($addressType) > 0) {
                        foreach ($addressType as $addressKey => $address) {

                            $address['name'] = $data['business_name'];
                            $address['address_type'] = $addressTypeKey;
                            $address['package_name'] = $this->packageName;

                            if ($address['seq'] == 0) {
                                $address['is_primary'] = 1;
                            } else {
                                $address['is_primary'] = 0;
                            }

                            if ($address['new'] == 1) {
                                $this->basepackages->addressbook->addAddress($address);
                            } else {
                                $address['id'] = $addressKey;
                                $this->basepackages->addressbook->updateAddress($address);
                            }
                            array_push($addressesIds[$addressTypeKey], $this->basepackages->addressbook->packagesData->last['id']);
                        }
                    }
                }
            }
        }

        $data['address_ids'] = Json::encode($addressesIds);

        return $data;
    }

    protected function deleteAddresses($ids)
    {
        foreach ($ids as $id) {
            $this->basepackages->addressbook->removeAddress(['id' => $id]);
        }
    }

    public function searchByFullName(string $nameQueryString)
    {
        $searchCustomers =
            $this->getByParams(
                [
                    'conditions'    => 'full_name LIKE :fullName:',
                    'bind'          => [
                        'fullName'     => '%' . $nameQueryString . '%'
                    ]
                ]
            );

        if (count($searchCustomers) > 0) {
            $customers = [];

            foreach ($searchCustomers as $customerKey => $customerValue) {
                $customers[$customerKey]['id'] = $customerValue['id'];
                $customers[$customerKey]['full_name'] = $customerValue['full_name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->customers = $customers;

            return true;
        }
    }

    public function searchByEmail(string $nameQueryString)
    {
        $searchCustomers =
            $this->getByParams(
                [
                    'conditions'            => 'account_email LIKE :accountEmail:',
                    'bind'                  => [
                        'accountEmail'      => '%' . $nameQueryString . '%'
                    ]
                ]
            );

        if (count($searchCustomers) > 0) {
            $customers = [];

            foreach ($searchCustomers as $customerKey => $customerValue) {
                $customers[$customerKey]['id'] = $customerValue['id'];
                $customers[$customerKey]['account_email'] = $customerValue['account_email'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->customers = $customers;

            return true;
        }
    }

    public function getPaymentTerms()
    {
        return
            [
                [
                    'id'    => 'DAYSAFTERBILLDATE',
                    'name'  => 'Day(s) after bill date',
                ],
                [
                    'id'    => 'DAYSAFTERBILLMONTH',
                    'name'  => 'Day(s) after bill month',
                ],
                [
                    'id'    => 'OFCURRENTMONTH',
                    'name'  => 'Of the current month'
                ],
                [
                    'id'    => 'OFFOLLOWINGMONTH',
                    'name'  => 'Of the following month'
                ]
            ];
    }

    public function getContactSources()
    {
        return
            [
                [
                    'id'            => 'phone_call',
                    'name'          => 'Phone Call'
                ],
                [
                    'id'            => 'email',
                    'name'          => 'Email'
                ],
                [
                    'id'            => 'website',
                    'name'          => 'Website'
                ],
                [
                    'id'            => 'tv',
                    'name'          => 'Tv'
                ],
                [
                    'id'            => 'radio',
                    'name'          => 'Radio'
                ],
                [
                    'id'            => 'walk_ins',
                    'name'          => 'Walk Ins'
                ],
                [
                    'id'            => 'contact_referrer',
                    'name'          => 'Contact Referrer'
                ]
            ];
    }
}