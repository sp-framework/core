<?php

namespace Apps\Dash\Packages\Business\Entities;

use Apps\Dash\Packages\Business\Entities\Model\BusinessEntities;
use Apps\Dash\Packages\System\Api\Api;
use System\Base\BasePackage;

class Entities extends BasePackage
{
    protected $modelToUse = BusinessEntities::class;

    protected $packageName = 'entities';

    public $entities;

    public function addEntity(array $data)
    {
        $data['package_name'] = $this->packageName;

        $data['name'] = $data['business_name'];

        $this->basepackages->addressbook->addAddress($data);

        $data['address_id'] = $this->basepackages->addressbook->packagesData->last['id'];

        if ($this->add($data)) {

            if (isset($data['api_id']) &&
                ($data['api_id'] !== '' && $data['api_id'] != 0)
            ) {
                $apiPackage = $this->usePackage(Api::class);

                $api = $apiPackage->getById($data['api_id']);

                $api['in_use'] = 1;

                $api['used_by'] = 'Entity (' . $data['business_name'] . ')';

                $apiPackage->update($api);
            }


            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['business_name'] . ' business entity.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new business entity.';
        }
    }

    public function updateEntity(array $data)
    {
        $data['package_name'] = $this->packageName;

        $data['name'] = $data['business_name'];

        $this->basepackages->addressbook->mergeAndUpdate($data);

        if ($this->update($data)) {

            if (isset($data['api_id']) &&
                ($data['api_id'] !== '' && $data['api_id'] != 0)
            ) {
                $apiPackage = $this->usePackage(Api::class);

                $api = $apiPackage->getById($data['api_id']);

                $api['in_use'] = 1;

                $api['used_by'] = 'Entity (' . $data['business_name'] . ')';

                $apiPackage->update($api);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['business_name'] . ' business entity.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating business entity.';
        }
    }

    public function removeEntity(array $data)
    {
        $entity = $this->getById($data['id']);

        if ($this->remove($data['id'])) {

            if (isset($entity['api_id']) &&
                ($entity['api_id'] !== '' && $entity['api_id'] != 0)
            ) {
                $apiPackage = $this->usePackage(Api::class);

                $api = $apiPackage->getById($entity['api_id']);

                $api['in_use'] = 0;

                $api['used_by'] = '';

                $apiPackage->update($api);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed business entity.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing business entity.';
        }
    }
}