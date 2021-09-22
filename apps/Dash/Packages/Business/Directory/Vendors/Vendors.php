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
        $vendorModel = new $this->modelToUse;

        $vendorObj = $vendorModel::findFirstById($id);

        if ($vendorObj) {
            $vendor = $vendorObj->toArray();

            if ($vendor['address_ids'] && $vendor['address_ids'] !== '') {
                $vendor['address_ids'] = Json::decode($vendor['address_ids'], true);

                foreach ($vendor['address_ids'] as $addressTypeKey => $addressType) {
                    if (is_array($addressType) && count($addressType) > 0) {
                        foreach ($addressType as $addressKey => $address) {
                            $vendor['address_ids'][$addressTypeKey][$addressKey] =
                                $this->basepackages->addressbook->getById($address);
                        }
                    }
                    $vendor['address_ids'][$addressTypeKey] =
                        msort($vendor['address_ids'][$addressTypeKey], 'is_primary', SORT_REGULAR, SORT_DESC);
                }
            }

            $vendor['b2bAccountManagers'] = [];

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

            $contacts = $this->usePackage(Contacts::class);

            $vendor['contact_ids'] = $contacts->searchByVendorId($vendor['id']);

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

        $data = $this->updateAddresses($data);

        $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);

        if ($this->add($data)) {
            $this->basepackages->storages->changeOrphanStatus($data['logo']);

            $data['vendor_id'] = $this->packagesData->last['id'];

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

        $data = $this->updateAddresses($data);

        if (isset($data['delete_address_ids']) && $data['delete_address_ids'] !== '') {
            $data['delete_address_ids'] = Json::decode($data['delete_address_ids'], true);
            if (count($data['delete_address_ids']) > 0) {
                $this->deleteAddresses($data['delete_address_ids']);
            }
        }

        $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);

        if ($this->update($data)) {
            if ($data['is_b2b_customer'] == '0') {
                $this->disableVendorContactAccounts($data['id']);
            }

            $data['vendor_id'] = $data['id'];

            $this->updateFinancialDetails($data);

            $this->basepackages->storages->changeOrphanStatus($data['logo'], $vendor['logo']);

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
        //Check relations before removing.
        //Remove Address
        //Notes
        $vendorObj = $this->modelToUse::findFirstById($data['id']);

        $vendor = $vendorObj->toArray();

        if ($vendorObj->getFinancial_details()) {
            $vendorObj->getFinancial_details()->delete();
        }

        if ($this->remove($data['id'])) {

            $this->basepackages->storages->changeOrphanStatus(null, $vendor['logo']);

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

        return $data;
    }

    protected function addB2bAccountManagers(array $data)
    {
        $data['b2b_account_managers'] = Json::decode($data['b2b_account_managers'], true);

        if (isset($data['b2b_account_managers']['data'])) {
            $data['b2b_account_managers'] = Json::encode($data['b2b_account_managers']['data']);
        } else {
            $data['b2b_account_managers'] = Json::encode($data['b2b_account_managers']);
        }

        return $data;
    }

    public function getAllManufacturers()
    {
        $this->getAll()->vendors;

        $manufacturers = [];

        $filter =
            $this->model->filter(
                function($vendor) {
                    $vendor = $vendor->toArray();
                    if ($vendor['is_manufacturer'] == 1) {
                        return $vendor;
                    }
                }
            );

        foreach ($filter as $key => $value) {
            $manufacturers[$key] = $value;
        }

        return $manufacturers;
    }

    public function getAllSuppliers()
    {
        $this->getAll()->vendors;

        $suppliers = [];

        $filter =
            $this->model->filter(
                function($vendor) {
                    $vendor = $vendor->toArray();
                    if ($vendor['is_supplier'] == 1) {
                        return $vendor;
                    }
                }
            );

        foreach ($filter as $key => $value) {
            $suppliers[$key] = $value;
        }

        return $suppliers;
    }

    public function getAllManufacturersSuppliers()
    {
        $this->getAll()->vendors;

        $filter =
            $this->model->filter(
                function($vendor) {
                    $vendor = $vendor->toArray();
                    if ($vendor['is_manufacturer'] == 1 ||
                        $vendor['is_supplier'] == 1 ||
                        $vendor['does_dropship'] == 1
                    ) {
                        return $vendor;
                    }
                }
            );

        return $filter;
    }

    public function getAllServiceProviders()
    {
        $vendors = $this->getAll()->vendors;

        $filter =
            $this->model->filter(
                function($vendor) {
                    $vendor = $vendor->toArray();
                    if ($vendor['is_service_provider'] == 1) {
                        return $vendor;
                    }
                }
            );

        return $filter;
    }

    public function getAllJobworkProviders()
    {
        $vendors = $this->getAll()->vendors;

        $filter =
            $this->model->filter(
                function($vendor) {
                    $vendor = $vendor->toArray();
                    if ($vendor['is_service_provider'] == 1 &&
                        $vendor['does_jobwork'] == 1
                    ) {
                        return $vendor;
                    }
                }
            );

        return $filter;
    }

    public function updateAddresses($data)
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

    public function searchByName(string $nameQueryString)
    {
        $searchVendors =
            $this->getByParams(
                [
                    'conditions'            => 'business_name LIKE :aBusinessName:',
                    'bind'                  => [
                        'aBusinessName'     => '%' . $nameQueryString . '%'
                    ]
                ]
            );

        if ($searchVendors && count($searchVendors) > 0) {
            $vendors = [];

            foreach ($searchVendors as $vendorKey => $vendorValue) {
                $vendors[$vendorKey]['id'] = $vendorValue['id'];
                $vendors[$vendorKey]['name'] = $vendorValue['business_name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->vendors = $vendors;

            return true;
        }
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
            $vendors = [];

            foreach ($searchVendors as $vendorKey => $vendorValue) {
                $vendors[$vendorKey]['id'] = $vendorValue['id'];
                $vendors[$vendorKey]['name'] = $vendorValue['business_name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->vendors = $vendors;

            return true;
        }
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