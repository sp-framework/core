<?php

namespace Apps\Core\Components\Devtools\Api\Enums;

use Apps\Core\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Core\Packages\Devtools\Api\Contracts\Contracts;
use Apps\Core\Packages\Devtools\Api\Enums\Enums;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class EnumsComponent extends BaseComponent
{
	use DynamicTable;

	protected $enumsPackage;

	protected $contract;

	public function initialize()
	{
		$this->enumsPackage = $this->usePackage(Enums::class);

		$this->contractsPackage = $this->usePackage(Contracts::class);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		$this->view->contracts = $this->contractsPackage->getAll()->contracts;

		if (isset($this->getData()['id'])) {
			if ($this->getData()['id'] != 0) {
				$this->view->enum =
					$this->enumsPackage->getById($this->getData()['id']);

				if (isset($this->getData()['view']) && $this->getData()['view'] == 'true') {
					$this->enumsPackage->generateEnum($this->getData()['id']);
					return false;
					$this->view->pick('enums/viewenum');
				} else {
					$this->view->pick('enums/view');
				}

			}
			return;
		}

		$controlActions =
			[
				// 'disableActionsForIds'  => [1],
				'includeQ'				=> true,
				'actionsToEnable'       =>
				[
					'view'      => 'devtools/api/enums/q/view/true',
					'edit'      => 'devtools/api/enums/q/',
					'remove'    => 'devtools/api/enums/remove/q/'
				]
			];

		$this->generateDTContent(
			$this->enumsPackage,
			'devtools/api/enums/view',
			null,
			['name'],
			true,
			['name'],
			$controlActions,
			null,
			null,
			'name'
		);

		$this->view->pick('enums/list');
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
			$this->enumsPackage->addEnum($this->postData());

			$this->view->responseCode = $this->enumsPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->enumsPackage->packagesData->responseMessage;

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
			$this->enumsPackage->updateEnum($this->postData());

			$this->view->responseCode = $this->enumsPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->enumsPackage->packagesData->responseMessage;

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

			$this->enumsPackage->removeEnum($this->postData());

			$this->view->responseCode = $this->enumsPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->enumsPackage->packagesData->responseMessage;

		} else {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Method Not Allowed';
		}
	}

	public function extractAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->enumsPackage->extractEnums($this->postData());

			$this->view->responseCode = $this->enumsPackage->packagesData->responseCode;

			$this->view->responseMessage = $this->enumsPackage->packagesData->responseMessage;

			$this->view->responseData = $this->enumsPackage->packagesData->responseData;

		} else {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Method Not Allowed';
		}
	}
}