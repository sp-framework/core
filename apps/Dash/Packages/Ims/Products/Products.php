<?php

namespace Apps\Dash\Packages\Ims\Products;

use Apps\Dash\Packages\Ims\Brands\Brands;
use Apps\Dash\Packages\Ims\Products\Model\ImsProducts;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Products extends BasePackage
{
    protected $modelToUse = ImsProducts::class;

    protected $packageName = 'products';

    public $products;

    public function addProduct(array $data)
    {
        $data = $this->addBrands($data);

        if ($this->add($data)) {
            if ($data['images'] !== '') {
                $this->basepackages->storages->changeOrphanStatus($data['images'], null, true);
            }

            if ($data['downloadables'] !== '') {
                $this->basepackages->storages->changeOrphanStatus($data['downloadables'], null, true);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['title'] . ' product';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new product.';
        }
    }

    public function updateProduct(array $data)
    {
        $data = $this->addBrands($data);

        $product = $this->getById($data['id']);

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

    public function addProductCount(int $id)
    {
        $product = $this->getById($id);

        if ($product['product_count'] && $product['product_count'] != '') {
            $product['product_count'] = (int) $product['product_count'] + 1;
        } else {
            $product['product_count'] = 1;
        }

        $this->update($product);
    }

    public function removeProductCount(int $id)
    {
        $product = $this->getById($id);

        if ($product['product_count'] && $product['product_count'] != '') {
            $product['product_count'] = (int) $product['product_count'] - 1;
        } else {
            $product['product_count'] = 0;
        }

        $this->update($product);
    }

    public function searchProducts(string $productQueryString)
    {
        $searchProducts =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :bName:',
                    'bind'          => [
                        'bName'     => '%' . $productQueryString . '%'
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

    protected function addBrands(array $data)
    {
        $brands = $this->usePackage(Brands::class);

        $data['brands'] = Json::decode($data['brands'], true);

        if (isset($data['brands']['newTags']) &&
            count($data['brands']['newTags']) > 0
        ) {
            foreach ($data['brands']['newTags'] as $brand) {
                $newBrand = $brands->add(['name' => $brand]);
                if ($newBrand) {
                    array_push($data['brands']['data'], $brands->packagesData->last['id']);
                }
            }
        }

        $data['brands'] = Json::encode($data['brands']['data']);

        return $data;
    }
}