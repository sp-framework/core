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
        parent::getAll();

        $entities = [];

        if ($this->{$this->packageName} && count($this->{$this->packageName}) > 0) {
            foreach ($this->{$this->packageName} as $entityKey => $entity) {
                if ($entity['settings'] !== '') {
                    $entity['settings'] = Json::decode($entity['settings'], true);
                }

                $entity['address'] = $this->getEntityAddress($entity['id']);

                $entities[$entity['id']] = $entity;
            }
        }

        $this->{$this->packageName} = $entities;

        return $this;
    }

    public function getEntityById($id)
    {
        $entity = $this->getById($id);

        if ($entity) {
            if ($entity['settings'] !== '') {
                $entity['settings'] = Json::decode($entity['settings'], true);
            }

            $entity['address'] = $this->getEntityAddress($entity['id']);

            return $entity;
        }

        return false;
    }

    protected function getEntityAddress($id, $obj = false)
    {
        $entityObj = $this->getFirst('id', $id);

        if ($entityObj) {
            $addressObj = $entityObj->getAddress();

            if ($addressObj) {
                if ($obj) {
                    return $addressObj;
                }

                return $addressObj->toArray();
            }
        }

        return [];
    }

    public function addEntity(array $data)
    {
        $data = $this->addAccountant($data);

        if ($this->checkEntityDuplicate($data['business_name'])) {
            $this->addResponse('Entity ' . $data['business_name'] . ' already exists.', 1);

            return;
        }

        $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);

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

            $data['package_name'] = $this->packageName;
            $data['package_row_id'] = $this->packagesData->last['id'];
            $this->basepackages->addressbook->addAddress($data);

            $this->addResponse('Added ' . $data['business_name'] . ' business entity.');
        } else {
            $this->addResponse('Error adding new business entity.', 1);
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

        $data['contact_phone'] = $this->extractNumbers($data['contact_phone']);
        $data['contact_fax'] = $this->extractNumbers($data['contact_fax']);

        if ($this->update($data)) {
            if (isset($data['api_id']) &&
                ($data['api_id'] !== '' && $data['api_id'] != 0)
            ) {
                $apiPackage = $this->usePackage(Api::class);

                if ($entity['api_id'] && $entity['api_id'] != '0' &&
                    $data['api_id'] !== $entity['api_id']
                ) {
                    $api = $apiPackage->getById($entity['api_id']);

                    $api['in_use'] = 0;

                    $api['used_by'] = '';

                    $apiPackage->update($api);
                }

                $api = $apiPackage->getById($data['api_id']);

                $api['in_use'] = 1;

                $api['used_by'] = 'Entity (' . $data['business_name'] . ')';

                $apiPackage->update($api);
            } else if (isset($data['api_id']) &&
                       ($data['api_id'] === '' || $data['api_id'] == 0) &&
                       $entity['api_id'] != '0'
            ) {
                $apiPackage = $this->usePackage(Api::class);

                $api = $apiPackage->getById($entity['api_id']);

                $api['in_use'] = 0;

                $api['used_by'] = '';

                $apiPackage->update($api);
            }

            $data['package_name'] = $this->packageName;
            $data['package_row_id'] = $data['id'];
            $this->basepackages->addressbook->mergeAndUpdate($data);

            $this->addResponse('Updated ' . $data['business_name'] . ' business entity.');
        } else {
            $this->addResponse('Error updating business entity.', 1);
        }
    }

    public function removeEntity(array $data)
    {
        $entity = $this->getById($data['id']);

        $addressObj = $this->getEntityAddress($data['id'], true);

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

            if ($addressObj) {
                $addressObj->delete();
            }

            $this->addResponse('Removed business entity.');
        } else {
            $this->addResponse('Error removing business entity.');
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

    public function searchByEntityId($id)
    {
        $entity = $this->getEntityById($id);

        if ($entity) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Entity Found';

            $this->packagesData->entity = $entity;

            return true;
        }

        $this->addResponse('Entity with id ' . $id . ' not found', 1);

        return false;
    }
}