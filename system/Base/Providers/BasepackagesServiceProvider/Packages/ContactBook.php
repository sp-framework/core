<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesContactBook;

class ContactBook extends BasePackage
{
    protected $modelToUse = BasepackagesContactBook::class;

    protected $packageName = 'contactbook';

    public $contactbook;

    public function getContactById($id)
    {
        $contact = $this->getById($id);

        if ($contact) {
            unset($contact['id']);
            unset($contact['name']);
            unset($contact['package_class']);

            return $contact;
        }

        return false;
    }

    public function addContact(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('Added contact');
        } else {
            $this->addResponse('Error adding new contact.', 1);
        }

        return $this->packagesData->last;
    }

    public function updateContact(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated contact');
        } else {
            $this->addResponse('Error updating contact.', 1);
        }
    }

    public function removeContact(array $data)
    {
        if ($this->remove($data['id'])) {
            $this->addResponse('Removed contact');
        } else {
            $this->addResponse('Error removing contact.', 1);
        }
    }

    public function getContactsByPackageClassAndPackageRowId($packageClass, $packageRowId)
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