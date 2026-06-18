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
            unset($address['package_class']);

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

    public function getAddressesByPackageClassAndPackageRowId($packageClass, $packageRowId)
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    => 'package_class = :packageClass: AND package_row_id = :packageRowId:',
                    'bind'          =>
                        [
                            'packageClass'      => $packageClass,
                            'packageRowId'      => $packageRowId
                        ]
                ];
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['package_class', '=', $packageClass],
                        ['package_row_id', '=', (int) $packageRowId]
                    ]
                ];
        }

        return $this->getByParams($conditions);
    }
}