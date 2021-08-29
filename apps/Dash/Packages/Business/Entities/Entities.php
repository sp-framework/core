<?php

namespace Apps\Dash\Packages\Business\Entities;

use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Business\Entities\Model\BusinessEntities;
use Apps\Dash\Packages\System\Api\Api;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Entities extends BasePackage
{
    protected $modelToUse = BusinessEntities::class;

    protected $packageName = 'entities';

    public $entities;

    public function getAll(bool $resetCache = false, bool $enableCache = true)
    {
        if ($enableCache && $this->config->cache->enabled) {
            $parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());
        } else {
            $parameters = [];
        }

        if (!$this->{$this->packageName} || $resetCache) {

            $this->model = $this->modelToUse::find($parameters);

            $entities = $this->model->toArray();

            foreach ($entities as $entityKey => &$entity) {
                if ($entity['settings'] !== '') {
                    $entity['settings'] = Json::decode($entity['settings'], true);
                }
            }

            $this->{$this->packageName} = $entities;
        }

        return $this;
    }

    public function addEntity(array $data)
    {
        $data = $this->addAccountant($data);

        if ($this->checkEntityDuplicate($data['business_name'])) {
            $this->addResponse('Entity ' . $data['business_name'] . ' already exists.', 1);

            return;
        }

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
        $data = $this->addAccountant($data);

        $entity = $this->getById($data['id']);

        if ($entity['business_name'] !== $data['business_name']) {
            if ($this->checkEntityDuplicate($data['business_name'])) {
                $this->addResponse('Entity ' . $data['business_name'] . ' already exists.', 1);

                return;
            }
        }

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

    protected function checkEntityDuplicate($name)
    {
        return $this->modelToUse::findFirst(
            [
                'conditions'    => 'business_name = :name:',
                'bind'          =>
                [
                    'name'      => $name
                ]
            ]
        );
    }

    protected function addAccountant(array $data)
    {
        $vendorPackage = $this->usePackage(Vendors::class);

        $data['accountant_vendor_id'] = Json::decode($data['accountant_vendor_id'], true);

        if (isset($data['accountant_vendor_id']['newTags']) &&
            count($data['accountant_vendor_id']['newTags']) > 0
        ) {
            foreach ($data['accountant_vendor_id']['newTags'] as $accountant) {
                $newAccountant = $vendorPackage->add(
                    [
                        'abn'                   => '00000000000',
                        'business_name'         => $accountant,
                        'is_service_provider'   => '1'
                    ]
                );
                if ($newAccountant) {
                    $data['accountant_vendor_id'] = $vendorPackage->packagesData->last['id'];
                } else {
                    $data['accountant_vendor_id'] = 0;
                }
            }
        } else {
            $data['accountant_vendor_id'] = $data['accountant_vendor_id']['data'][0];
        }

        return $data;
    }
}