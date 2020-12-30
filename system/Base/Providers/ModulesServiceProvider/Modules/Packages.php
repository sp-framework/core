<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Packages as PackagesModel;;

class Packages extends BasePackage
{
	protected $modelToUse = PackagesModel::class;

	public $packages;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}


	public function getNamedPackageForApplication($name, $applicationId)
	{
		$filter =
			$this->model->filter(
				function($package) use ($name, $applicationId) {
					$package = $package->toArray();
					$package['applications'] = Json::decode($package['applications'], true);
					if (isset($package['applications'][$applicationId])) {
						if ($package['name'] === ucfirst($name) &&
							$package['applications'][$applicationId]['enabled'] === true
						) {
							return $package;
						}
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate package name found for package ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getNamedPackageForRepo($name, $repo)
	{
		$filter =
			$this->model->filter(
				function($package) use ($name, $repo) {
					$package = $package->toArray();

					if ($package['name'] === ucfirst($name) &&
						$package['repo'] === $repo
					) {
						return $package;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate package name found for package ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getIdPackage($id)
	{
		$filter =
			$this->model->filter(
				function($package) use ($id) {
					$package = $package->toArray();
					if ($package['id'] === $id) {
						return $package;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate package Id found for id ' . $id);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getNamePackage($name)
	{
		$filter =
			$this->model->filter(
				function($package) use ($name) {
					$package = $package->toArray();
					if ($package['name'] === $name) {
						return $package;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate package found for name ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getPackagesForCategoryAndSubcategory($category, $subCategory, $inclCommon = true)
	{
		$packages = [];

		$filter =
			$this->model->filter(
				function($package) use ($category, $subCategory, $inclCommon) {
					$package = $package->toArray();
					if ($inclCommon) {
						if (($package['category'] === $category && $package['sub_category'] === $subCategory) ||
							($package['category'] === $category && $package['sub_category'] === 'common')
						) {
							return $package;
						}
					} else {
						if ($package['category'] === $category && $package['sub_category'] === $subCategory) {
							return $package;
						}
					}
				}
			);

		foreach ($filter as $key => $value) {
			$packages[$key] = $value;
		}
		return $packages;
	}

	public function addPackage(array $data)
	{
		if ($this->add($data)) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Added ' . $data['name'] . ' package';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error adding new package.';
		}
	}

	public function updatePackage(array $data)
	{
		if ($this->update($data)) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' package';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error updating package.';
		}
	}

	public function removePackage(array $data)
	{
		if ($this->remove($data['id'])) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Removed package';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error removing package.';
		}
	}
}