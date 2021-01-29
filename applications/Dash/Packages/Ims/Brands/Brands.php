<?php

namespace Applications\Dash\Packages\Ims\Brands;

use Applications\Dash\Packages\Ims\Brands\Model\ImsBrands;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Brands extends BasePackage
{
    protected $modelToUse = ImsBrands::class;

    protected $packageName = 'brands';

    public $brands;

    public function addBrand(array $data)
    {
        if ($this->add($data)) {

            $this->basepackages->storages->changeOrphanStatus($data['logo']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' brand';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new brand.';
        }
    }

    public function updateBrand(array $data)
    {
        $brand = $this->getById($data['id']);

        if ($this->update($data)) {

            $this->basepackages->storages->changeOrphanStatus($data['logo'], $brand['logo']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' brand';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating brand.';
        }
    }

    public function removeBrand(array $data)
    {
        $brand = $this->getById($data['id']);

        if ($brand['product_count'] && (int) $brand['product_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Brand is assigned to ' . $brand['product_count'] . ' products. Error removing brand.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->basepackages->storages->changeOrphanStatus(null, $brand['logo']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed brand';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing brand.';
        }
    }

    public function addProductCount(int $id)
    {
        $brand = $this->getById($id);

        if ($brand['product_count'] && $brand['product_count'] != '') {
            $brand['product_count'] = (int) $brand['product_count'] + 1;
        } else {
            $brand['product_count'] = 1;
        }

        $this->update($brand);
    }

    public function removeProductCount(int $id)
    {
        $brand = $this->getById($id);

        if ($brand['product_count'] && $brand['product_count'] != '') {
            $brand['product_count'] = (int) $brand['product_count'] - 1;
        } else {
            $brand['product_count'] = 0;
        }

        $this->update($brand);
    }

    public function searchBrands(string $brandQueryString)
    {
        $searchBrands =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :bName:',
                    'bind'          => [
                        'bName'     => '%' . $brandQueryString . '%'
                    ]
                ]
            );

        if ($searchBrands) {
            $brands = [];

            foreach ($searchBrands as $brandKey => $brandValue) {
                $brands[$brandKey] = $brandValue;
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->brands = $brands;

            return true;
        }
    }
}