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
        $contactObj = $this->getFirst('id', $id);

        if ($contactObj) {
            $contact = $contactObj->toArray();

            $contact['address_ids'] = [];
            $contact['notes'] = [];
            $contact['activityLogs'] = [];

            if ($contactObj->getAddresses()) {
                $contactAddresses = $contactObj->getAddresses()->toArray();

                if (count($contactAddresses) > 0) {
                    foreach ($contactAddresses as $contactAddress) {
                        if (!isset($contact['address_ids'][$contactAddress['address_type']])) {
                            $contact['address_ids'][$contactAddress['address_type']] = [];
                        }

                        array_push($contact['address_ids'][$contactAddress['address_type']], $contactAddress);
                    }

                    foreach ($contact['address_ids'] as $addressTypeKey => $addressTypeAddresses) {
                        $contact['address_ids'][$addressTypeKey] =
                            msort($contact['address_ids'][$addressTypeKey], 'is_primary', SORT_REGULAR, SORT_DESC);
                    }
                }
            }

            if ($contactObj->getVendor()) {
                $contact['vendor'] = $contactObj->getVendor()->toArray();
            }

            $contact['activityLogs'] = $this->getActivityLogs($contact['id']);

            $contact['notes'] = $this->getNoteLogs($contact['id']);

            unset($contact['cc_details']);

            return $contact;
        }

        return false;
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

        $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        $data['contact_mobile'] = $this->extractNumbers($data['contact_mobile']);
        $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);

        if ($this->add($data)) {
            if (isset($data['portrait'])) {
                $this->basepackages->storages->changeOrphanStatus($data['portrait']);
            }

            if (isset($data['create_account']) && $data['create_account'] == '1') {
                $data['account_id'] = $this->addUpdateAccount($this->packagesData->last);
            }

            $data['id'] = $this->packagesData->last['id'];

            $this->updateAddresses($data);

            $this->update($data);

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

        if (isset($data['delete_address_ids']) && $data['delete_address_ids'] !== '') {
            $data['delete_address_ids'] = Json::decode($data['delete_address_ids'], true);
            if (count($data['delete_address_ids']) > 0) {
                $this->deleteAddresses($data['delete_address_ids']);
            }
        }

        $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        $data['contact_mobile'] = $this->extractNumbers($data['contact_mobile']);
        $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);

        if ($this->update($data)) {
            if (isset($data['portrait'])) {
                $this->basepackages->storages->changeOrphanStatus($data['portrait'], $contact['portrait']);
            }

            if (isset($data['create_account']) && $data['create_account'] == '1') {
                $data['account_id'] = $this->addUpdateAccount($this->packagesData->last);
            }

            $this->updateAddresses($data);

            $this->update($data);

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

        if ($this->remove($data['id'])) {
            if ($contact['portrait'] !== '') {
                $this->basepackages->storages->changeOrphanStatus(null, $contact['portrait']);
            }

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

    protected function addUpdateAccount($data)
    {
        $data['package_name'] = 'contacts';
        $data['package_row_id'] = $data['id'];

        unset($data['id']);

        $data['email'] = $data['account_email'];

        if (isset($data['account_id']) &&
            $data['account_id'] != '' &&
            $data['account_id'] != '0' &&
            $this->basepackages->accounts->getById($data['account_id'])
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
                            $address['package_row_id'] = $data['id'];

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