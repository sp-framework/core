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
					if ($domain->domain === $name) {
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
}