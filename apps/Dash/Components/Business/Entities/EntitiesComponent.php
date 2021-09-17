<?php

namespace Apps\Dash\Components\Business\Entities;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\ABNLookup\ABNLookup;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
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
            $this->view->packages = $this->getPackages();

            $this->view->accountants = $this->usePackage(Vendors::class)->getAll()->vendors;

            $this->view->logoLink = '';

            $entitiesArr = $this->entities->getAll()->entities;
            $entities = [];

            foreach ($entitiesArr as $key => $value) {
                $entities[$value['id']] = $value;
            }

            if ($this->getData()['id'] != 0) {
                if (!isset($entities[$this->getData()['id']])) {
                    return $this->throwIdNotFound();
                }

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

                $apiArr = $this->apiPackage->getApiByType('xero', false);

                $apis = [];

                foreach ($apiArr as $api) {
                    $apis[$api['id']] = $api;
                }

                if (isset($entity['api_id']) &&
                    ($entity['api_id'] !== '' && $entity['api_id'] != 0)
                ) {
                    $thisEntitiesApi[$entity['api_id']] = $this->apiPackage->getById($entity['api_id']);

                    $this->view->apis = array_replace($apis, $thisEntitiesApi);
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

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->currencyEnabled()) {
                $this->view->currency = true;
            } else {
                $this->view->currency = false;
            }

            $this->view->currencies = $this->basepackages->geoCountries->currencyEnabled(true);

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

    protected function getPackages()
    {
        $prefixPackages =
            [
                'Bills'                     =>
                    [
                        'prefix'            => 'BIL-',
                        'next_seq_number'   => '1'
                    ],
                'ExpensesVouchers'          =>
                    [
                        'prefix'            => 'EEV-',
                        'next_seq_number'   => '1'
                    ],
                'Invoices'                  =>
                    [
                        'prefix'            => 'INV-',
                        'next_seq_number'   => '1'
                    ],
                'CreditNotes'               =>
                    [
                        'prefix'            => 'CRN-',
                        'next_seq_number'   => '1'
                    ],
                'JobWorks'                  =>
                    [
                        'prefix'            => 'JOW-',
                        'next_seq_number'   => '1'
                    ],
                'SalesOrders'               =>
                    [
                        'prefix'            => 'SAO-',
                        'next_seq_number'   => '1'
                    ],
                'PurchaseOrders'            =>
                    [
                        'prefix'            => 'PUO-',
                        'next_seq_number'   => '1'
                    ],
                'Quotes'                    =>
                    [
                        'prefix'            => 'QUO-',
                        'next_seq_number'   => '1'
                    ],
                'ShippingOrders'            =>
                    [
                        'prefix'            => 'SHO-',
                        'next_seq_number'   => '1'
                    ],
                'Employees'                 =>
                    [
                        'prefix'            => 'EMP-',
                        'next_seq_number'   => '1'
                    ]
            ];

        $packages = [];

        foreach ($this->modules->packages->packages as $packageKey => $package) {
            if (isset($prefixPackages[$package['name']])) {
                $package = array_merge($package, $prefixPackages[$package['name']]);
                array_push($packages, $package);
            }
        }

        return $packages;
    }

    public function searchEntityIdAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->postData()['entityId']) {

                $entityId = $this->postData()['entityId'];

                $searchEntity = $this->entities->searchByEntityId($entityId);

                if ($searchEntity) {
                    $this->view->entity = $this->entities->packagesData->entity;
                }

                $this->addResponse(
                    $this->entities->packagesData->responseMessage,
                    $this->entities->packagesData->responseCode
                );
            } else {
                $this->addResponse('vendor id missing', 1);
            }
        }
    }
}