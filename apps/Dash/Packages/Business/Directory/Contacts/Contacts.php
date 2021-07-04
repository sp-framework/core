<?php

namespace Apps\Dash\Packages\Business\Directory\Contacts;

use Apps\Dash\Packages\Business\Directory\Contacts\Model\BusinessDirectoryContacts;
use Apps\Dash\Packages\Ims\Brands\Brands;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Contacts extends BasePackage
{
    protected $modelToUse = BusinessDirectoryContacts::class;

    protected $packageName = 'contacts';

    public $contacts;

    /**
     * @notification(name=add)
     */
    public function addContact(array $data)
    {
        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];

        $data = $this->updateAddresses($data);

        if ($this->add($data)) {
            if (isset($data['portrait'])) {
                $this->basepackages->storages->changeOrphanStatus($data['portrait']);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['full_name'] . ' contact';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new contact.';
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateContact(array $data)
    {
        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];

        $data = $this->updateAddresses($data);

        if (isset($data['delete_address_ids']) && $data['delete_address_ids'] !== '') {
            $data['delete_address_ids'] = Json::decode($data['delete_address_ids'], true);
            if (count($data['delete_address_ids']) > 0) {
                $this->deleteAddresses($data['delete_address_ids']);
            }
        }

        $contact = $this->getById($data['id']);

        if ($this->update($data)) {
            if (isset($data['portrait'])) {
                $this->basepackages->storages->changeOrphanStatus($data['portrait'], $contact['portrait']);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['full_name'] . ' contact';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating contact.';
        }
    }

    /**
     * @notification(name=remove)
     */
    public function removeContact(array $data)
    {
        $contact = $this->getById($data['id']);
        //Check relations before removing.
        //Remove Address

        if ($this->remove($data['id'])) {
            if ($contact['portrait'] !== '') {
                $this->basepackages->storages->changeOrphanStatus(null, $contact['portrait']);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed contact';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing contact.';
        }
    }

    public function searchByEmail(string $nameQueryString)
    {
        $searchContacts =
            $this->getByParams(
                [
                    'conditions'            => 'account_email LIKE :accountEmail:',
                    'bind'                  => [
                        'accountEmail'      => '%' . $nameQueryString . '%'
                    ]
                ]
            );

        if (count($searchContacts) > 0) {
            $contacts = [];

            foreach ($searchContacts as $contactKey => $contactValue) {
                $contacts[$contactKey]['id'] = $contactValue['id'];
                $contacts[$contactKey]['account_email'] = $contactValue['account_email'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->contacts = $contacts;

            return true;
        }
    }

    public function searchByFullName(string $nameQueryString)
    {
        $searchContacts =
            $this->getByParams(
                [
                    'conditions'    => 'full_name LIKE :fullName:',
                    'bind'          => [
                        'fullName'     => '%' . $nameQueryString . '%'
                    ]
                ]
            );

        if (count($searchContacts) > 0) {
            $contacts = [];

            foreach ($searchContacts as $contactKey => $contactValue) {
                $contacts[$contactKey]['id'] = $contactValue['id'];
                $contacts[$contactKey]['full_name'] = $contactValue['full_name'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->contacts = $contacts;

            return true;
        }
    }

    public function searchById($id)
    {
        $contact = $this->getById($id);

        if ($contact) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->contact = $contact;

            return;
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->contact = 'No Contact Found!';
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

                            $address['name'] = $data['full_name'];
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
}