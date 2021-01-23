<?php

namespace Applications\Dash\Components\Ims\Suppliers;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Dash\Packages\ABNLookup\ABNLookup;
use Applications\Dash\Packages\Ims\Brands\Brands;
use Applications\Dash\Packages\Ims\Suppliers\Suppliers;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class SuppliersComponent extends BaseComponent
{
    use DynamicTable;

    protected $suppliers;

    public function initialize()
    {
        $this->suppliers = $this->usePackage(Suppliers::class);
    }

    public function searchABNAction()
    {
        if ($this->postData()['abn']) {
            $abn = $this->usePackage(ABNLookup::class);

            $findDetails = $abn->lookupABN($this->postData()['abn']);

            if ($findDetails) {
                $this->view->supplierDetails = $abn->packagesData->businessDetails;
            }
            $this->view->responseCode = $abn->packagesData->responseCode;

            $this->view->responseMessage = $abn->packagesData->responseMessage;
        }
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->brands = $this->usePackage(Brands::class)->getAll()->brands;

            $this->view->logoLink = '';

            if ($this->getData()['id'] != 0) {

                $supplier = $this->suppliers->getById($this->getData()['id']);

                $address = $this->basepackages->addressbook->getById($supplier['address_id']);

                unset($address['id']);

                $supplier = array_merge($supplier, $address);

                $storages = $this->basepackages->storages;

                if ($supplier['logo'] && $supplier['logo'] !== '') {
                    $this->view->logoLink = $storages->getPublicLink($supplier['logo'], 200);
                }

                if ($supplier['brands']) {
                    $supplier['brands'] = Json::decode($supplier['brands'], true);
                }

                $this->view->supplierType = $supplier['type'];

                $this->view->supplier = $supplier;
            } else {
                $this->view->supplierType = $this->getData()['type'];
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

            $this->view->pick('suppliers/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'is_manufacturer'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ],
                    'does_dropship'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
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
                    'edit'      => 'ims/suppliers',
                    'remove'    => 'ims/suppliers/remove'
                ]
            ];

        $this->generateDTContent(
            $this->suppliers,
            'ims/suppliers/view',
            null,
            ['abn', 'name', 'is_manufacturer', 'does_dropship'],
            true,
            ['abn', 'name', 'is_manufacturer', 'does_dropship'],
            $controlActions,
            [],
            $replaceColumns,
            'name'
        );

        $this->view->pick('suppliers/list');
    }

    public function getAllSuppliersAction()
    {
        $this->view->suppliers = $this->suppliers->getAll()->suppliers;
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

            $this->suppliers->addSupplier($this->postData());

            $this->view->responseCode = $this->suppliers->packagesData->responseCode;

            $this->view->responseMessage = $this->suppliers->packagesData->responseMessage;

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

            $this->suppliers->updateSupplier($this->postData());

            $this->view->responseCode = $this->suppliers->packagesData->responseCode;

            $this->view->responseMessage = $this->suppliers->packagesData->responseMessage;

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

            $this->suppliers->removeSupplier($this->postData());

            $this->view->responseCode = $this->suppliers->packagesData->responseCode;

            $this->view->responseMessage = $this->suppliers->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}