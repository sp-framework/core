<?php

namespace Applications\Ecom\Dashboard\Packages\Inventory\Brands;

use Applications\Ecom\Dashboard\Packages\Inventory\Brands\Model\Brands as BrandsModel;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Brands extends BasePackage
{
    protected $modelToUse = BrandsModel::class;

    protected $packageName = 'brands';

    public $brands;

    public function addBrand(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' brand';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new brand.';
        }
    }

    public function updateBrand(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' brand';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating brand.';
        }
    }

    public function removeBrand(array $data)
    {
        //Check relations before removing.
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed brand';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing brand.';
        }
    }
}