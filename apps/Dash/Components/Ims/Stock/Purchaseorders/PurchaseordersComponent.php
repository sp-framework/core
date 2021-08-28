<?php

namespace Apps\Dash\Components\Ims\Stock\PurchaseOrders;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Business\Entities\Entities;
use Apps\Dash\Packages\Business\Finances\Taxes\Taxes;
use Apps\Dash\Packages\Business\Locations\Locations;
use Apps\Dash\Packages\Crms\Customers\Customers;
use Apps\Dash\Packages\Hrms\Employees\Employees;
use Apps\Dash\Packages\Ims\Stock\PurchaseOrders\PurchaseOrders;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class PurchaseordersComponent extends BaseComponent
{
	use DynamicTable;

	protected $purchaseOrdersPackage;

	protected $entities;

	public function initialize()
	{
		$this->purchaseOrdersPackage = $this->usePackage(PurchaseOrders::class);

		$this->entities = $this->usePackage(Entities::class);

		$this->vendors = $this->usePackage(Vendors::class);

		$this->locations = $this->usePackage(Locations::class);

		$this->taxes = $this->usePackage(Taxes::class);

		$this->notes = $this->basepackages->notes;
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		if (isset($this->getData()['id'])) {

			$this->view->entities = $this->entities->getAll()->entities;

			$this->view->orderStatuses = $this->purchaseOrdersPackage->getOrderStatuses();

			$this->view->vendors = $this->vendors->getAllSuppliers();

			$this->view->locations = $this->locations->getLocationsByInboundShipping();

			$this->view->taxes = $this->taxes->getAll()->taxes;

			if ($this->getData()['id'] != 0) {
				$purchaseOrder = $this->purchaseOrdersPackage->getPurchaseOrderById($this->getData()['id']);

				$purchaseOrder = $this->getAddress($purchaseOrder);

				if (isset($purchaseOrder['attachments']) && $purchaseOrder['attachments'] !== '') {
					$purchaseOrder['attachments'] = Json::decode($purchaseOrder['attachments'], true);

					foreach ($purchaseOrder['attachments'] as $attachmentKey => &$attachment) {
						$attachmentInfo = $this->basepackages->storages->getFileInfo($attachment);

						if ($attachmentInfo) {
							if ($attachmentInfo['links']) {
								$attachmentInfo['links'] = Json::decode($attachmentInfo['links'], true);
							}
							$attachment = $attachmentInfo;
						}
					}
				}

				$purchaseOrder['activityLogs'] = $this->purchaseOrdersPackage->getActivityLogs($this->getData()['id']);

				$purchaseOrder['notes'] = $this->notes->getNotes('vendors', $this->getData()['id']);

				if ($this->vendors->searchByVendorId($purchaseOrder['vendor_id'])) {
					$vendor = $this->vendors->packagesData->vendor;
					unset($vendor['address_ids']);
					unset($vendor['contact_ids']);
					$this->view->vendor = Json::encode($vendor);

					$purchaseOrder['vendor'] = $this->vendors->packagesData->vendor;
					$purchaseOrder['vendor_addresses'] = $this->vendors->packagesData->vendor['address_ids']['2'];
					$purchaseOrder['vendor_contacts'] = $this->vendors->packagesData->vendor['contact_ids'];
				}

				if ($purchaseOrder['entity_location_id'] !== '0') {
					$locations[] = $this->locations->getById($purchaseOrder['entity_location_id']);

					if ($locations[0]['employee_ids'] !== '') {
						$locations[0]['employee_ids'] = Json::decode($locations[0]['employee_ids'], true);

						$employeesPackage = $this->usepackage(Employees::class);

						$employees = [];

						foreach ($locations[0]['employee_ids'] as $employeeKey => $employee) {
							$employeeArr = $employeesPackage->getById($employee);

							if ($employeeArr) {
								array_push($employees, $employeeArr);
							}
						}
					}

					$this->view->locations = $locations;
				} else {
					$employees = [];
				}

				$this->view->employees = $employees;

				if ($purchaseOrder['customer_id'] !== '0') {
					$customers = $this->usePackage(Customers::class);

					if ($customers->searchByCustomerId($purchaseOrder['customer_id'])) {
						$purchaseOrder['customer'][] = $customers->packagesData->customer;
						$purchaseOrder['customer_addresses'] = $customers->packagesData->customer['address_ids']['1'];
					}
				} else {
					$purchaseOrder['customer'] = [];
				}

				$this->view->purchaseOrder = $purchaseOrder;
			} else {
				$purchaseOrder = [];
				$purchaseOrder['customer'] = [];
				$purchaseOrder['products'] = [];
				$this->view->purchaseOrder = $purchaseOrder;
				$this->view->employees = [];
				$this->view->vendor = '';
			}

			$this->view->pick('purchaseorders/view');

			$this->useStorage('private');

			return;
		}

		$controlActions =
			[
				// 'disableActionsForIds'  => [1],
				// 'includeQ'				=> true,
				'actionsToEnable'       =>
				[
					'edit'      => 'ims/stock/purchaseorders/',
					'remove'    => 'ims/stock/purchaseorders/remove/'
				]
			];

		if ($this->request->isPost()) {
			$replaceColumns =
				function ($dataArr) {
					if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
						return $this->replaceColumns($dataArr);
					}

					return $dataArr;
				};
		} else {
			$replaceColumns = null;
		}

		$this->generateDTContent(
			$this->purchaseOrdersPackage,
			'ims/stock/purchaseorders/view',
			null,
			['status', 'vendor_id', 'references', 'total_quantity', 'total_amount', 'delivery_date'],
			true,
			['status', 'vendor_id', 'references', 'total_quantity', 'total_amount', 'delivery_date'],
			$controlActions,
			['vendor_id' => 'vendor'],
			$replaceColumns,
			'id'
		);

		$this->view->pick('purchaseorders/list');
	}

	protected function replaceColumns($dataArr)
	{
		$orderStatuses = $this->purchaseOrdersPackage->getOrderStatuses();

		$vendorsArr = $this->vendors->getAllSuppliers();

		$vendors = [];

		foreach ($vendorsArr as $vendorKey => $vendor) {
			$vendors[$vendor['id']]	= $vendor;
		}

		foreach ($dataArr as $dataKey => &$data) {
			$data['vendor_id'] = $vendors[$data['vendor_id']]['business_name'];
			$data['status'] = $orderStatuses[$data['status']]['name'];
		}

		return $dataArr;
	}

	protected function getAddress($purchaseOrder)
	{
		$address = $this->basepackages->addressbook->getById($purchaseOrder['address_id']);
		unset($address['id']);
		unset($address['name']);
		unset($address['address_type']);
		unset($address['is_primary']);

		$purchaseOrder = array_merge($purchaseOrder, $address);

		return $purchaseOrder;
	}

	/**
	 * @acl(name="add")
	 */
	public function addAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->purchaseOrdersPackage->addPurchaseOrder($this->postData());

			$this->addResponse(
				$this->purchaseOrdersPackage->packagesData->responseMessage,
				$this->purchaseOrdersPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	/**
	 * @acl(name="update")
	 */
	public function updateAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->purchaseOrdersPackage->updatePurchaseOrder($this->postData());

			$this->addResponse(
				$this->purchaseOrdersPackage->packagesData->responseMessage,
				$this->purchaseOrdersPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	/**
	 * @acl(name="remove")
	 */
	public function removeAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->purchaseOrdersPackage->removePurchaseOrder($this->postData());

			$this->addResponse(
				$this->purchaseOrdersPackage->packagesData->responseMessage,
				$this->purchaseOrdersPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function syncWithXeroAction()
	{
		if ($this->request->isPost()) {
			// if (!$this->checkCSRF()) {
			// 	return;
			// }

			$poSync = new \Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\PurchaseOrders;

			$poSync->sync();die();

			$this->addResponse(
				$this->purchaseOrdersPackage->packagesData->responseMessage,
				$this->purchaseOrdersPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function calculateProductAmountsAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$calculatedProductAmounts = $this->purchaseOrdersPackage->calculateProductAmounts($this->postData());

			if ($calculatedProductAmounts) {
				$this->addResponse(
					'Ok',
					0,
					['calculatedProducts' => $this->purchaseOrdersPackage->packagesData->responseData]
				);
			} else {
				$this->addResponse('incorrect data provided', 1);
			}
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}
}