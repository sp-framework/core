<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders;

use Apps\Dash\Packages\Business\Finances\Taxes\Taxes;
use Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model\ImsStockPurchaseOrders;
use Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model\ImsStockPurchaseOrdersProducts;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class PurchaseOrders extends BasePackage
{
    protected $modelToUse = ImsStockPurchaseOrders::class;

    protected $packageName = 'purchaseorders';

    public $purchaseorders;

    public function getPurchaseOrderById(int $id)
    {
        $purchaseOrderModel = new $this->modelToUse;

        $purchaseOrderObj = $purchaseOrderModel::findFirstById($id);

        $purchaseOrder = $purchaseOrderObj->toArray();

        $productsObj = $purchaseOrderObj->getProducts();

        if ($productsObj) {
            $purchaseOrder['products'] = $productsObj->toArray();
        }

        $this->packagesData->purchaseOrder = $purchaseOrder;

        return $purchaseOrder;
    }

    public function addPurchaseOrder(array $data)
    {
        $data = $this->updateAddress($data);

        $data = $this->getTotals($data);

        if ($this->add($data)) {
            $data['id'] = $this->packagesData->last['id'];

            $data = $this->addRefId($data);

            if ($data['attachments'] !== '') {
                $this->basepackages->storages->changeOrphanStatus($data['attachments'], null, true);
            }

            if ($data['products'] !== '') {
                $data['products'] = Json::decode($data['products'], true);

                foreach ($data['products'] as $productKey => $product) {
                    $product['purchase_order_id'] = $data['id'];

                    $this->addPurchaseOrderProducts($product);
                }
            }

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addActivityLog($data);

            $this->addResponse('Added purchase order.');

            $this->addToNotification('add', 'Added new purchase order ' . $data['id']);
        } else {
            $this->addResponse('Error adding purchase order.', 1);
        }
    }

    public function updatePurchaseOrder(array $data)
    {
        $purchaseOrder = $this->getById($data['id']);

        $data = $this->updateAddress($data, $purchaseOrder);

        $data = $this->getTotals($data);

        if ($this->update($data)) {
            $data = $this->addRefId($data);

            if (isset($data['attachments']) && $data['attachments'] !== '') {
                $this->basepackages->storages->changeOrphanStatus($data['attachments'], $purchaseOrder['attachments'], true);
            }

            if ($data['products'] !== '') {
                $data['products'] = Json::decode($data['products'], true);

                foreach ($data['products'] as $productKey => $product) {
                    $product['purchase_order_id'] = $data['id'];

                    $this->addPurchaseOrderProducts($product);
                }
            }

            if ($data['products_remove_mpns'] !== '') {
                $data['products_remove_mpns'] = Json::decode($data['products_remove_mpns'], true);

                foreach ($data['products_remove_mpns'] as $mpnKey => $mpn) {
                    $this->removePurchaseOrderProducts($mpn);
                }
            }

            $this->basepackages->notes->addNote($this->packageName, $data);

            $this->addActivityLog($data, $purchaseOrder);

            $this->addToNotification('update', 'Updated purchase order ' . $data['id']);

            $this->addResponse('Updated purchase order.');
        } else {
            $this->addResponse('Error updating purchase order.', 1);
        }
    }

    public function removePurchaseOrder(array $data)
    {
        $purchaseOrderObj = $this->modelToUse::findFirstById($data['id']);

        if ($purchaseOrderObj) {
            $purchaseOrderObj->getProducts()->delete();

            $purchaseOrderObj->delete();

            $this->addResponse('Removed purchase order.');
        } else {
            $this->addResponse('Error removing purchase order.', 1);
        }
    }

    protected function updateAddress(array $data, $purchaseOrder = null)
    {
        if ($data['delivery_type'] == '1') {
            $data['address_id'] = $data['location_address_id'];
        } else if ($data['delivery_type'] == '2') {
            $data['address_id'] = $data['customer_address_id'];
        } else if ($data['delivery_type'] == '3') {
            if (!$purchaseOrder['one_off_address_id'] || $purchaseOrder['one_off_address_id'] == '0') {
                $address = $data;
                $address['name'] = $data['contact_fullname'];
                $address['package_name'] = $this->packageName;
                $address['id'] = '';

                $this->basepackages->addressbook->addAddress($address);

                $data['address_id'] = $this->basepackages->addressbook->packagesData->last['id'];
                $data['one_off_address_id'] = $this->basepackages->addressbook->packagesData->last['id'];
            } else {
                $address = $data;
                $address['name'] = $data['contact_fullname'];
                $address['package_name'] = $this->packageName;
                $address['id'] = $purchaseOrder['one_off_address_id'];

                $this->basepackages->addressbook->updateAddress($address);

                $data['address_id'] = $purchaseOrder['one_off_address_id'];
            }
        }

        return $data;
    }

    protected function getTotals(array $data)
    {
        if (isset($data['products_counters'])) {
            $data['products_counters'] = Json::decode($data['products_counters'], true);

            $data['total_quantity'] = $data['products_counters']['tq'];
            $data['total_tax'] = $data['products_counters']['tt'];
            $data['total_discount'] = $data['products_counters']['td'];
            $data['total_amount'] = $data['products_counters']['ta'];
        }

        return $data;
    }

    protected function addPurchaseOrderProducts(array $data)
    {
        $this->modelToUse = ImsStockPurchaseOrdersProducts::class;

        if (isset($data['id'])) {
            $this->update($data);
        } else {
            $this->add($data);
        }

        $this->modelToUse = ImsStockPurchaseOrders::class;
    }

    protected function removePurchaseOrderProducts($mpn)
    {
        $this->modelToUse = ImsStockPurchaseOrdersProducts::class;

        $product = $this->modelToUse::findFirst(
            [
                'conditions'    => 'mpn = :mpn:',
                'bind'          => [
                    'mpn'       => $mpn,
                ]
            ]
        );

        if ($product) {
            $product->delete();
        }

        $this->modelToUse = ImsStockPurchaseOrders::class;
    }

    public function getOrderStatuses()
    {
        return
            [
                '1'     => [
                    'id'    => '1',
                    'name'  => 'DRAFT',
                ],
                '2'     => [
                    'id'    => '2',
                    'name'  => 'SUBMITTED',
                ],
                '3'     => [
                    'id'    => '3',
                    'name'  => 'AUTHORISED',
                ],
                '4'     => [
                    'id'    => '4',
                    'name'  => 'BILLED',
                ],
                '5'     => [
                    'id'    => '5',
                    'name'  => 'DELETED'
                ]
            ];
    }

    public function calculateProductAmounts($data)
    {
        if (isset($data['products']) && count($data['products']) > 0) {
            $total_qty = 0;
            $total_tax = 0.00;
            $total_discount = 0.00;
            $total_amount = 0.00;

            foreach ($data['products'] as $productKey => &$product) {
                $product = $this->calculateProductAmount($product);

                $total_qty = $total_qty + (float) $product['product_qty'];
                $total_tax = $total_tax + $product['taxValue'];
                if (isset($product['discountedValue'])) {
                    $total_discount = $total_discount + $product['discountedValue'];
                    $total_amount = $total_amount + $product['discountedAmountInclTax'];
                } else {
                    $total_amount = $total_amount + $product['amountInclTax'];
                }
            }

            $data['products']['po_totals'] = [];
            $data['products']['po_totals']['total_qty'] = round($total_qty, 2);
            $data['products']['po_totals']['total_tax'] = round($total_tax, 2);
            $data['products']['po_totals']['total_discount'] = round($total_discount, 2);
            $data['products']['po_totals']['total_amount'] = round($total_amount, 2);
        }

        $this->addResponse('Ok', 0, $data['products']);

        return true;
    }

    public function calculateProductAmount(&$product)
    {
        if (isset($product['product_tax_rate']) && $product['product_tax_rate'] !== '') {
            $taxPackage = $this->usePackage(Taxes::class);

            $tax = $taxPackage->getById($product['product_tax_rate']);

            if ($tax) {
                $taxAmount = (float) $tax['amount'];
                $taxName = $tax['name'];
            } else {
                $this->addResponse('Tax rate not found', 1);

                return;
            }
        } else {
            $this->addResponse('Tax rate needed.', 1);

            return;
        }

        $unitPrice = (float) $product['product_unit_price'];

        $qty = (float) $product['product_qty'];

        $amountCalc = round($unitPrice * $qty, 2);

        if ($product['product_unit_price_incl_tax'] == 'true') {
            $amountInclTax = $amountCalc;

            $amountExclTax = round($amountInclTax / (1 + ($taxAmount / 100)), 2);

            $taxValue = round($amountInclTax - $amountExclTax, 2);
        } else {
            $amountExclTax = $amountCalc;

            $taxValue = round(($amountCalc * $taxAmount) / 100, 2);

            $amountInclTax = round($taxValue + $amountCalc, 2);
        }

        $product['taxName'] = $taxName;
        $product['taxAmount'] = $taxAmount;
        $product['taxValue'] = $taxValue;
        $product['amountExclTax'] = $amountExclTax;
        $product['amountInclTax'] = $amountInclTax;

        if (isset($product['product_discount']) && $product['product_discount'] !== '') {
            $product['discountedTaxValue'] = round($taxValue * ((100 - $product['product_discount']) / 100), 2);
            $product['discountedAmountExclTax'] = round($amountExclTax * ((100 - $product['product_discount']) / 100), 2);
            $product['discountedAmountInclTax'] = round($amountInclTax * ((100 - $product['product_discount']) / 100), 2);
            $product['discountedValue'] = round($amountInclTax - $product['discountedAmountInclTax'], 2);
        }

        return $product;
    }
}