<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Domains as DomainsModel;

class Domains extends BasePackage
{
	protected $modelToUse = DomainsModel::class;

	public $domains;

	protected $domain = null;

	protected $applicationDefaults;

	// protected $domainSettings = null;

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
			if ($this->domain['settings']) {
				$this->domain['settings'] = Json::decode($this->domain['settings'], true);
			}
		}
		// foreach ($this->domain['settings'] as $key => $value) {
		// 	if ($key !== 'defaultApplication') {
		// 		if ($value['defaultComponent']) {
		// 			$this->domain['settings'][$key]['defaultComponent'] =
		// 				$this->modules->components->getIdComponent($value['defaultComponent']);
		// 		}
		// 		if ($value['defaultViews']) {
		// 			$this->domain['settings'][$key]['defaultViews'] =
		// 				$this->modules->views->getIdView($value['defaultViews']);
		// 		}
		// 	}
		// }
	}
	// public function getDomainDefaults()
	// {
	// 	if (!$this->domainSettings) {
	// 		$this->setDomainDefaults();

	// 		return $this->domainSettings;
	// 	} else {
	// 		return $this->domainSettings;
	// 	}
	// }

	// protected function setDomainDefaults()
	// {
	// 	$this->domainSettings['domain'] = $this->getDomain();
	// 	$this->domainSettings['application'] = $this->modules->applications->getById($this->domainSettings['domain']['default_application_id']);
	// 	$this->domainSettings['component'] = $this->modules->components->getById($this->domainSettings['domain']['default_component_id']);
	// 	$this->domainSettings['views'] = $this->modules->views->getById($this->domainSettings['domain']['default_views_id']);
	// }

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
		$applicationsArr = $this->modules->applications->applications;
		$applications = [];

		foreach ($applicationsArr as $key => $value) {
			$applications[$value['id']] = $value;
			$applications[$value['id']]['components'] =
				$this->modules->components->getComponentsForApplication($value['id']);
			$applications[$value['id']]['views'] =
				$this->modules->views->getViewsForApplication($value['id']);
		}

		$this->packagesData->applications = $applications;

		$this->packagesData->emailservices = $this->emailservices->init()->getAll()->emailservices;

		if ($did) {
			$domain = $this->getById($did);

			$domain['settings'] = Json::decode($domain['settings'], true);

			$this->packagesData->domain = $domain;

			return true;
		}
		return false;
	}
}