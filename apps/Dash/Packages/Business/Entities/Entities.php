<?php

namespace Apps\Dash\Packages\Business\Entities;

use Apps\Dash\Packages\Business\Entities\Model\BusinessEntities;
use System\Base\BasePackage;

class Entities extends BasePackage
{
    protected $modelToUse = BusinessEntities::class;

    protected $packageName = 'entities';

    public $entities;

    public function addEntity(array $data)
    {
        $data['package_name'] = $this->packageName;

        $this->basepackages->addressbook->addAddress($data);

        $data['address_id'] = $this->basepackages->addressbook->packagesData->last['id'];

        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' business entity.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new business entity.';
        }
    }

    public function updateEntity(array $data)
    {
        $data['package_name'] = $this->packageName;

        $this->basepackages->addressbook->mergeAndUpdate($data);

        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' business entity.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating business entity.';
        }
    }

    public function removeEntity(array $data)
    {
        //Check relations before removing.
        //Remove Address
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed business entity.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing business entity.';
        }
    }
}