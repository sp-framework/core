<?php

namespace Apps\Dash\Components\Ims\Stock\PurchaseOrders;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Business\Entities\Entities;
use Apps\Dash\Packages\Business\Finances\TaxGroups\TaxGroups;
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

		$this->taxGroups = $this->usePackage(TaxGroups::class);

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

			$this->view->taxGroups = $this->taxGroups->getAll()->taxgroups;

			if ($this->getData()['id'] != 0) {
				$purchaseOrder = $this->purchaseOrdersPackage->getPurchaseOrderById($this->getData()['id']);

				$purchaseOrder = $this->getAddress($purchaseOrder);

				$purchaseOrder['activityLogs'] = $this->purchaseOrdersPackage->getActivityLogs($this->getData()['id']);

				$purchaseOrder['notes'] = $this->notes->getNotes('purchaseorders', $this->getData()['id']);

				if ($this->vendors->searchByVendorId($purchaseOrder['vendor_id'])) {
					$vendor = $this->vendors->packagesData->vendor;
					unset($vendor['address_ids']);
					unset($vendor['contact_ids']);
					$this->view->vendor = Json::encode($vendor);

					$purchaseOrder['vendor'] = $this->vendors->packagesData->vendor;
					$purchaseOrder['vendor_addresses'] = $this->vendors->packagesData->vendor['address_ids']['2'];
					$purchaseOrder['vendor_contacts'] = $this->vendors->packagesData->vendor['contact_ids'];
				}

				if ($purchaseOrder['entity_location_id'] && $purchaseOrder['entity_location_id'] !== '0') {
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

				if ($purchaseOrder['customer_id'] && $purchaseOrder['customer_id'] !== '0') {
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
			function ($dataArr) {
				if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
					return $this->replaceControlActions($dataArr, $this->view);
				}

				return $dataArr;
			};
			// [
			// 	// 'disableActionsForIds'  => [1],
			// 	// 'includeQ'				=> true,
			// 	'actionsToEnable'       =>
			// 	[
			// 		'edit'      => 'ims/stock/purchaseorders/'
			// 	]
			// ];

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

		$conditions =
			[
				'order'         => 'id desc'
			];

		$this->generateDTContent(
			$this->purchaseOrdersPackage,
			'ims/stock/purchaseorders/view',
			$conditions,
			['ref_id', 'sent', 'status', 'vendor_id', 'references', 'total_quantity', 'total_amount', 'delivery_date'],
			true,
			['ref_id', 'sent', 'status', 'vendor_id', 'references', 'total_quantity', 'total_amount', 'delivery_date'],
			$controlActions,
			['ref_id' => 'PO#', 'vendor_id' => 'vendor'],
			$replaceColumns,
			'ref_id'
		);

		$this->view->pick('purchaseorders/list');
	}

	protected function replaceControlActions($dataArr, $view)
	{
		foreach ($dataArr as $dataKey => &$data) {
			if ($data['sent'] === '<span class="badge badge-success text-uppercase">Yes</span>') {
				$data['__control'] = ['view' => $this->links->url('ims/stock/purchaseorders/q/id/' . $data['id'])];
			} else {
				$data['__control'] = ['edit' => $this->links->url('ims/stock/purchaseorders/q/id/' . $data['id'])];
			}
		}

		return $dataArr;
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
			if ($data['vendor_id'] != '0') {
				$data['vendor_id'] = $vendors[$data['vendor_id']]['business_name'];
				$data['status'] = $orderStatuses[$data['status']]['name'];
			}

			if ($data['sent'] == '1') {
				$data['sent'] = '<span class="badge badge-success text-uppercase">Yes</span>';
			} else {
				$data['sent'] = '<span class="badge badge-secondary text-uppercase">No</span>';
			}
		}

		return $dataArr;
	}

	protected function getAddress($purchaseOrder)
	{
		if ($purchaseOrder['address_id'] && $purchaseOrder['address_id'] !== '0') {
			$address = $this->basepackages->addressbook->getById($purchaseOrder['address_id']);

			if ($address) {
				unset($address['id']);
				unset($address['name']);
				unset($address['address_type']);
				unset($address['is_primary']);

				$purchaseOrder = array_merge($purchaseOrder, $address);
			}
		} else {
			$purchaseOrder = array_merge(
				$purchaseOrder,
				[
					'street_address' => '',
					'street_address_2' => '',
					'city_id' => '',
					'city_name' => '',
					'post_code' => '',
					'state_id' => '',
					'state_name' => '',
					'country_id' => '',
					'country_name' => '',
				]
			);
		}

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