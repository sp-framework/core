<?php

namespace System\Base\Providers\ModulesServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use System\Base\BasePackage;

class FilterInstaller extends BasePackage
{
    public function installFilters($componentClass)
    {
        try {
            $file = 'apps/' . implode('/', array_slice(explode('\\', get_class($componentClass)), 1, -1)) . '/component.json';

            if ($this->localContent->fileExists($file)) {
                $installComponentJsonFile = $this->helper->decode($this->localContent->read($file), true);

                if (strtolower($installComponentJsonFile['app_type']) === 'core') {
                    return true;
                }

                if (!isset($installComponentJsonFile['filters']) ||
                    (isset($installComponentJsonFile['filters']) &&
                     is_array($installComponentJsonFile['filters']) &&
                     count($installComponentJsonFile['filters']) === 0)
                ) {
                    return true;
                }

                //Get Component information
                $componentClassArr = array_slice(explode('\\', get_class($componentClass)), 1, -2);
                $componentClass = 'Apps\\' . implode('\\', $componentClassArr) . '\\' . $this->helper->last($componentClassArr) . 'Component';

                $component = $this->modules->components->init(true)->getComponentByClass($componentClass);

                if ($component) {
                    if (isset($installComponentJsonFile['filters'])) {
                        if (!is_array($installComponentJsonFile['filters']) && $installComponentJsonFile['filters'] !== '') {
                            $installComponentJsonFile['filters'] = $this->helper->decode($installComponentJsonFile['filters'], true);
                        }
                    }

                    if (isset($installComponentJsonFile['filters']) &&
                        count($installComponentJsonFile['filters']) > 0
                    ) {
                        $defaultFilter = null;

                        foreach ($installComponentJsonFile['filters'] as $filterArr) {
                            if (!isset($filterArr['name']) || !isset($filterArr['conditions'])) {
                                continue;
                            }

                            $filter = $this->basepackages->filters->getFiltersByComponentIdAndCondition($component['id'], $filterArr['conditions']);

                            if ($filter) {
                                $this->basepackages->filters->updateFilter(
                                    [
                                        'id'                => $filter['id'],
                                        'name'              => $filterArr['name'],
                                        'app_type'          => $component['app_type'],
                                        'conditions'        => $filterArr['conditions'],
                                        'component_id'      => $component['id'],
                                        'filter_type'       => 0,//System
                                        'is_default'        => $filterArr['is_default'] == 'true' ? 1 : 0,
                                        'auto_generated'    => 0,
                                        'account_id'        => 0,
                                        'archived'          => $filterArr['archived'] == 'true' ? 1 : 0,
                                    ]
                                );
                            } else {
                                $this->basepackages->filters->addFilter(
                                    [
                                        'name'              => $filterArr['name'],
                                        'app_type'          => $component['app_type'],
                                        'conditions'        => $filterArr['conditions'],
                                        'component_id'      => $component['id'],
                                        'filter_type'       => 0,//System
                                        'is_default'        => $filterArr['is_default'] == 'true' ? 1 : 0,
                                        'auto_generated'    => 0,
                                        'account_id'        => 0,
                                        'archived'          => $filterArr['archived'] == 'true' ? 1 : 0,
                                    ]
                                );

                                $filter = $this->basepackages->filters->packagesData->last;
                            }

                            if ($filter['is_default'] === 1) {
                                $defaultFilter = $filter;
                            }
                        }

                        //Remove filters that dont exists
                        $componentFilters = $this->basepackages->filters->getFiltersForComponent((int) $component['id']);

                        if ($componentFilters && count($componentFilters) > 0) {
                            $componentFiltersConditions = [];

                            foreach ($installComponentJsonFile['filters'] as $filter) {
                                if (!in_array($filter['conditions'], $componentFiltersConditions)) {
                                    array_push($componentFiltersConditions, $filter['conditions']);
                                }
                            }
                            //Default All Filter
                            array_push($componentFiltersConditions, '');

                            foreach ($componentFilters as $componentFilter) {
                                if (!in_array($componentFilter['conditions'], $componentFiltersConditions)) {
                                    $this->basepackages->filters->removeFilter($componentFilter);

                                    continue;
                                }

                                //make sure we only make 1 default.
                                if ($defaultFilter) {
                                    if ($defaultFilter['id'] === $componentFilter['id']) {
                                        $componentFilter['is_default'] = 1;
                                    } else {
                                        $componentFilter['is_default'] = 0;
                                    }

                                    $this->basepackages->filters->updateFilter($componentFilter);
                                } else {
                                    if ($componentFilter['conditions'] === '') {
                                        $componentFilter['is_default'] = 1;
                                    }

                                    $this->basepackages->filters->updateFilter($componentFilter);
                                }
                            }
                        }
                    }
                }
            }
        } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
            throw $e;
        }

        if ($this->opCache) {
            $this->opCache->removeCache('components', 'core');
        }

        return true;
    }

    public function uninstallFilters($componentClass)
    {
        //Get MenuId
        $componentClassArr = array_slice(explode('\\', get_class($componentClass)), 1, -2);
        $componentClass = 'Apps\\' . implode('\\', $componentClassArr) . '\\' . $this->helper->last($componentClassArr) . 'Component';

        $component = $this->modules->components->init(true)->getComponentByClass($componentClass);

        if ($component) {
            $componentFilters = $this->basepackages->filters->getFiltersForComponent((int) $component['id']);

            if ($componentFilters && count($componentFilters) > 0) {
                foreach ($componentFilters as $componentFilter) {
                    $componentFilter['force'] = true;

                    unset($componentFilter['component_id']);

                    $this->basepackages->filters->removeFilter($componentFilter);
                }
            }
        }

        return false;
    }
}