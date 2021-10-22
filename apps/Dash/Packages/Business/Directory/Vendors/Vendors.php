<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors;

use Apps\Dash\Packages\Business\Directory\Contacts\Contacts;
use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendors;
use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendorsFinancialDetails;
use Apps\Dash\Packages\Hrms\Employees\Employees;
use Apps\Dash\Packages\Ims\Brands\Brands;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Vendors extends BasePackage
{
    protected $modelToUse = BusinessDirectoryVendors::class;

    protected $packageName = 'vendors';

    public $vendors;

    public function getVendorById(int $id)
    {
        $vendorObj = $this->getFirst('id', $id);

        if ($vendorObj) {
            $vendor = $vendorObj->toArray();

            $vendor['address_ids'] = [];
            $vendor['contact_ids'] = [];
            $vendor['b2bAccountManagers'] = [];
            $vendor['notes'] = [];
            $vendor['activityLogs'] = [];

            if ($vendorObj->getAddresses()) {
                $vendorAddresses = $vendorObj->getAddresses()->toArray();

                if (count($vendorAddresses) > 0) {
                    foreach ($vendorAddresses as $vendorAddress) {
                        if (!isset($vendor['address_ids'][$vendorAddress['address_type']])) {
                            $vendor['address_ids'][$vendorAddress['address_type']] = [];
                        }

                        array_push($vendor['address_ids'][$vendorAddress['address_type']], $vendorAddress);
                    }

                    foreach ($vendor['address_ids'] as $addressTypeKey => $addressTypeAddresses) {
                        $vendor['address_ids'][$addressTypeKey] =
                            msort($vendor['address_ids'][$addressTypeKey], 'is_primary', SORT_REGULAR, SORT_DESC);
                    }
                }
            }

            if ($vendorObj->getContacts()) {
                $vendor['contact_ids'] = $vendorObj->getContacts()->toArray();
            }

            if ($vendor['b2b_account_managers'] && $vendor['b2b_account_managers'] !== '') {
                $vendor['b2b_account_managers'] = Json::decode($vendor['b2b_account_managers'], true);

                if (count($vendor['b2b_account_managers']) > 0) {
                    $employees = $this->usePackage(Employees::class);

                    foreach ($vendor['b2b_account_managers'] as $employeeKey => $employee) {
                        if ($employees->searchById($employee)) {
                            $vendor['b2bAccountManagers'][$employeeKey]['id'] = $employees->packagesData->employee['id'];
                            $vendor['b2bAccountManagers'][$employeeKey]['full_name'] = $employees->packagesData->employee['full_name'];
                        }
                    }
                }
            }

            $vendor['activityLogs'] = $this->getActivityLogs($vendor['id']);

            $vendor['notes'] = $this->basepackages->notes->getNotes('vendors', $vendor['id']);

            $financialDetailsObj = $vendorObj->getFinancial_details();

            $financialDetails = $financialDetailsObj->toArray();
            unset($financialDetails['id']);

            return array_merge($vendor, $financialDetails);
        }

        return false;
    }

    /**
     * @notification(name=add)
     */
    public function addVendor(array $data)
    {
        if ($this->checkVendorDuplicate($data['business_name'])) {
            $this->addResponse('Vendor ' . $data['business_name'] . ' already exists.', 1);

            return;
        }

        $data = $this->addBrands($data);

        $data = $this->addB2bAccountManagers($data);

        if (isset($data['contact_phone'])) {
            $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        }
        if (isset($data['contact_fax'])) {
            $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);
        }

        if ($this->add($data)) {
            if (isset($data['logo'])) {
                $this->basepackages->storages->changeOrphanStatus($data['logo']);
            }

            $data['vendor_id'] = $this->packagesData->last['id'];

            $this->updateAddresses($data);

            $this->addFinancialDetails($data);

            $data['id'] = $this->packagesData->last['id'];

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addActivityLog($data);

            $this->addResponse('Added new vendor ' . $data['business_name']);

            $this->addToNotification('add', 'Added new vendor ' . $data['business_name']);
        } else {
            $this->addResponse('Error adding new vendor.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateVendor(array $data)
    {
        $vendor = $this->getById($data['id']);

        if ($data['business_name'] !== $vendor['business_name']) {
            if ($this->checkVendorDuplicate($data['business_name'])) {
                $this->addResponse('Vendor ' . $data['business_name'] . ' already exists.', 1);

                return;
            }
        }

        $data = $this->addBrands($data);

        $data = $this->addB2bAccountManagers($data);

        if (isset($data['contact_phone'])) {
            $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        }
        if (isset($data['contact_fax'])) {
            $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);
        }

        if ($this->update($data)) {
            if ($data['is_b2b_customer'] == '0') {
                $this->disableVendorContactAccounts($data['id']);
            }

            $data['vendor_id'] = $data['id'];

            $this->updateAddresses($data);

            if (isset($data['delete_address_ids']) && $data['delete_address_ids'] !== '') {
                $data['delete_address_ids'] = Json::decode($data['delete_address_ids'], true);
                if (count($data['delete_address_ids']) > 0) {
                    $this->deleteAddresses($data['delete_address_ids']);
                }
            }

            $this->updateFinancialDetails($data);

            if (isset($data['logo'])) {
                $this->basepackages->storages->changeOrphanStatus($data['logo'], $vendor['logo']);
            }

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addActivityLog($data, $vendor);

            $this->addResponse('Updated vendor ' . $data['business_name']);

            $this->addToNotification('update', 'Updated vendor ' . $data['business_name']);
        } else {
            $this->addResponse('Error updating vendor.', 1);
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function removeVendor(array $data)
    {
        $vendorObj = $this->getFirst('id', $data['id']);

        $vendor = $vendorObj->toArray();

        if ($vendorObj->getContacts()->count() > 0) {
            $this->addResponse('Vendor has contacts. Error removing vendor ' . $vendor['business_name'], 1);

            return;
        }

        if ($this->remove($data['id'])) {
            if (isset($data['logo'])) {
                $this->basepackages->storages->changeOrphanStatus(null, $vendor['logo']);
            }

            $this->addResponse('Removed vendor ' . $vendor['business_name']);

            $this->addToNotification('remove', 'Removed vendor ' . $vendor['business_name']);
        } else {
            $this->addResponse('Error removing vendor.', 1);
        }
    }

    /**
     * @notification(name=error)
     */
    public function errorVendor($messageTitle = null, $messageDetails = null, $id = null)
    {
        if (!$messageTitle) {
            $messageTitle = 'Contact has errors, contact administrator!';
        }

        $this->addToNotification('error', $messageTitle, $messageDetails, null, $id);
    }

    public function updateAddresses($data)
    {
        if (isset($data['address_ids']) && $data['address_ids'] !== '') {
            $data['address_ids'] = Json::decode($data['address_ids'], true);

            if (count($data['address_ids']) > 0) {
                foreach ($data['address_ids'] as $addressTypeKey => $addressType) {
                    $addressesIds[$addressTypeKey] = [];

                    if (is_array($addressType) && count($addressType) > 0) {
                        foreach ($addressType as $addressKey => $address) {

                            $address['address_type'] = $addressTypeKey;
                            $address['package_name'] = $this->packageName;
                            $address['package_row_id'] = $data['vendor_id'];

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
                        }
                    }
                }
            }
        }

        return true;
    }

    protected function deleteAddresses($ids)
    {
        foreach ($ids as $id) {
            $this->basepackages->addressbook->removeAddress(['id' => $id]);
        }
    }

    protected function disableVendorContactAccounts($vendorId)
    {
        $contactsPackage = $this->usePackage(Contacts::class);

        $condition =
            [
                'conditions'    => 'vendor_id = :vid:',
                'bind'          =>
                    [
                        'vid'   => $vendorId
                    ]
            ];

        $contacts = $contactsPackage->getByParams($condition);

        if ($contacts && count($contacts) > 0) {
            foreach ($contacts as $contactKey => $contact) {
                if ($contact['account_id'] !== '0') {
                    $this->basepackages->accounts->removeAccount(['id' => $contact['account_id']]);
                }
            }
        }
    }

    public function addFinancialDetails(array $data)
    {
        $this->modelToUse = BusinessDirectoryVendorsFinancialDetails::class;

        unset($data['id']);

        $this->add($data);

        $this->modelToUse = BusinessDirectoryVendors::class;
    }

    public function updateFinancialDetails(array $data)
    {
        $this->modelToUse = BusinessDirectoryVendorsFinancialDetails::class;

        $financialDetailsModel = $this->modelToUse::findFirst(
            [
                'conditions'    => 'vendor_id = :vid:',
                'bind'          => [
                    'vid'       => $data['vendor_id']
                ]
            ]
        );

        if ($financialDetailsModel) {
            if (isset($data['id'])) {
                unset($data['id']);
            }

            $financialDetails = $financialDetailsModel->toArray();

            $financialDetails = array_merge($financialDetails, $data);

            $this->update($financialDetails);
        }

        $this->modelToUse = BusinessDirectoryVendors::class;
    }

    public function checkVendorDuplicate($name)
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

    protected function addBrands(array $data)
    {
        if (isset($data['brands']) && $data['brands'] !== '') {
            $brands = $this->usePackage(Brands::class);

            $data['brands'] = Json::decode($data['brands'], true);

            if (isset($data['brands']['data'])) {
                if (isset($data['brands']['newTags']) &&
                    count($data['brands']['newTags']) > 0
                ) {
                    foreach ($data['brands']['newTags'] as $brand) {
                        $newBrand = $brands->add(['name' => $brand]);
                        if ($newBrand) {
                            array_push($data['brands']['data'], $brands->packagesData->last['id']);
                        }
                    }
                }

                $data['brands'] = Json::encode($data['brands']['data']);
            } else {
                $data['brands'] = Json::encode($data['brands']);
            }
        } else {
            $data['brands'] = Json::encode([]);
        }

        return $data;
    }

    protected function addB2bAccountManagers(array $data)
    {
        if (isset($data['b2b_account_managers']) && $data['b2b_account_managers'] !== '') {
            $data['b2b_account_managers'] = Json::decode($data['b2b_account_managers'], true);

            if (isset($data['b2b_account_managers']['data'])) {
                $data['b2b_account_managers'] = Json::encode($data['b2b_account_managers']['data']);
            } else {
                $data['b2b_account_managers'] = Json::encode($data['b2b_account_managers']);
            }
        } else {
            $data['b2b_account_managers'] = Json::encode([]);
        }

        return $data;
    }

    public function getAllManufacturers()
    {
        $searchVendors =
            $this->getByParams(
                [
                    'conditions'    => 'is_manufacturer = :im:',
                    'bind'          => [
                        'im'        => '1'
                    ]
                ], true
            );

        if ($searchVendors && count($searchVendors) > 0) {
            return $this->setAllData($searchVendors);
        }

        return false;
    }

    public function getAllSuppliers()
    {
        $searchVendors =
            $this->getByParams(
                [
                    'conditions'    => 'is_supplier = :is:',
                    'bind'          => [
                        'is'        => '1'
                    ]
                ], true
            );

        if ($searchVendors && count($searchVendors) > 0) {
            return $this->setAllData($searchVendors);
        }

        return false;
    }

    public function getAllManufacturersSuppliers()
    {
        $searchVendors =
            $this->getByParams(
                [
                    'conditions'    => 'is_manufacturer = :im: OR is_supplier = :is: OR does_dropship = :dd:',
                    'bind'          => [
                        'im'        => '1',
                        'is'        => '1',
                        'dd'        => '1'
                    ]
                ], true
            );

        if ($searchVendors && count($searchVendors) > 0) {
            return $this->setAllData($searchVendors);
        }

        return false;
    }

    public function getAllServiceProviders()
    {
        $searchVendors =
            $this->getByParams(
                [
                    'conditions'    => 'is_service_provider = :isp:',
                    'bind'          => [
                        'isp'       => '1'
                    ]
                ], true
            );

        if ($searchVendors && count($searchVendors) > 0) {
            return $this->setAllData($searchVendors);
        }

        return false;
    }

    public function getAllJobworkProviders()
    {
        $searchVendors =
            $this->getByParams(
                [
                    'conditions'    => 'is_service_provider = :isp: AND does_jobwork = :djw:',
                    'bind'          => [
                        'isp'       => '1',
                        'djw'       => '1'
                    ]
                ], true
            );

        if ($searchVendors && count($searchVendors) > 0) {
            return $this->setAllData($searchVendors);
        }

        return false;
    }

    public function searchByName(string $nameQueryString)
    {
        $searchVendors =
            $this->getByParams(
                [
                    'conditions'            => 'business_name LIKE :aBusinessName:',
                    'bind'                  => [
                        'aBusinessName'     => '%' . $nameQueryString . '%'
                    ]
                ], true
            );

        if ($searchVendors && count($searchVendors) > 0) {
            return $this->setAllData($searchVendors);
        }

        return false;
    }

    public function searchSuppliersManufacturersByName(string $nameQueryString)
    {
        $searchVendors =
            $this->getByParams(
                [
                    'conditions'            => 'business_name LIKE :aBusinessName: AND is_supplier = :iss: AND is_manufacturer = :ism:',
                    'bind'                  => [
                        'aBusinessName'     => '%' . $nameQueryString . '%',
                        'iss'               => '1',
                        'ism'               => '1'
                    ]
                ]
            );

        if ($searchVendors && count($searchVendors) > 0) {
            return $this->setAllData($searchVendors);
        }

        return false;
    }

    protected function setAllData($searchVendors)
    {
        $vendors = [];

        foreach ($searchVendors as $vendorKey => $vendorValue) {
            $vendors[$vendorKey]['id'] = $vendorValue['id'];
            $vendors[$vendorKey]['name'] = $vendorValue['business_name'];
        }

        $this->packagesData->responseCode = 0;

        $this->packagesData->vendors = $vendors;

        return true;
    }

    public function searchByVendorId($id)
    {
        $vendor = $this->getVendorById($id);

        if ($vendor) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Vendor Found';

            $this->packagesData->vendor = $vendor;

            return true;
        }

        $this->addResponse('Vendor with id ' . $id . ' not found', 1);
    }

    public function addProductCount(int $id)
    {
        $vendor = $this->getById($id);

        if ($vendor['product_count'] && $vendor['product_count'] != '') {
            $vendor['product_count'] = (int) $vendor['product_count'] + 1;
        } else {
            $vendor['product_count'] = 1;
        }

        $this->update($vendor);
    }

    public function removeProductCount(int $id)
    {
        $vendor = $this->getById($id);

        if ($vendor['product_count'] && $vendor['product_count'] != '') {
            $vendor['product_count'] = (int) $vendor['product_count'] - 1;
        } else {
            $vendor['product_count'] = 0;
        }

        $this->update($vendor);
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
}