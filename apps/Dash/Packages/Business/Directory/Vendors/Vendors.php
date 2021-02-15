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

    public function addVendor(array $data)
    {
        $data = $this->addBrands($data);

        $data = $this->updateContacts($data);

        $data = $this->updateAddresses($data);

        if ($this->add($data)) {
            $this->basepackages->storages->changeOrphanStatus($data['logo']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' vendor';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new vendor.';
        }
    }

    public function updateVendor(array $data)
    {
        $data = $this->addBrands($data);

        $data = $this->updateContacts($data);

        $data = $this->updateAddresses($data);

        if (isset($data['delete_address_ids']) && $data['delete_address_ids'] !== '') {
            $data['delete_address_ids'] = Json::decode($data['delete_address_ids'], true);
            if (count($data['delete_address_ids']) > 0) {
                $this->deleteAddresses($data['delete_address_ids']);
            }
        }

        $vendor = $this->getById($data['id']);

        if ($this->update($data)) {
            $this->basepackages->storages->changeOrphanStatus($data['logo'], $vendor['logo']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' vendor';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating vendor.';
        }
    }

    public function removeVendor(array $data)
    {
        $vendor = $this->getById($data['id']);
        //Check relations before removing.
        //Remove Address

        if ($this->remove($data['id'])) {
            $this->basepackages->storages->changeOrphanStatus(null, $vendor['logo']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed vendor';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing vendor.';
        }
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
        $vendors = $this->getAll()->vendors;

        $filter =
            $this->model->filter(
                function($vendor) {
                    $vendor = $vendor->toArray();
                    if ($vendor['is_manufacturer'] == 1) {
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
                    if ($vendor['does_jobwork'] == 1) {
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

                        $contact['contact_address_ids'] = Json::encode([]);

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

                            $address['name'] = $data['name'];
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
                    'conditions'    => 'name LIKE :aName:',
                    'bind'          => [
                        'aName'     => '%' . $nameQueryString . '%'
                    ]
                ]
            );

        if (count($searchVendors) > 0) {
            $vendors = [];

            foreach ($searchVendors as $vendorKey => $vendorValue) {
                $vendors[$vendorKey]['id'] = $vendorValue['id'];
                $vendors[$vendorKey]['name'] = $vendorValue['name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->vendors = $vendors;

            return true;
        }
    }
}