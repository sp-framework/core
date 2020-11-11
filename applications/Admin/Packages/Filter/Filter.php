<?php

namespace Applications\Admin\Packages\Filter;

use Applications\Admin\Packages\Filters\Filters as FiltersPackage;
use Applications\Admin\Packages\Filters\Model\Filters;
use System\Base\BasePackage;

class Filter extends BasePackage
{
	protected $modelToUse = Filters::class;

	protected $packageName = 'filter';

	protected $defaultFilter;

	public function addFilter(array $data)
	{
		if ($this->checkDefaultFilter($data)) {
			$add = $this->add($data);

			if ($add) {
				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Filter Added';

				$this->packagesData->filters =
					$filtersArr = $this->usePackage(FiltersPackage::class)->getFiltersForComponent($data['component_id']);

				return true;
			}
		}

		$this->packagesData->responseCode = 1;

		$this->packagesData->responseMessage = 'Cannot add filter.';

		return false;

	}

	public function updateFilter(array $data)
	{
		if ($this->checkDefaultFilter($data)) {
			$update = $this->update($data);

			if ($update) {
				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Filter Updated';

				$this->packagesData->filters =
					$filtersArr = $this->usePackage(FiltersPackage::class)->getFiltersForComponent($data['component_id']);

				return true;
			}
		}

		$this->packagesData->responseCode = 1;

		$this->packagesData->responseMessage = 'Cannot update filter.';

		return false;
	}

	public function removeFilter(array $data)
	{
		$remove = $this->remove($data['id']);

		if ($remove) {
				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Filter Removed';

				$this->packagesData->filters =
					$filtersArr = $this->usePackage(FiltersPackage::class)->getFiltersForComponent($data['component_id']);

				return true;
		}

		return false;
	}

	public function cloneFilter(array $data)
	{
		$filter = $this->getById($data['id']);
		$filter['permission'] = 1;
		$filter['is_default'] = 0;

		$clone = $this->clone($data['id'], 'name', $filter);

		if ($clone) {
				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Filter cloned successfully';

				$this->packagesData->filters =
					$filtersArr = $this->usePackage(FiltersPackage::class)->getFiltersForComponent($data['component_id']);

				return true;
		}

		return false;
	}

	public function getDefaultFilter(int $componentId)
	{
		$params =
			[
				'conditions'	=> 'component_id = :cid: AND is_default = :isd:',
				'bind'			=>
					[
						'cid'	=> $componentId,
						'isd'	=> '1'
					]
			];

		$this->defaultFilter = $this->getByParams($params);

		if ($this->defaultFilter) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->defaultFilter = $this->defaultFilter;

			return true;
		}

		$this->packagesData->responseCode = 1;

		return false;
	}

	protected function checkDefaultFilter(array $data)
	{
		$this->getDefaultFilter($data['component_id']);

		if ($this->defaultFilter) {
			if (isset($data['id']) && $data['id'] !== '') {

				if ($data['id'] !== $this->defaultFilter[0]['id']) {
					$this->defaultFilter[0]['is_default'] = 0;

					if (!$this->update($this->defaultFilter[0])) {
						$this->packagesData->responseCode = 1;

						$this->packagesData->responseMessage = 'Error removing default filter';

						return false;
					}
				}
			} else {
				$this->defaultFilter[0]['is_default'] = 0;
				if (!$this->update($this->defaultFilter[0])) {
					$this->packagesData->responseCode = 1;

					$this->packagesData->responseMessage = 'Error removing default filter';

					return false;
				}
			}
		}

		return true;
	}
}