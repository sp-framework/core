<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesRepositories;

class Repositories extends BasePackage
{
	protected $modelToUse = ModulesRepositories::class;

	protected $packageNameS = 'Repository';

	public $repositories;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function addRepository(array $data)
	{
		$add = $this->add($data);

		if ($add) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Repository Added';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error Adding Repository';
		}
	}

	public function updateRepository(array $data)
	{
		$update = $this->update($data);

		if ($update) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Repository Updated';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error Updating Repository';
		}
	}

	public function removeRepository(array $data)
	{
		$remove = $this->remove($data['id']);

		if ($remove) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Repository Removed';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error Removing Repository';
		}
	}
}