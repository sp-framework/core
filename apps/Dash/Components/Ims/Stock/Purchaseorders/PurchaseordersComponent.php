<?php

namespace Apps\Dash\Components\Ims\Stock\PurchaseOrders;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Business\Entities\Entities;
use Apps\Dash\Packages\Business\Locations\Locations;
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

			if ($this->getData()['id'] != 0) {
				$purchaseOrder = $this->purchaseOrdersPackage->getById($this->getData()['id']);

				if ($this->vendors->searchByVendorId($purchaseOrder['vendor_id'])) {
					$purchaseOrder['vendor_addresses'] = $this->vendors->packagesData->vendor['address_ids']['2'];
					$purchaseOrder['vendor_contacts'] = $this->vendors->packagesData->vendor['contact_ids'];
				}

				$this->view->purchaseOrder = $purchaseOrder;
			} else {
				$this->view->purchaseOrder = [];
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

		$this->generateDTContent(
			$this->purchaseOrdersPackage,
			'ims/stock/purchaseorders/view',
			null,
			['vendor_id'],
			true,
			['vendor_id'],
			$controlActions,
			null,
			null,
			'id'
		);

		$this->view->pick('purchaseorders/list');
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

			$this->view->responseCode = $this->purchaseOrdersPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->purchaseOrdersPackage->packagesData->responseMessage;

		} else {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Method Not Allowed';
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

			$this->view->responseCode = $this->purchaseOrdersPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->purchaseOrdersPackage->packagesData->responseMessage;

		} else {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Method Not Allowed';
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

			$this->view->responseCode = $this->purchaseOrdersPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->purchaseOrdersPackage->packagesData->responseMessage;

		} else {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Method Not Allowed';
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

			$this->view->responseCode = $this->purchaseOrdersPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->purchaseOrdersPackage->packagesData->responseMessage;

		} else {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Method Not Allowed';
		}
	}
}