<?php

namespace Applications\Dash\Packages\Customers;

use Applications\Dash\Packages\Customers\Model\Customers as CustomersModel;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Customers extends BasePackage
{
    protected $modelToUse = CustomersModel::class;

    protected $packageName = 'customers';

    public $customers;

    public function addCustomer(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' customer';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new customer.';
        }
    }

    public function updateCustomer(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' customer';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating customer.';
        }
    }

    public function removeCustomer(array $data)
    {
        $customer = $this->getById($id);

        if ($customer['product_count'] && (int) $customer['product_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Customer is assigned to ' . $customer['product_count'] . ' products. Error removing customer.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed customer';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing customer.';
        }
    }

    public function addProductCount(int $id)
    {
        $customer = $this->getById($id);

        if ($customer['product_count'] && $customer['product_count'] != '') {
            $customer['product_count'] = (int) $customer['product_count'] + 1;
        } else {
            $customer['product_count'] = 1;
        }

        $this->update($customer);
    }

    public function removeProductCount(int $id)
    {
        $customer = $this->getById($id);

        if ($customer['product_count'] && $customer['product_count'] != '') {
            $customer['product_count'] = (int) $customer['product_count'] - 1;
        } else {
            $customer['product_count'] = 0;
        }

        $this->update($customer);
    }
}