<?php

namespace Applications\Dash\Components\Ims\Brands;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Dash\Packages\Ims\Brands\Brands;
use System\Base\BaseComponent;

class BrandsComponent extends BaseComponent
{
    use DynamicTable;

    protected $brands;

    public function initialize()
    {
        $this->brands = $this->usePackage(Brands::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {

                $this->view->brand = $this->brands->getById($this->getData()['id']);

                $storages = $this->basepackages->storages;

                if ($this->view->brand['logo'] && $this->view->brand['logo'] !== '') {
                    $this->view->logoLink = $storages->getPublicLink($this->view->brand['logo'], 200);
                }
            } else {
                $this->view->logoLink = '';
            }

            $this->view->brands = [];

            $this->view->pick('brands/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'ims/brands',
                    'remove'    => 'ims/brands/remove'
                ]
            ];

        $this->generateDTContent(
            $this->brands,
            'ims/brands/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('brands/list');
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

            $this->brands->addBrand($this->postData());

            $this->view->responseCode = $this->brands->packagesData->responseCode;

            $this->view->responseMessage = $this->brands->packagesData->responseMessage;

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

            $this->brands->updateBrand($this->postData());

            $this->view->responseCode = $this->brands->packagesData->responseCode;

            $this->view->responseMessage = $this->brands->packagesData->responseMessage;

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

            $this->brands->removeBrand($this->postData());

            $this->view->responseCode = $this->brands->packagesData->responseCode;

            $this->view->responseMessage = $this->brands->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchBrandAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 2) {
                    return;
                }

                $searchBrands = $this->brands->searchBrands($searchQuery);

                if ($searchBrands) {
                    $this->view->responseCode = $this->brands->packagesData->responseCode;

                    $this->view->brands = $this->brands->packagesData->brands;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}