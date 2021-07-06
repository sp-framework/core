<?php

namespace Apps\Dash\Components\Business\Entities;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\ABNLookup\ABNLookup;
use Apps\Dash\Packages\Business\Entities\Entities;
use Apps\Dash\Packages\System\Api\Api;
use System\Base\BaseComponent;

class EntitiesComponent extends BaseComponent
{
    use DynamicTable;

    protected $entities;

    public function initialize()
    {
        $this->entities = $this->usePackage(Entities::class);

        $this->apiPackage = $this->usePackage(Api::class);
    }

    public function searchABNAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->postData()['abn']) {
                $abn = $this->usePackage(ABNLookup::class);

                $findDetails = $abn->lookupABN($this->postData()['abn']);

                if ($findDetails) {
                    $this->view->businessDetails = $abn->packagesData->businessDetails;
                }

                $this->view->responseCode = $abn->packagesData->responseCode;

                $this->view->responseMessage = $abn->packagesData->responseMessage;
            }
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->logoLink = '';

            $entitiesArr = $this->entities->getAll()->entities;
            $entities = [];

            foreach ($entitiesArr as $key => $value) {
                $entities[$value['id']] = $value;
            }

            if ($this->getData()['id'] != 0) {
                $entity = $entities[$this->getData()['id']];

                $address = $this->basepackages->addressbook->getById($entity['address_id']);

                unset($address['id']);
                unset($address['name']);

                $entity = array_merge($entity, $address);

                $this->view->entity = $entity;

                unset($entities[$this->getData()['id']]);

                $storages = $this->basepackages->storages;

                if ($this->view->entity['logo']) {
                    $this->view->logoLink = $storages->getPublicLink($this->view->entity['logo'], 200);
                }

                $apis = $this->apiPackage->getApiByType('xero', false);

                if (isset($entity['api_id']) &&
                    ($entity['api_id'] !== '' && $entity['api_id'] != 0)
                ) {
                    $thisEntitiesApi = [$this->apiPackage->getById($entity['api_id'])];

                    $this->view->apis = array_merge($apis, $thisEntitiesApi);
                } else {
                    $this->view->apis = $apis;
                }
            } else {
                $this->view->apis = $this->apiPackage->getApiByType('xero', false);
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->isEnabled()) {
                $this->view->geo = true;
            } else {
                $this->view->geo = false;
            }

            $storages = $this->basepackages->storages->getAppStorages();

            if ($storages && isset($storages['public'])) {
                $this->view->storages = $storages['public'];
            } else {
                $this->view->storages = [];
            }

            $this->view->entities = $entities;

            $this->view->pick('entities/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'entity_type'   => ['html'  =>
                        [
                            'IND' => 'Individual/Sole Trader',
                            'PRV' => 'Australian Private Company'
                        ]
                    ]
                ];
        } else {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'business/entities',
                    'remove'    => 'business/entities/remove'
                ]
            ];

        $this->generateDTContent(
            $this->entities,
            'business/entities/view',
            null,
            ['abn', 'business_name', 'entity_type'],
            false,
            [],
            $controlActions,
            [],
            $replaceColumns,
            'business_name'
        );

        $this->view->pick('entities/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->entities->addEntity($this->postData());

            $this->view->responseCode = $this->entities->packagesData->responseCode;

            $this->view->responseMessage = $this->entities->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->entities->updateEntity($this->postData());

            $this->view->responseCode = $this->entities->packagesData->responseCode;

            $this->view->responseMessage = $this->entities->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->entities->removeEntity($this->postData());

            $this->view->responseCode = $this->entities->packagesData->responseCode;

            $this->view->responseMessage = $this->entities->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}