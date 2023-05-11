<?php

namespace Apps\Core\Components\Devtools\Api\Contracts;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use Apps\Core\Packages\Devtools\Api\Contracts\Contracts;
use Phalcon\Helper\Arr;
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
		if (isset($this->getData()['file'])) {
			$extension = Arr::last(explode('.', $this->getData()['file']));

			if ($extension === 'json') {
				$this->response->setContentType('application/json');
			} else if ($extension === 'yaml') {
				$this->response->setContentType('text/yaml');
			}

			$this->response->setHeader(
				"Content-Length",
				filesize(
					base_path(
						'apps/Core/Packages/Devtools/Api/Contracts/Contracts/' . $this->getData()['file']
					)
				)
			);

			return $this->response->setContent(
				$this->localContent->read(
					'apps/Core/Packages/Devtools/Api/Contracts/Contracts/' . $this->getData()['file']
				)
			);
		}

		if (isset($this->getData()['id'])) {
			$contractPackage = $this->modules->packages->getPackageByName('Contracts');

			$contractPackage['settings'] = Json::decode($contractPackage['settings'], true);

			$apiCategories = $contractPackage['settings']['categories'];

			$apiTypes = $contractPackage['settings']['types'];

			$this->view->apiCategories = $apiCategories;

			$this->view->apiTypes = $apiTypes;

			$this->view->contract = [];

			if ($this->getData()['id'] != 0) {
				$contract = $this->contractsPackage->getById($this->getData()['id']);

				if (isset($this->getData()['view']) && $this->getData()['view'] == 'true') {
					$contract = $this->getViewLink($contract);

					$this->view->pick('contracts/viewcontract');
				} else {
					$this->view->pick('contracts/view');
				}

				if ($contract) {
					$this->view->contract = $contract;
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
					'view'      => 'devtools/api/contracts/q/view/true',
					'edit'      => 'devtools/api/contracts/q/',
					'remove'    => 'devtools/api/contracts/remove/q/'
				]
			];

		$this->generateDTContent(
			$this->contractsPackage,
			'devtools/api/contracts/view',
			null,
			['provider_name'],
			true,
			['provider_name'],
			$controlActions,
			null,
			null,
			'provider_name'
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

	protected function getViewLink($contract)
	{
		$filename = Arr::last(explode('/', $contract['filename']));

		$contract['url'] = $this->links->url('devtools/api/contracts/q/file/' . $filename);

		return $contract;
	}
}