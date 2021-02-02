<?php

namespace System\Base\Providers\DomainsServiceProvider;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\DomainsServiceProvider\Model\Domains as DomainsModel;

class Domains extends BasePackage
{
	protected $modelToUse = DomainsModel::class;

	public $domains;

	public $domain = null;

	protected $appDefaults;

	protected $defaults = null;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		$this->getDomain();

		return $this;
	}

	public function getDomain()
	{
		if (!$this->domain) {
			$this->setDomain();

			return $this->domain;
		}
		return $this->domain;
	}

	protected function setDomain()
	{
		$this->domain = $this->getNamedDomain($this->request->getHttpHost());

		if ($this->domain) {
			if ($this->domain['apps']) {
				$this->domain['apps'] = Json::decode($this->domain['apps'], true);
			}
		}
	}

	public function addDomain(array $data)
	{
		$add = $this->add($data);

		if ($add) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Domain ' . $data['name'] . ' Added';
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

			$this->packagesData->responseMessage = 'Domain ' . $data['name'] . ' Updated';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error Updating Domain';
		}
	}

	public function removeDomain(array $data)
	{
		$remove = $this->remove($data['id']);

		if ($remove) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Domain ' . $data['name'] . ' Removed';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error Removing Domain';
		}
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

	public function generateViewData(int $did = null)
	{
		$appsArr = $this->apps->apps;
		$apps = [];

		foreach ($appsArr as $key => $value) {
			$apps[$value['id']] = $value;
			$apps[$value['id']]['views'] =
				$this->modules->views->getViewsForApp($value['id']);
		}

		$this->packagesData->apps = $apps;

		$this->packagesData->emailservices = $this->basepackages->emailservices->init()->emailservices;

		$this->packagesData->storages = $this->basepackages->storages->storages;

		if ($did) {
			$domain = $this->getById($did);

			$domain['apps'] = Json::decode($domain['apps'], true);
			if ($domain['settings']) {
				$domain['settings'] = Json::decode($domain['settings'], true);
			} else {
				$domain['settings'] = [];
			}

			$this->packagesData->domain = $domain;

			return true;
		}
		return false;
	}
}