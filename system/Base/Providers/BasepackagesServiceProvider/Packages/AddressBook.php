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
            unset($address['package_name']);

            return $address;
        }

        return false;
    }

    public function addAddress(array $data)
    {
        if (!isset($data['address_reference'])) {
            $data['address_reference'] = 'Main';
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
}