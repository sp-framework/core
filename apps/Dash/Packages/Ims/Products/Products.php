<?php

namespace Apps\Dash\Packages\Ims\Products;

use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Ims\Brands\Brands;
use Apps\Dash\Packages\Ims\Categories\Categories;
use Apps\Dash\Packages\Ims\Products\Model\ImsProducts;
use Apps\Dash\Packages\Ims\Specifications\Specifications;
use Apps\Dash\Packages\System\Tools\Barcodes\Barcodes;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Products extends BasePackage
{
    protected $modelToUse = ImsProducts::class;

    protected $packageName = 'products';

    public $products;

    protected $brandsPackage;

    protected $categoriesPackage;

    protected $manufacturersPackage;

    protected function initPackages()
    {
        $this->brandsPackage = $this->usePackage(Brands::class);

        $this->manufacturersPackage = $this->usePackage(Vendors::class);

        $this->categoriesPackage = $this->usePackage(Categories::class);

        $this->specificationsPackage = $this->usePackage(Specifications::class);
    }

    protected function processAddUpdateData(array $data)
    {
        $this->initPackages();

        $data = $this->addBrand($data);

        $data = $this->addManufacturer($data);

        $data = $this->addCategory($data);

        $data = $this->addSpecification($data);

        return $data;
    }

    protected function updateProductCount(array $data = null, array $oldData = null)
    {
        if ($data && !$oldData) {
            if ($data['brand'] !== '' && $data['brand'] != '0') {
                $this->brandsPackage->addProductCount($data['brand']);
            }

            if ($data['manufacturer'] !== '' && $data['manufacturer'] != '0') {
                $this->manufacturersPackage->addProductCount($data['manufacturer']);
            }

            if ($data['category_ids'] !== '') {
                $addProductCountArr = [];
                $categoriesIds = Json::decode($data['category_ids'], true);
                foreach ($categoriesIds as $channelKey => $channel) {
                    if (count($channel) > 0) {
                        foreach ($channel as $categoryKey => $category) {
                            if (!in_array($category, $addProductCountArr)) {
                                array_push($addProductCountArr, $category);
                            }
                        }
                    }
                }
                if (count($addProductCountArr) > 0) {
                    foreach ($addProductCountArr as $addCount) {
                        $this->categoriesPackage->addProductCount($addCount);
                    }
                }
            }
        } else if ($data && $oldData) {
            if ($data['category_ids'] !== '') {
                $addProductCountArr = [];
                $categoriesIds = Json::decode($data['category_ids'], true);
                foreach ($categoriesIds as $channelKey => $channel) {
                    if (count($channel) > 0) {
                        foreach ($channel as $categoryKey => $category) {
                            if (!in_array($category, $addProductCountArr)) {
                                array_push($addProductCountArr, $category);
                            }
                        }
                    }
                }
                if (count($addProductCountArr) > 0) {
                    foreach ($addProductCountArr as $addCount) {
                        $this->categoriesPackage->addProductCount($addCount);
                    }
                }
            }
            if ($oldData['category_ids'] !== '') {
                $removeProductCountArr = [];
                $categoriesIds = Json::decode($oldData['category_ids'], true);
                foreach ($categoriesIds as $channelKey => $channel) {
                    if (count($channel) > 0) {
                        foreach ($channel as $categoryKey => $category) {
                            if (!in_array($category, $removeProductCountArr)) {
                                array_push($removeProductCountArr, $category);
                            }
                        }
                    }
                }

                if (count($removeProductCountArr) > 0) {
                    foreach ($removeProductCountArr as $removeCount) {
                        $this->categoriesPackage->removeProductCount($removeCount);
                    }
                }
            }

            if ($data['brand'] !== '' && $data['brand'] != '0') {
                $this->brandsPackage->addProductCount($data['brand']);
            }
            if ($oldData['brand'] !== '' && $oldData['brand'] != '0') {
                $this->brandsPackage->removeProductCount($oldData['brand']);
            }

            if ($data['manufacturer'] !== '' && $data['manufacturer'] != '0') {
                $this->manufacturersPackage->addProductCount($data['manufacturer']);
            }
            if ($oldData['manufacturer'] !== '' && $oldData['manufacturer'] != '0') {
                $this->manufacturersPackage->removeProductCount($oldData['manufacturer']);
            }
        } else if (!$data && $oldData) {
            if ($oldData['category_ids'] !== '') {
                $removeProductCountArr = [];
                $categoriesIds = Json::decode($oldData['category_ids'], true);
                foreach ($categoriesIds as $channelKey => $channel) {
                    if (count($channel) > 0) {
                        foreach ($channel as $categoryKey => $category) {
                            if (!in_array($category, $removeProductCountArr)) {
                                array_push($removeProductCountArr, $category);
                            }
                        }
                    }
                }

                if (count($removeProductCountArr) > 0) {
                    foreach ($removeProductCountArr as $removeCount) {
                        $this->categoriesPackage->removeProductCount($removeCount);
                    }
                }
            }

            if ($oldData['brand'] !== '' && $oldData['brand'] != '0') {
                $this->brandsPackage->removeProductCount($oldData['brand']);
            }

            if ($oldData['manufacturer'] !== '' && $oldData['manufacturer'] != '0') {
                $this->manufacturersPackage->removeProductCount($oldData['manufacturer']);
            }
        }

        return;
    }

    public function addProduct(array $data)
    {
        if (!checkCtype($data['title'])) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Product title cannot have special characters';

            return false;

        } else {
            if ($data['code_ean'] !== '') {
                if (!$this->checkEAN($data['code_ean'])) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'UPC is incorrect!';

                    return;
                }
            }

            $data = $this->processAddUpdateData($data);

            $add = $this->add($data);

            if ($add) {
                if ($data['images'] !== '') {
                    $this->basepackages->storages->changeOrphanStatus($data['images'], null, true);
                }

                if ($data['downloadables'] !== '') {
                    $this->basepackages->storages->changeOrphanStatus($data['downloadables'], null, true);
                }

                $data = $this->packagesData->last;

                if ($data['code_ean'] === '') {
                    $data['code_ean'] = $this->generateEAN($data['id']);
                    $data['code_ean_barcode'] = $this->generateEANBarcode($data['code_ean']);
                }

                $this->update($data);

                $this->updateProductCount($data);

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Added ' . $data['title'] . ' product';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error adding new product.';
            }
        }
    }

    public function updateProduct(array $data)
    {
        if (!checkCtype($data['title'])) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Product title cannot have special characters';

            return false;

        } else {
            if ($data['code_ean'] !== '') {
                if (!$this->checkEAN($data['code_ean'])) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'UPC is incorrect!';

                    return;
                }
            }

            $data = $this->processAddUpdateData($data);

            $product = $this->getById($data['id']);

            if ($data['code_ean'] === '') {
                $data['code_ean'] = $this->generateEAN($data['id']);
                $data['code_ean_barcode'] = $this->generateEANBarcode($data['code_ean']);
            } else if (!$product['code_ean_barcode']) {
                $data['code_ean_barcode'] = $this->generateEANBarcode($data['code_ean']);
            }

            if ($this->update($data)) {
                if ($data['images'] !== '') {
                    $this->basepackages->storages->changeOrphanStatus($data['images'], $product['images'], true);
                }

                if ($data['downloadables'] !== '') {
                    $this->basepackages->storages->changeOrphanStatus($data['downloadables'], $product['downloadables'], true);
                }

                $this->updateProductCount($data, $product);

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Updated ' . $data['title'] . ' product';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error updating product.';
            }
        }
    }

    public function removeProduct(array $data)
    {
        $this->initPackages();

        $product = $this->getById($data['id']);

        if ($this->remove($product['id'])) {

            $this->updateProductCount(null, $product);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed product';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing product.';
        }
    }

    public function searchByMPN(string $mpnQueryString)
    {
        $searchProducts =
            $this->getByParams(
                [
                    'conditions'    => 'code_mpn LIKE :aMPN:',
                    'bind'          => [
                        'aMPN'     => '%' . $mpnQueryString . '%'
                    ]
                ]
            );

        if ($searchProducts) {
            $products = [];

            foreach ($searchProducts as $productKey => $productValue) {
                $products[$productKey]['id'] = $productValue['id'];
                $products[$productKey]['code_mpn'] = $productValue['code_mpn'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->products = $products;

            return true;
        }
    }

    public function searchByTitle(string $titleQueryString)
    {
        $searchProducts =
            $this->getByParams(
                [
                    'conditions'    => 'title LIKE :aTitle:',
                    'bind'          => [
                        'aTitle'     => '%' . $titleQueryString . '%'
                    ]
                ]
            );

        if ($searchProducts) {
            $products = [];

            foreach ($searchProducts as $productKey => $productValue) {
                $products[$productKey]['id'] = $productValue['id'];
                $products[$productKey]['title'] = $productValue['title'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->products = $products;

            return true;
        }
    }

    public function searchByCodeEAN(string $ean)
    {
        $searchProducts =
            $this->getByParams(
                [
                    'conditions'    => 'code_ean = :aEAN:',
                    'bind'          => [
                        'aEAN'      => $ean
                    ]
                ]
            );

        if ($searchProducts) {
            $products = [];

            foreach ($searchProducts as $productKey => $productValue) {
                $products[$productKey] = $productValue;
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->products = $products;

            return true;
        }
    }

    public function searchByCodeSKU(string $sku)
    {
        $searchProducts =
            $this->getByParams(
                [
                    'conditions'    => 'code_sku LIKE :aSKU:',
                    'bind'          => [
                        'aSKU'     => '%' . $sku . '%'
                    ]
                ]
            );

        if ($searchProducts) {
            $products = [];

            foreach ($searchProducts as $productKey => $productValue) {
                $products[$productKey]['id'] = $productValue['id'];
                $products[$productKey]['code_sku'] = $productValue['code_sku'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->products = $products;

            return true;
        }
    }

    protected function addBrand(array $data)
    {
        $data['brand'] = Json::decode($data['brand'], true);

        if (isset($data['brand']['newTags']) &&
            count($data['brand']['newTags']) > 0
        ) {
            foreach ($data['brand']['newTags'] as $brand) {
                $newBrand = $this->brandsPackage->add(['name' => $brand]);

                if ($newBrand) {
                    $data['brand'] = $this->brandsPackage->packagesData->last['id'];
                } else {
                    $data['brand'] = 0;
                }
            }
        } else {
            $data['brand'] = $data['brand']['data'][0];
        }

        return $data;
    }

    protected function addManufacturer(array $data)
    {
        $data['manufacturer'] = Json::decode($data['manufacturer'], true);

        if (isset($data['manufacturer']['newTags']) &&
            count($data['manufacturer']['newTags']) > 0
        ) {
            foreach ($data['manufacturer']['newTags'] as $manufacturer) {
                $newManufacturer = $this->manufacturersPackage->add(
                    [
                        'name'              => $manufacturer,
                        'is_manufacturer'   => '1',

                    ]
                );
                if ($newManufacturer) {
                    $data['manufacturer'] = $this->manufacturersPackage->packagesData->last['id'];
                } else {
                    $data['manufacturer'] = 0;
                }
            }
        } else {
            $data['manufacturer'] = $data['manufacturer']['data'][0];
        }

        return $data;
    }

    protected function addCategory($data)
    {
        if ($data['category_ids'] !== '') {
            $categoryIds = Json::decode($data['category_ids'], true);

            $data['category_ids'] = [];
            if (count($categoryIds) > 0) {
                foreach ($categoryIds as $channelKey => $channel) {
                    $data['category_ids'][$channelKey] = [];
                    if (count($channel) > 0) {
                        foreach ($channel as $categoryKey => $category) {
                            $categoryById = $this->categoriesPackage->getById($category['category_id']);
                            $categoryByHStr = $this->categoriesPackage->searchByHierarchyString($category['category']);

                            if (($categoryById && $categoryByHStr) &&
                                ($categoryById['id'] === $categoryByHStr['id'])
                            ) {
                                array_push($data['category_ids'][$channelKey], $categoryById['id']);
                            } else if (!$categoryById && $categoryByHStr) {
                                array_push($data['category_ids'][$channelKey], $categoryByHStr['id']);
                            } else {
                                $categoryTree = explode('/', $category['category']);
                                foreach ($categoryTree as &$value) {
                                    $value = trim($value);
                                }

                                $newCategoryId = null;

                                for ($i = 0; $i < count($categoryTree); $i++) {
                                    if (!$newCategoryId) {
                                        $conditions =
                                            [
                                                'conditions'    => 'name = :name:',
                                                'bind'          =>
                                                    [
                                                        'name'    => $categoryTree[$i]
                                                    ]
                                            ];

                                        $categoryTreeParent = $this->categoriesPackage->getByParams($conditions);

                                        if ($categoryTreeParent) {
                                            $newCategoryId = $categoryTreeParent[0]['id'];
                                        } else {
                                            $newCategoryArr =
                                            [
                                                'name'     => $categoryTree[$i]
                                            ];

                                            $this->categoriesPackage->addCategory($newCategoryArr);

                                            if ($this->categoriesPackage->packagesData->responseCode === 0) {
                                                $newCategoryId = $this->categoriesPackage->packagesData->last['id'];
                                            }
                                        }
                                    } else {
                                        $conditions =
                                            [
                                                'conditions'    => 'name = :name: AND parent_id = :parent:',
                                                'bind'          =>
                                                    [
                                                        'name'          => $categoryTree[$i],
                                                        'parent'        => $newCategoryId
                                                    ]
                                            ];
                                        $categoryTreeParent = $this->categoriesPackage->getByParams($conditions);

                                        if ($categoryTreeParent) {
                                            $newCategoryId = $categoryTreeParent[0]['id'];
                                        } else {
                                            $newCategoryArr =
                                            [
                                                'name'      => $categoryTree[$i],
                                                'parent_id' => $newCategoryId
                                            ];

                                            $this->categoriesPackage->addCategory($newCategoryArr);

                                            if ($this->categoriesPackage->packagesData->responseCode === 0) {
                                                $newCategoryId = $this->categoriesPackage->packagesData->last['id'];
                                            }
                                        }
                                    }
                                }
                                array_push($data['category_ids'][$channelKey], $newCategoryId);
                            }
                        }
                    }
                }
            }
            $data['category_ids'] = Json::encode($data['category_ids']);
        }

        return $data;
    }

    protected function addSpecification(array $data)
    {
        // $data['specifications'] = Json::decode($data['specifications'], true);

        // if (isset($data['specifications']['newTags']) &&
        //     count($data['specifications']['newTags']) > 0
        // ) {
        //     foreach ($data['specifications']['newTags'] as $specification) {
        //         $newManufacturer = $this->specificationsPackage->add(
        //             [
        //                 'name'              => $specification,
        //                 'is_specification'   => '1',

        //             ]
        //         );
        //         if ($newManufacturer) {
        //             $data['specifications'] = $this->specificationsPackage->packagesData->last['id'];
        //         } else {
        //             $data['specifications'] = 0;
        //         }
        //     }
        // } else {
        //     $data['specifications'] = $data['specifications']['data'][0];
        // }

        return $data;
    }

    protected function checkEAN($ean)
    {
        $checksumDigit = substr($ean, -1);

        $barcodes = $this->usePackage(Barcodes::class);

        if ($checksumDigit == $barcodes->generateBarcodeChecksum(rtrim($ean), 'EAN13')) {
            return true;
        } else {
            return false;
        }
    }

    protected function generateEAN($id)
    {
        //Total 12 digits for EAN + 1 digit for checksum.
        $gs1PrivateRange = '040';//040-049, UPC-A compatible - Used to issue restricted circulation numbers within a company (Source:Wikipedia)

        $ourRange = '01';//01-99 for different packages (99 max) 01 = Product package

        $code = str_pad($id, 7, "0", STR_PAD_LEFT);

        $barcodes = $this->usePackage(Barcodes::class);

        return $barcodes->getBarcodeWithChecksum($gs1PrivateRange.$ourRange.$code, 'EAN13');
    }

    protected function generateEANBarcode($ean)
    {
        $barcodes = $this->usePackage(Barcodes::class);

        $settings = $barcodes->getBarcodesSettings();

        return $barcodes->generateBarcode(
            $ean,
            'EAN13',
            $settings
        );
    }
}