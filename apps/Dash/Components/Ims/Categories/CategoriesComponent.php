<?php

namespace Apps\Dash\Components\Ims\Categories;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Channels\Channels;
use Apps\Dash\Packages\Ims\Categories\Categories;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

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
        $this->view->imageLink = '';
        $channels = $this->usePackage(Channels::class)->getAll()->channels;

        $localChannels = [];
        // $remoteChannels = [];
        if (count($channels) > 0) {
            foreach ($channels as $channelKey => $channel) {
                if ($channel['channel_type'] === 'eshop') {
                    array_push($localChannels, $channels[$channelKey]);
                // } else if ($channel['channel_type'] === 'ebay') {
                //     array_push($remoteChannels, $channels[$channelKey]);
                }
            }
        }
        $this->view->localChannels = $localChannels;
        // $this->view->remoteChannels = $remoteChannels;

        if (isset($this->getData()['id'])) {
            $categoriesArr = $this->categories->getAll()->categories;
            $categories = [];

            foreach ($categoriesArr as $key => $value) {
                $categories[$value['id']] = $value;
            }

            if ($this->getData()['id'] != 0) {
                $category = $categories[$this->getData()['id']];

                unset($categories[$this->getData()['id']]);

                $storages = $this->basepackages->storages;

                if ($category['image'] !== '') {
                    $this->view->imageLink = $storages->getPublicLink($category['image'], 200);
                }

                $category['visible_to_role_ids'] = Json::decode($category['visible_to_role_ids'], true);
                $category['visible_on_channel_ids'] = Json::decode($category['visible_on_channel_ids'], true);

                $this->view->categoryType = $category['type'];

                $this->view->category = $category;

            } else {

                $this->view->categoryType = $this->getData()['type'];

                $this->view->imageLink = '';
            }

            $storages = $this->basepackages->storages->getAppStorages();

            if ($storages && isset($storages['public'])) {
                $this->view->storages = $storages['public'];
            } else {
                $this->view->storages = [];
            }

            $this->view->categories = $categories;

            $this->view->roles = $this->basepackages->roles->init()->roles;

            $this->view->pick('categories/view');

            return;
        }

        if ($this->request->isPost()) {
            $parentToName = [];

            $categories = $this->categories->getAll()->categories;

            foreach ($categories as $categoryKey => $categoryValue) {
                if ($categoryValue['parent'] != '0') {
                    $parent = $this->categories->getById($categoryValue['parent']);
                    $parentToName[$categoryValue['parent']] = $parent['name'] . ' (' . $categoryValue['parent'] . ')';
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
                    'edit'      => 'ims/categories',
                    'remove'    => 'ims/categories/remove'
                ]
            ];

        $this->generateDTContent(
            $this->categories,
            'ims/categories/view',
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