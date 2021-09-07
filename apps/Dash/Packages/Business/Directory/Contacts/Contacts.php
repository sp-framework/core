<?php

namespace Apps\Dash\Packages\Business\Directory\Contacts;

use Apps\Dash\Packages\Business\Directory\Contacts\Model\BusinessDirectoryContacts;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Ims\Brands\Brands;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Contacts extends BasePackage
{
    protected $modelToUse = BusinessDirectoryContacts::class;

    protected $packageName = 'contacts';

    public $contacts;

    public function getContactById(int $id)
    {
        $contactModel = new $this->modelToUse;

        $contactObj = $contactModel::findFirstById($id);

        $contact = $contactObj->toArray();

        unset($contact['cc_details']);

        return $contact;
    }

    /**
     * @notification(name=add)
     */
    public function addContact(array $data)
    {
        if ($this->checkContactDuplicate($data['account_email'])) {
            $this->addResponse('Contact ' . $data['account_email'] . ' already exists.', 1);

            return;
        }

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

            // $this->updateVendorContacts($data);

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addResponse('Added ' . $data['full_name'] . ' contact');

            $this->addToNotification('add', 'Added new contact ' . $data['full_name']);
        } else {
            $this->addResponse('Error adding new contact.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateContact(array $data)
    {
        $contact = $this->getById($data['id']);

        if ($data['account_email'] !== $contact['account_email']) {
            if ($this->checkContactDuplicate($data['account_email'])) {
                $this->addResponse('Contact ' . $data['account_email'] . ' already exists.', 1);

                return;
            }
        }

        $data['account_id'] = $contact['account_id'];

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
                $this->basepackages->storages->changeOrphanStatus($data['portrait'], $contact['portrait']);
            }

            $data['account_id'] = $this->addUpdateAccount($data);

            $this->update($data);

            // $this->updateVendorContacts($data, $contact);

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addResponse('Updated ' . $data['full_name'] . ' contact');

            $this->addToNotification('update', 'Updated contact ' . $data['full_name']);
        } else {
            $this->addResponse('Error updating contact.', 1);
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

            $this->updateVendorContacts($data);

            $this->basepackages->accounts->removeAccount(['id' => $contact['account_id']]);

            $this->addToNotification('remove', 'Removed contact ' . $contact['full_name']);

            $this->addResponse('Removed contact');
        } else {
            $this->addResponse('Error removing contact.', 1);
        }
    }

    /**
     * @notification(name=error)
     */
    public function errorContact($messageTitle = null, $messageDetails = null, $id = null)
    {
        if (!$messageTitle) {
            $messageTitle = 'Contact has errors, contact administrator!';
        }

        $this->addToNotification('error', $messageTitle, $messageDetails, null, $id);
    }

    public function checkContactDuplicate($email)
    {
        return $this->modelToUse::findFirst(
            [
                'conditions'    => 'account_email = :email:',
                'bind'          =>
                [
                    'email'     => $email
                ]
            ]
        );
    }

    protected function updateVendorContacts($data, $contact = null)
    {
        $vendors = $this->usePackage(Vendors::class);

        if ($contact) {
            if ($contact['vendor_id'] === $data['vendor_id']) {
                return;
            } else {
                $vendorObj = $vendors->getModelToUse()::findFirstById($contact['vendor_id']);

                if ($vendorObj) {
                    $vendor = $vendorObj->toArray();

                    $vendor['contact_ids'] = Json::decode($vendor['contact_ids'], true);

                    if (in_array($contact['id'], $vendor['contact_ids'])) {
                        $key = array_keys($vendor['contact_ids'], $contact['id']);

                        if ($key) {
                            array_splice($vendor['contact_ids'], $key[0], 1);
                        }

                        $vendor['contact_ids'] = Json::encode($vendor['contact_ids']);

                        $vendorObj->assign($vendor);

                        $vendorObj->update();
                    }
                }

                $vendorObj = $vendors->getModelToUse()::findFirstById($data['vendor_id']);

                if ($vendorObj) {
                    $vendor = $vendorObj->toArray();

                    $vendor['contact_ids'] = Json::decode($vendor['contact_ids'], true);

                    if (!in_array($contact['id'], $vendor['contact_ids'])) {
                        array_push($vendor['contact_ids'], $data['id']);

                        $vendor['contact_ids'] = Json::encode($vendor['contact_ids']);

                        $vendorObj->assign($vendor);

                        $vendorObj->update();
                    }
                }
            }
        }
    }

    protected function addUpdateAccount($data)
    {
        $vendors = $this->usePackage(Vendors::class);

        $vendorArr = $vendors->getById($data['vendor_id']);

        if ($vendorArr['is_b2b_customer'] == '1') {
            $data['package_name'] = 'contacts';
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
                    $this->addResponse('Error adding/updating contact account. Please contact administrator', 1);
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
                    $this->addResponse('Error adding/updating contact account. Please contact administrator', 1);
                }
            }
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

        if ($searchContacts && count($searchContacts) > 0) {
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

        if ($searchContacts && count($searchContacts) > 0) {
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

    public function searchByVendorId(int $vendorId)
    {
        $searchContacts =
            $this->getByParams(
                [
                    'conditions'        => 'vendor_id = :vid:',
                    'bind'              => [
                        'vid'           => $vendorId
                    ]
                ]
            );

        if ($searchContacts && count($searchContacts) > 0) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->contacts = $searchContacts;

            return $searchContacts;
        }

        return [];
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