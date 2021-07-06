<?php

namespace Apps\Dash\Packages\AdminLTETags\Traits;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Json;

trait DynamicTable {
    public function generateDTContent(
        $package,
        string $postUrl,
        $postUrlParams,
        array $columnsForTable = [],
        $withFilter = true,
        array $columnsForFilter = [],
        array $controlActions = null,
        array $dtReplaceColumnsTitle = null,
        $dtReplaceColumns = null,
        string $dtNotificationTextFromColumn = null,
        array $dtAdditionControlButtons = null,
        bool $dtAdditionControlButtonsBeforeControlButtons = false,
        int $componentId = null
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
            $table['columns'] = $package->getModelsColumnMap($this->removeEscapeFromName($columnsForTable));
            if ($dtReplaceColumnsTitle && count($dtReplaceColumnsTitle) > 0) {
                foreach ($dtReplaceColumnsTitle as $dtReplaceColumnsTitleKey => $dtReplaceColumnsTitleValue) {
                    $table['columns'][$dtReplaceColumnsTitleKey]['name'] = $dtReplaceColumnsTitleValue;
                }
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

                $table['filterColumns'] = $package->getModelsColumnMap($this->removeEscapeFromName($columnsForFilter));
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
                    } else if (isset($this->getData()['filter'])) {
                        if ($this->getData()['filter'] === $filter['id']) {
                            $table['filters'][$filter['id']]['data']['queryFilterId'] = $filter['id'];
                            $table['postUrlParams'] = ['conditions' => $filter['conditions']];
                        } else {
                            $table['filters'][$filter['id']]['data']['queryFilterId'] = $this->getData()['filter'];
                            $table['postUrlParams'] = ['conditions' => '-:id:equals:0&'];
                        }
                    } else if (($account && ($account['id'] === $filter['account_id'])) &&
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
            if (is_callable($dtReplaceColumns)) {
                $pagedData = $package->getPaged();

                $rows = $pagedData->getItems();

                $rows = $this->extractColumnsForTable($columnsForTable, $dtReplaceColumns($rows));//Call & extract columnsTable

                $dtReplaceColumns = null;//Remove function before its passed to Table
            } else {
                $pagedData =
                    $package->getPaged(
                        [
                            'columns' => $columnsForTable
                        ]
                    );

                $rows = $pagedData->getItems();
            }


            if ($controlActions) {
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

            $adminltetags = new AdminLTETags();

            $this->view->rows =
                $adminltetags->useTag('content/listing/table',
                    [
                        'componentId'                                        => $this->app['route'] . '-' . strtolower($this->componentName),
                        'dtRows'                                             => $rows,
                        'dtNotificationTextFromColumn'                       => $dtNotificationTextFromColumn,
                        'dtPagination'                                       => true,
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
}