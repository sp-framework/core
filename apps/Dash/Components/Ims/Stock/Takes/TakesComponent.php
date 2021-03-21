<?php

namespace Apps\Dash\Components\Ims\Stock\Takes;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Devtools\Api\Contracts\Contracts;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ContractsComponent extends BaseComponent
{
	use DynamicTable;

	protected $contractsPackage;

	protected $contract;

	public function initialize()
	{
		$this->contractsPackage = $this->usePackage(Contracts::class);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		if (isset($this->getData()['id'])) {
			if ($this->getData()['id'] != 0) {
				$contract = $this->contractsPackage->getById($this->getData()['id']);

				if (isset($this->getData()['view']) && $this->getData()['view'] == 'true') {
					$this->contractsPackage->generateClassesFromContract($this->getData()['id']);
					return false;
					$this->view->pick('contracts/viewcontract');
				} else {
					$this->view->pick('contracts/view');
				}

				if ($contract) {
					$this->view->contract = $contract;
				}
			} else {
				$this->view->contract = [];
			}
			return;
		}

		$controlActions =
			[
				// 'disableActionsForIds'  => [1],
				'includeQ'				=> true,
				'actionsToEnable'       =>
				[
					'view'      => 'devtools/api/contracts/q/view/true',
					'edit'      => 'devtools/api/contracts/q/',
					'remove'    => 'devtools/api/contracts/remove/q/'
				]
			];

		$this->generateDTContent(
			$this->contractsPackage,
			'devtools/api/contracts/view',
			null,
			['name'],
			true,
			['name'],
			$controlActions,
			null,
			null,
			'name'
		);

		$this->view->pick('contracts/list');
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
			$this->contractsPackage->addContract($this->postData());

			$this->view->responseCode = $this->contractsPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->contractsPackage->packagesData->responseMessage;

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
			$this->contractsPackage->updateContract($this->postData());

			$this->view->responseCode = $this->contractsPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->contractsPackage->packagesData->responseMessage;

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

			$this->contractsPackage->removeContract($this->postData());

			$this->view->responseCode = $this->contractsPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->contractsPackage->packagesData->responseMessage;

		} else {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Method Not Allowed';
		}
	}
}