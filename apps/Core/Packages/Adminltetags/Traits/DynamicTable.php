<?php

namespace Apps\Core\Packages\Adminltetags\Traits;

use Apps\Core\Packages\Adminltetags\Adminltetags;

trait DynamicTable {
    public function generateDTContent(
        $package,
        string $postUrl = null,
        $postUrlParams,
        array $columnsForTable = [],
        $withFilter = true,
        array $columnsForFilter = [],
        $controlActions = null,
        array $dtReplaceColumnsTitle = null,
        $dtReplaceColumns = null,
        string $dtNotificationTextFromColumn = null,
        $dtAdditionControlButtons = null,
        bool $dtAdditionControlButtonsBeforeControlButtons = false,
        int $componentId = null,
        $resetCache = false,
        $enableCache = true,
        $packageData = []
    ) {
        if (gettype($package) === 'string') {
            $package = $this->usePackage($package);
        }

        if (count($columnsForTable) > 0) {
            $columnsForTable = array_merge($columnsForTable, ['id']);
        }

        if (count($columnsForFilter) > 0) {
            $columnsForFilter = array_merge($columnsForFilter, ['id']);
        } else {
            $withFilter = false;//Disable filter if no columns are defined.
        }

        if ($this->request->isGet()) {
            $table = [];

            $modelsColumnMap = $package->getModelsColumnMap($this->removeEscapeFromName($columnsForTable));

            if (isset($modelsColumnMap['columns'])) {
                $table['columns'] = $this->sortColumns($columnsForTable, $modelsColumnMap['columns']);
            } else {
                $table['columns'] = $this->sortColumns($columnsForTable, $modelsColumnMap);
            }

            if ($dtReplaceColumnsTitle && count($dtReplaceColumnsTitle) > 0) {
                foreach ($dtReplaceColumnsTitle as $dtReplaceColumnsTitleKey => $dtReplaceColumnsTitleValue) {
                    $table['columns'][$dtReplaceColumnsTitleKey]['name'] = $dtReplaceColumnsTitleValue;
                }
            }

            if ($postUrl === null) {
                throw new \Exception('Datatables postUrl Missing.');
            }

            $table['postUrl'] = $this->links->url($postUrl);

            $table['component'] = $this->component;

            if (!$componentId) {
                $componentId = $this->component['id'];
            }

            if ($withFilter) {
                $table['withFilter'] = $withFilter;

                $account = $this->auth->account();

                if ($account) {
                    $filtersArr = $this->basepackages->filters->getFiltersForAccountAndComponent($account, $componentId);
                } else {
                    $filtersArr = $this->basepackages->filters->getFiltersForComponent($componentId);
                }

                $modelsColumnMap = $package->getModelsColumnMap($this->removeEscapeFromName($columnsForFilter));

                if (isset($modelsColumnMap['columns'])) {
                    $table['filterColumns'] = $this->sortColumns($columnsForFilter, $modelsColumnMap['columns']);
                } else {
                    $table['filterColumns'] = $this->sortColumns($columnsForFilter, $modelsColumnMap);
                }

                foreach ($filtersArr as $key => $filter) {
                    $table['filters'][$filter['id']] = $filter;
                    $table['filters'][$filter['id']]['data']['name'] = $filter['name'];
                    $table['filters'][$filter['id']]['data']['id'] = $filter['id'];
                    $table['filters'][$filter['id']]['data']['component_id'] = $filter['component_id'];
                    $table['filters'][$filter['id']]['data']['conditions'] = $filter['conditions'];
                    $table['filters'][$filter['id']]['data']['filter_type'] = $filter['filter_type'];
                    $table['filters'][$filter['id']]['data']['auto_generated'] = $filter['auto_generated'];
                    $table['filters'][$filter['id']]['data']['is_default'] = $filter['is_default'];
                    $table['filters'][$filter['id']]['data']['account_id'] = $filter['account_id'];
                    $table['filters'][$filter['id']]['data']['shared'] = $filter['shared'];
                    $table['filters'][$filter['id']]['data']['shared_ids'] = $filter['shared_ids'];
                    $table['filters'][$filter['id']]['data']['url'] = $filter['url'];

                    if (isset($postUrlParams) && is_array($postUrlParams)) {
                        $table['postUrlParams'] = $postUrlParams;

                        if ($account &&
                            $account['id'] === $filter['account_id'] &&
                            $filter['is_default'] === '1'
                        ) {
                            $table['postUrlParams'] = array_merge($table['postUrlParams'], ['conditions' => $filter['conditions']]);
                        }
                    } else if (isset($this->getData()['filter'])) {
                        if ($this->getData()['filter'] === $filter['id']) {
                            $table['filters'][$filter['id']]['data']['queryFilterId'] = $filter['id'];
                            $table['postUrlParams'] = ['conditions' => $filter['conditions']];
                        } else {
                            $table['filters'][$filter['id']]['data']['queryFilterId'] = $this->getData()['filter'];
                            $table['postUrlParams'] = ['conditions' => '-|id|equals|0&'];
                        }
                    } else if ($account &&
                               $account['id'] === $filter['account_id'] &&
                               $filter['is_default'] === '1'
                    ) {
                        $table['postUrlParams'] = ['conditions' => $filter['conditions']];
                    } else {
                        $table['postUrlParams'] = [];
                    }
                }
            } else {
                if ($postUrlParams) {
                    $table['postUrlParams'] = $postUrlParams;
                } else {
                    $table['postUrlParams'] = [];
                }
                $table['filters'] = [];
                $table['filterColumns'] = [];
            }

            $this->view->table = $table;
        } else if ($this->request->isPost()) {
            $conditions =
                [
                    'columns' => $columnsForTable
                ];

            if ($postUrlParams) {
                $conditions = array_replace($conditions, $postUrlParams);
            }

            try {
                if (count($packageData) === 0) {
                    $packageData = false;
                }

                $pagedData =
                    $package->getPaged(
                        $conditions,
                        $resetCache,
                        $enableCache,
                        $packageData
                    );

                $rows = $pagedData->getItems();

                if (is_callable($dtReplaceColumns)) {
                    $rows = $this->extractColumnsForTable($columnsForTable, $dtReplaceColumns($rows));//Call & extract columnsTable

                    $dtReplaceColumns = null;//Remove function before its passed to Table
                }
            } catch (\Exception $e) {
                $rows = [];

                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->debug($e);
                }

                $this->addResponse('Exception: Please check exceptions log for more details.', 1);

                return;
            }

            if ($dtAdditionControlButtons && is_callable($dtAdditionControlButtons)) {
                $dtAdditionControlButtons = $dtAdditionControlButtons($rows);
            }

            if ($controlActions && is_callable($controlActions)) {
                $rows = $controlActions($rows);
            } else if ($controlActions) {
                // add control action to each row
                foreach($rows as &$row) {
                    $actions = [];

                    foreach ($controlActions['actionsToEnable'] as $key => &$action) {
                        if (isset($controlActions['disableActionsForIds'][$key]) &&
                            is_array($controlActions['disableActionsForIds'][$key]) &&
                            count($controlActions['disableActionsForIds'][$key]) > 0
                        ) {
                            if (!in_array($row['id'], $controlActions['disableActionsForIds'][$key])) {
                                if (isset($controlActions['includeQ']) && $controlActions['includeQ'] == true) {
                                    $actions[$key] = $this->links->url($action . '/id/' . $row['id']);
                                } else {
                                    $actions[$key] = $this->links->url($action . '/q/id/' . $row['id']);
                                }
                            }
                        } else if (isset($controlActions['enableActionsForIds'][$key]) &&
                            is_array($controlActions['enableActionsForIds'][$key]) &&
                            count($controlActions['enableActionsForIds'][$key]) > 0
                        ) {
                            if (in_array($row['id'], $controlActions['enableActionsForIds'][$key])) {
                                if (isset($controlActions['includeQ']) && $controlActions['includeQ'] == true) {
                                    $actions[$key] = $this->links->url($action . '/id/' . $row['id']);
                                } else {
                                    $actions[$key] = $this->links->url($action . '/q/id/' . $row['id']);
                                }
                            }
                        } else {
                            if (isset($controlActions['includeQ']) && $controlActions['includeQ'] == true) {
                                $actions[$key] = $this->links->url($action . '/id/' . $row['id']);
                            } else {
                                $actions[$key] = $this->links->url($action . '/q/id/' . $row['id']);
                            }
                        }
                    }

                    $row["__control"] = $actions;
                }
            }

            if ($this->api->isApi()) {
                return ['rows' => $rows, 'counters' => $package->packagesData->paginationCounters];
            }

            $adminltetags = new Adminltetags();

            $this->view->rows =
                $adminltetags->useTag('content/listing/table',
                    [
                        'componentId'                                        => $this->app['route'] . '-' . strtolower($this->componentName),
                        'dtRows'                                             => $rows,
                        'dtNotificationTextFromColumn'                       => $dtNotificationTextFromColumn,
                        'dtPagination'                                       => true,
                        'dtShowRowControls'                                  => $controlActions ? true: false,
                        'dtPaginationCounters'                               => $package->packagesData->paginationCounters,
                        'dtReplaceColumns'                                   => $dtReplaceColumns,
                        'dtAdditionControlButtons'                           => $dtAdditionControlButtons,
                        'dtAdditionControlButtonsBeforeControlButtons'       => $dtAdditionControlButtonsBeforeControlButtons
                    ]
                );
        }
    }

    protected function removeEscapeFromName(array $columns)
    {
        foreach ($columns as $key => &$column) {
            $column = str_replace('[', '', $column);
            $column = str_replace(']', '', $column);
        }

        return $columns;
    }

    protected function extractColumnsForTable($columnsForTable, $rows)
    {
        if (!$columnsForTable) {
            return $rows;
        }

        $columnsForTable = $this->removeEscapeFromName($columnsForTable);

        foreach ($rows as $key => $value) {
            foreach ($value as $columnKey => $columnValue) {
                if (!in_array($columnKey, $columnsForTable)) {
                    unset($rows[$key][$columnKey]);
                }
            }
        }

        return $rows;
    }

    protected function sortColumns($columnsForTable, $dbColumns)
    {
        $columnsForTable = $this->removeEscapeFromName($columnsForTable);

        $sortedColumns = [];

        $sortedColumns = array_merge(['id' => $dbColumns['id']]);

        foreach ($columnsForTable as $key => $column) {
            if ($column !== 'id') {
                if ($dbColumns[$column]) {
                    $sortedColumns[$column] = $dbColumns[$column];
                }
            }
        }

        return $sortedColumns;
    }
}