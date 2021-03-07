<?php

namespace Apps\Dash\Packages\Ims\Products;

use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Ims\Brands\Brands;
use Apps\Dash\Packages\Ims\Categories\Categories;
use Apps\Dash\Packages\Ims\Products\Model\ImsProducts;
use Apps\Dash\Packages\System\Tools\Barcodes\Barcodes;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Products extends BasePackage
{
    protected $modelToUse = ImsProducts::class;

    protected $packageName = 'products';

    public $products;

    public function addProduct(array $data)
    {
        if ($data['code_ean'] !== '') {
            if (!$this->checkEAN($data['code_ean'])) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'UPC is incorrect!';

                return;
            }
        }

        $data = $this->addBrand($data);

        $data = $this->addManufacturer($data);

        $data = $this->addCategory($data);

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

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['title'] . ' product';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new product.';
        }
    }

    public function updateProduct(array $data)
    {
        $data = $this->addBrand($data);

        $data = $this->addManufacturer($data);

        $data = $this->addCategory($data);

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

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['title'] . ' product';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating product.';
        }
    }

    public function removeProduct(array $data)
    {
        // $product = $this->getById($data['id']);

        // if ($product['product_count'] && (int) $product['product_count'] > 0) {
        //     $this->packagesData->responseCode = 1;

        //     $this->packagesData->responseMessage = 'Product is assigned to ' . $product['product_count'] . ' products. Error removing product.';

        //     return false;
        // }

        if ($this->remove($data['id'])) {
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
        $brands = $this->usePackage(Brands::class);

        $data['brand'] = Json::decode($data['brand'], true);

        if (isset($data['brand']['newTags']) &&
            count($data['brand']['newTags']) > 0
        ) {
            foreach ($data['brand']['newTags'] as $brand) {
                $newBrand = $brands->add(['name' => $brand]);

                if ($newBrand) {
                    $data['brand'] = $brands->packagesData->last['id'];
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
        $manufacturers = $this->usePackage(Vendors::class);

        $data['manufacturer'] = Json::decode($data['manufacturer'], true);

        if (isset($data['manufacturer']['newTags']) &&
            count($data['manufacturer']['newTags']) > 0
        ) {
            foreach ($data['manufacturer']['newTags'] as $manufacturer) {
                $newManufacturer = $manufacturers->add(
                    [
                        'name'              => $manufacturer,
                        'is_manufacturer'   => '1',

                    ]
                );
                if ($newManufacturer) {
                    $data['manufacturer'] = $manufacturers->packagesData->last['id'];
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
        $categoryPackage = $this->usePackage(Categories::class);

        if ($data['category_ids'] !== '') {
            $data['category_ids'] = Json::decode($data['category_ids'], true);

            if (count($data['category_ids']) > 0) {
                foreach ($data['category_ids'] as $channelKey => $channel) {
                    if (count($channel) > 0) {
                        foreach ($channel as $categoryKey => $category) {
                            try {
                                $categoryArr = $categoryPackage->getById($category['category_id']);
                            } catch (\Exception $e) {
                                if ($e->getMessage() === 'Not Found') {
                                    $categoryTree = explode('/', $category['category']);

                                    foreach ($categoryTree as &$value) {
                                        $value = trim($value);
                                    }

                                    $parent = null;

                                    for ($i = 0; $i < count($categoryTree); $i++) {
                                        if (!$parent) {
                                            $conditions =
                                                [
                                                    'conditions'    => 'name = :name:',
                                                    'bind'          =>
                                                        [
                                                            'name'    => $categoryTree[$i]
                                                        ]
                                                ];

                                            $categoryTreeParent = $categoryPackage->getByParams($conditions);

                                            if ($categoryTreeParent) {
                                                $parent = $categoryTreeParent[0]['id'];
                                                continue;
                                            } else {
                                                $newCategoryArr =
                                                [
                                                    'name'     => $categoryTree[$i]
                                                ];

                                                $categoryPackage->addCategory($newCategoryArr);
                                                $parent = $categoryPackage->packagesData->last['id'];
                                            }
                                        } else {
                                            $conditions =
                                                [
                                                    'conditions'    => 'name = :name: AND parent_id = :parent:',
                                                    'bind'          =>
                                                        [
                                                            'name'      => $categoryTree[$i],
                                                            'parent'    => $parent
                                                        ]
                                                ];

                                            $categoryTreeParent = $categoryPackage->getByParams($conditions);

                                            if ($categoryTreeParent) {
                                                $parent = $categoryTreeParent[0]['id'];
                                                continue;
                                            } else {
                                                $newCategoryArr =
                                                [
                                                    'name'      => $categoryTree[$i],
                                                    'parent_id' => $parent
                                                ];

                                                $categoryPackage->addCategory($newCategoryArr);
                                                $parent = $categoryPackage->packagesData->last['id'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        die();
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