<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Domains as DomainsModel;

class Domains extends BasePackage
{
	protected $modelToUse = DomainsModel::class;

	public $domains;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function getNamedDomain($name)
	{
		$filter =
			$this->model->filter(
				function($domain) use ($name) {
					if ($domain->name === $name) {
						return $domain;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate domain name found for domain ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0]->toArray();
		} else {
			return false;
		}
	}

	public function addDomain(array $data)
	{
		$add = $this->add($data);

		if ($add) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Domain Added';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error Adding Domain';
		}
	}

	public function updateDomain(array $data)
	{
		$update = $this->update($data);

		if ($update) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Domain Updated';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error Updating Domain';
		}
		//
	}

	public function removeDomain(array $data)
	{
		$remove = $this->remove($data['id']);

		if ($remove) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Domain Removed';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error Removing Domain';
		}
	}
}