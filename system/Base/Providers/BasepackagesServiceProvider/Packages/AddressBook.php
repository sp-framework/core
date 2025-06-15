<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;

class AddressBook extends BasePackage
{
    protected $modelToUse = BasepackagesAddressBook::class;

    protected $packageName = 'addressbook';

    public $addressbook;

    public function getAddressById($id)
    {
        $address = $this->getById($id);

        if ($address) {
            unset($address['id']);
            unset($address['name']);
            unset($address['address_type']);
            unset($address['is_primary']);
            unset($address['package_name']);

            return $address;
        }

        return false;
    }

    public function addAddress(array $data)
    {
        if (!isset($data['address_type'])) {//Default is shipping address
            $data['address_type'] = 1;
        }

        if (!isset($data['is_primary'])) {//Default is primary address
            $data['is_primary'] = 1;
        }

        if ($this->add($data)) {
            $this->addResponse('Added address');
        } else {
            $this->addResponse('Error adding new address.', 1);
        }

        return $this->packagesData->last;
    }

    public function updateAddress(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated address');
        } else {
            $this->addResponse('Error updating address.', 1);
        }
    }

    public function removeAddress(array $data)
    {
        if ($this->remove($data['id'])) {
            $this->addResponse('Removed address');
        } else {
            $this->addResponse('Error removing address.', 1);
        }
    }

    public function mergeAndUpdate(array $data)
    {
        $address = $this->getById($data['contact_address_id']);

        unset($data['id']);

        $address = array_merge($address, $data);

        $this->updateAddress($address);

        return true;
    }

    public function getAddressesTypes()
    {
        return
            [
                [
                    'id'              => '1',
                    'name'            => 'Shipping Address',
                    'status'          => '1',
                    'address_type'    => '1',
                    'description'     => 'Used for shipping packages.'
                ],
                [
                    'id'              => '2',
                    'name'            => 'Mailing Address',
                    'status'          => '1',
                    'address_type'    => '1',
                    'description'     => 'Used for mailing letters, invoices and bills, can be PO box.'
                ]
            ];
    }
}