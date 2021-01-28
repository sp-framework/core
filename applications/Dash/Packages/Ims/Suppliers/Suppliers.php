<?php

namespace Applications\Dash\Packages\Ims\Suppliers;

use Applications\Dash\Packages\Ims\Brands\Brands;
use Applications\Dash\Packages\Ims\Suppliers\Model\ImsSuppliers;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Suppliers extends BasePackage
{
    protected $modelToUse = ImsSuppliers::class;

    protected $packageName = 'suppliers';

    public $suppliers;

    public function addSupplier(array $data)
    {
        $data = $this->addBrands($data);

        $data['package_name'] = $this->packageName;

        $this->basepackages->addressbook->addAddress($data);

        $data['address_id'] = $this->basepackages->addressbook->packagesData->last['id'];

        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' supplier';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new supplier.';
        }
    }

    public function updateSupplier(array $data)
    {
        $data = $this->addBrands($data);

        $data['package_name'] = $this->packageName;

        $this->basepackages->addressbook->mergeAndUpdate($data);

        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' supplier';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating supplier.';
        }
    }

    public function removeSupplier(array $data)
    {
        //Check relations before removing.
        //Remove Address
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed supplier';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing supplier.';
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

    public function getAllManufacturers()
    {
        $suppliers = $this->getAll()->suppliers;

        $filter =
            $this->model->filter(
                function($supplier) {
                    $supplier = $supplier->toArray();
                    if ($supplier['is_manufacturer'] == 1) {
                        return $supplier;
                    }
                }
            );

        return $filter;
    }
}