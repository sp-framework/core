<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors;

use Apps\Dash\Packages\Business\Directory\Contacts\Contacts;
use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendors;
use Apps\Dash\Packages\Ims\Brands\Brands;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Vendors extends BasePackage
{
    protected $modelToUse = BusinessDirectoryVendors::class;

    protected $packageName = 'vendors';

    public $vendors;

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

        $data = $this->updateContacts($data);

        $data = $this->updateAddresses($data);

        if ($this->add($data)) {
            $this->basepackages->storages->changeOrphanStatus($data['logo']);

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

        $data = $this->updateContacts($data);

        $data = $this->updateAddresses($data);

        if (isset($data['delete_address_ids']) && $data['delete_address_ids'] !== '') {
            $data['delete_address_ids'] = Json::decode($data['delete_address_ids'], true);
            if (count($data['delete_address_ids']) > 0) {
                $this->deleteAddresses($data['delete_address_ids']);
            }
        }

        if ($this->update($data)) {
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
        $vendor = $this->getById($data['id']);
        //Check relations before removing.
        //Remove Address

        if ($this->remove($data['id'])) {
            $this->basepackages->storages->changeOrphanStatus(null, $vendor['logo']);

            $this->addResponse('Removed vendor ' . $vendor['business_name']);

            $this->addToNotification('remove', 'Removed vendor ' . $vendor['business_name']);
        } else {
            $this->addResponse('Error removing vendor.', 1);
        }
    }

    protected function checkVendorDuplicate($name)
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

    protected function updateContacts($data)
    {
        if ($data['contact_ids'] !== '') {
            $data['contact_ids'] = Json::decode($data['contact_ids'], true);

            $contactsIds = [];
            if (count($data['contact_ids']) > 0) {
                $contacts = $this->usePackage(Contacts::class);

                $data['contact_ids'] = msort($data['contact_ids'], 'seq');

                foreach ($data['contact_ids'] as $contactKey => $contact) {
                    if ($contact['new'] == 1) {

                        unset($contact['id']);
                        unset($contact['new']);
                        unset($contact['seq']);

                        $contact['address_ids'] = Json::encode([]);

                        $contacts->addContact($contact);
                    } else {
                        $existingContact = $contacts->getById($contact['id']);

                        unset($contact['id']);
                        unset($contact['new']);
                        unset($contact['seq']);

                        $contact = array_merge($existingContact, $contact);

                        $contacts->updateContact($contact);
                    }

                    array_push($contactsIds, $contacts->packagesData->last['id']);
                }
            }
        }

        $data['contact_ids'] = Json::encode($contactsIds);

        return $data;
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

        if (count($searchVendors) > 0) {
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
        $vendor = $this->getById($id);

        if ($vendor) {
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

            if ($vendor['contact_ids'] && $vendor['contact_ids'] !== '') {
                $vendor['contact_ids'] = Json::decode($vendor['contact_ids'], true);

                $contacts = $this->usePackage(Contacts::class);

                foreach ($vendor['contact_ids'] as $contactKey => $contact) {
                    $contactArr = $contacts->getById($contact);

                    $vendor['contact_ids'][$contactKey] = $contactArr;
                }
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->vendor = $vendor;

            return true;
        }
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
}