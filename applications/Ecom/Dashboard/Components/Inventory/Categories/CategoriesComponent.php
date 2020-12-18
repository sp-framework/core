<?php

namespace Applications\Ecom\Dashboard\Components\Inventory\Categories;

use Applications\Ecom\Dashboard\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Ecom\Dashboard\Packages\Channels\Channels;
use Applications\Ecom\Dashboard\Packages\Inventory\Categories\Categories;
use System\Base\BaseComponent;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;

class CategoriesComponent extends BaseComponent
{
    use DynamicTable;

    protected $categories;

    public function initialize()
    {
        $this->categories = $this->usePackage(Categories::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $channels = $this->usePackage(Channels::class)->getAll();

        $localChannels = [];
        $remoteChannels = [];

        foreach ($channels->channels as $key => $value) {
            if ($value['type'] === 'eshop') {
                array_push($localChannels, $channels->channels[$key]);
            } else if ($value['type'] === 'ebay') {
                array_push($remoteChannels, $channels->channels[$key]);
            }
        }

        $this->view->localChannels = $localChannels;
        $this->view->remoteChannels = $remoteChannels;

        if (isset($this->getData()['id'])) {
            $categoriesArr = $this->categories->getAll()->categories;
            $categories = [];

            foreach ($categoriesArr as $key => $value) {
                $categories[$value['id']] = $value;
            }

            if ($this->getData()['id'] != 0) {
                $this->view->category = $categories[$this->getData()['id']];

                unset($categories[$this->getData()['id']]);

                $storages = $this->usePackage(Storages::class);

                $this->view->imageLink = $storages->getPublicLink($this->view->category['image'], 200);

                $this->view->categoryType = $category['type'];
            } else {

                $this->view->categoryType = $this->getData()['type'];
            }
            $this->view->categories = $categories;

            $this->view->roles = $this->roles->init()->roles;

            $this->view->pick('categories/view');

            return;
        }

        if ($this->request->isPost()) {
            $parentToName = [];

            $categories = $this->categories->getAll()->categories;

            foreach ($categories as $categoryKey => $categoryValue) {
                if ($categoryValue['parent'] != '0') {
                    $parent = $this->categories->getById($categoryValue['parent']);
                    $parentToName[$categoryValue['parent']] = $parent['name'];
                } else {
                    $parentToName[$categoryValue['parent']] = '-';
                }
            }

            $replaceColumns =
                [
                    'parent' => ['html'  => $parentToName]
                ];
        } else {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'inventory/categories',
                    'remove'    => 'inventory/categories/remove'
                ]
            ];

        $this->generateDTContent(
            $this->categories,
            'inventory/categories/view',
            null,
            ['name', 'parent', 'product_count'],
            true,
            [],
            $controlActions,
            [],
            $replaceColumns,
            'name'
        );

        $this->view->pick('categories/list');
    }

    public function getAllCategoriesAction()
    {
        $this->view->categories = $this->categories->getAll()->categories;
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

            $this->categories->addCategory($this->postData());

            $this->view->responseCode = $this->categories->packagesData->responseCode;

            $this->view->responseMessage = $this->categories->packagesData->responseMessage;

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

            $this->categories->updateCategory($this->postData());

            $this->view->responseCode = $this->categories->packagesData->responseCode;

            $this->view->responseMessage = $this->categories->packagesData->responseMessage;

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

            $this->categories->removeCategory($this->postData());

            $this->view->responseCode = $this->categories->packagesData->responseCode;

            $this->view->responseMessage = $this->categories->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}