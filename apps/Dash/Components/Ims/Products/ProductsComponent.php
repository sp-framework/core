<?php

namespace Apps\Dash\Components\Ims\Products;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Channels\Channels;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Ims\Brands\Brands;
use Apps\Dash\Packages\Ims\Categories\Categories;
use Apps\Dash\Packages\Ims\Products\Products;
use Apps\Dash\Packages\System\Api\Ebay\Taxonomy\EbayTaxonomy;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ProductsComponent extends BaseComponent
{
    use DynamicTable;

    protected $products;

    public function initialize()
    {
        $this->products = $this->usePackage(Products::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->brands = $this->usePackage(Brands::class)->getAll()->brands;

            $this->view->manufacturers = $this->usePackage(Vendors::class)->getAllManufacturers();

            $channelsEshopArr = $this->usePackage(Channels::class)->getChannelByType('eshop');
            $channelsPosArr = $this->usePackage(Channels::class)->getChannelByType('pos');

            $this->view->channels = array_merge($channelsEshopArr, $channelsPosArr);

            $categorySources = [];

            $this->categoriesPackage = $this->usePackage(Categories::class);

            $categorySources['categories'] =
                [
                    'id'    => 1,
                    'name'  => 'Categories',
                    'data'  =>
                    [
                        'url'   => $this->links->url('/ims/categories/searchCategory')
                    ]
                ];

            if ($this->checkPackage(EbayTaxonomy::class)) {
                $categorySources['ebay_taxonomy'] =
                    [
                        'id'    => 2,
                        'name'  => 'eBay Taxonomy',
                        'data'  =>
                        [
                            'url'   => $this->links->url('/system/api/ebay/taxonomy/searchtaxonomy')
                        ]
                    ];
            }

            $this->view->categorySources = $categorySources;

            if ($this->getData()['id'] != 0) {

                $product = $this->products->getById($this->getData()['id']);

                $storages = $this->basepackages->storages;

                $this->view->productType = $product['product_type'];

                if ($product['images']) {
                    $attachments = [];

                    $attachmentsArr = Json::decode($product['images'], true);

                    foreach ($attachmentsArr as $key => $attachment) {
                        $attachmentInfo = $this->basepackages->storages->getFileInfo($attachment);
                        if ($attachmentInfo) {
                            if ($attachmentInfo['links']) {
                                $attachmentInfo['links'] = Json::decode($attachmentInfo['links'], true);
                            }
                            $attachments[$key] = $attachmentInfo;
                        }
                    }
                    $product['images'] = $attachments;
                }

                if ($product['downloadables']) {
                    $attachments = [];

                    $attachmentsArr = Json::decode($product['downloadables'], true);

                    foreach ($attachmentsArr as $key => $attachment) {
                        $attachmentInfo = $this->basepackages->storages->getFileInfo($attachment);
                        if ($attachmentInfo) {
                            if ($attachmentInfo['links']) {
                                $attachmentInfo['links'] = Json::decode($attachmentInfo['links'], true);
                            }
                            $attachments[$key] = $attachmentInfo;
                        }
                    }
                    $product['downloadables'] = $attachments;
                }

                if ($product['category_ids']) {
                    $product['category_ids'] = Json::decode($product['category_ids'], true);

                    foreach ($product['category_ids'] as $channelKey => &$channel) {
                        if (count($channel) > 0) {
                            foreach ($channel as $categoryKey => &$category) {
                                $category = $this->categoriesPackage->getById($category);
                            }
                        }
                    }
                }

                $this->view->product = $product;

            } else {
                $this->view->productType = $this->getData()['type'];

                $this->view->product = [];
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->isEnabled()) {
                $this->view->geo = true;
            } else {
                $this->view->geo = false;
            }

            $this->useStorage('public');

            $this->view->pick('products/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    // 'is_manufacturer'   => ['html'  =>
                    //     [
                    //         '0' => 'No',
                    //         '1' => 'Yes'
                    //     ]
                    // ],
                    // 'does_dropship'   => ['html'  =>
                    //     [
                    //         '0' => 'No',
                    //         '1' => 'Yes'
                    //     ]
                    // ]
                ];
        } else {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'ims/products',
                    'remove'    => 'ims/products/remove'
                ]
            ];

        $this->generateDTContent(
            $this->products,
            'ims/products/view',
            null,
            ['code_mpn', 'title'],
            true,
            ['code_mpn', 'title'],
            $controlActions,
            ['code_mpn' => 'mpn'],
            $replaceColumns,
            'title'
        );

        $this->view->pick('products/list');
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

            $this->products->addProduct($this->postData());

            $this->view->responseCode = $this->products->packagesData->responseCode;

            $this->view->responseMessage = $this->products->packagesData->responseMessage;

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

            $this->products->updateProduct($this->postData());

            $this->view->responseCode = $this->products->packagesData->responseCode;

            $this->view->responseMessage = $this->products->packagesData->responseMessage;

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

            $this->products->removeProduct($this->postData());

            $this->view->responseCode = $this->products->packagesData->responseCode;

            $this->view->responseMessage = $this->products->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchProductMPNAction()
    {
        $this->search('mpn');
    }

    public function searchProductTitleAction()
    {
        $this->search('title');
    }

    public function searchProductEANAction()
    {
        $this->search('code_ean');
    }

    public function searchProductSKUAction()
    {
        $this->search('code_sku');
    }

    protected function search($field)
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if ($field === 'mpn') {
                    if (strlen($searchQuery) < 2) {
                        return;
                    }
                    $searchProduct = $this->products->searchByMPN($searchQuery);
                } else if ($field === 'title') {
                    if (strlen($searchQuery) < 5) {
                        return;
                    }
                    $searchProduct = $this->products->searchByTitle($searchQuery);
                } else if ($field === 'code_ean') {
                    $searchProduct = $this->products->searchByCodeEAN($searchQuery);
                } else if ($field === 'code_sku') {
                    $searchProduct = $this->products->searchByCodeSKU($searchQuery);
                }

                if ($searchProduct) {
                    $this->view->responseCode = $this->products->packagesData->responseCode;

                    $this->view->products = $this->products->packagesData->products;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}