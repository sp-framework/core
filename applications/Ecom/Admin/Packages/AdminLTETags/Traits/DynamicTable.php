<?php

namespace Applications\Ecom\Admin\Packages\AdminLTETags\Traits;

use Applications\Ecom\Admin\Packages\AdminLTETags\AdminLTETags;

trait DynamicTable {
    public function generateDTContent(
        $package,
        string $postUrl,
        int $componentId = null,
        array $columnsForTable = [],
        $withFilter = true,
        array $columnsForFilter = [],
        array $controlActions = null,
        array $dtReplaceColumnsTitle = null,
        array $dtReplaceColumns = null,
        string $dtNotificationTextFromColumn = null,
        array $dtAdditionControlButtons = null
    )
    {
        if (gettype($package) === 'string') {
            $package = $this->usePackage($package);
        }

        if (count($columnsForTable) > 0) {
            $columnsForTable = array_merge($columnsForTable, ['id']);
            $columnsForFilter = array_merge($columnsForFilter, ['id']);
        }

        if ($this->request->isGet()) {

            $table = [];
            $table['columns'] = $package->getModelsColumnMap($columnsForTable);
            if ($dtReplaceColumnsTitle && count($dtReplaceColumnsTitle) > 0) {
                foreach ($dtReplaceColumnsTitle as $dtReplaceColumnsTitleKey => $dtReplaceColumnsTitleValue) {
                    $table['columns'][$dtReplaceColumnsTitleKey]['name'] = $dtReplaceColumnsTitleValue;
                }
            }
            $table['postUrl'] = $this->links->url($postUrl);
            $table['postUrlParams'] = [];

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

                $table['filterColumns'] = $package->getModelsColumnMap($columnsForFilter);
                foreach ($filtersArr as $key => $filter) {
                    $table['filters'][$filter['id']] = $filter;
                    $table['filters'][$filter['id']]['data']['name'] = $filter['name'];
                    $table['filters'][$filter['id']]['data']['id'] = $filter['id'];
                    $table['filters'][$filter['id']]['data']['component_id'] = $filter['component_id'];
                    $table['filters'][$filter['id']]['data']['conditions'] = $filter['conditions'];
                    $table['filters'][$filter['id']]['data']['type'] = $filter['type'];
                    $table['filters'][$filter['id']]['data']['auto_generated'] = $filter['auto_generated'];
                    $table['filters'][$filter['id']]['data']['is_default'] = $filter['is_default'];
                    $table['filters'][$filter['id']]['data']['account_id'] = $filter['account_id'];
                    $table['filters'][$filter['id']]['data']['shared_ids'] = $this->escaper->escapeHtml($filter['shared_ids']);
                    $table['filters'][$filter['id']]['data']['url'] = $this->links->url($this->component['route']) . '/q/filter/' . $filter['id'];

                    if (isset($this->getData()['filter'])) {
                        if ($this->getData()['filter'] === $filter['id']) {
                            $table['filters'][$filter['id']]['data']['queryFilterId'] = $filter['id'];
                            $table['postUrlParams'] = ['conditions' => $filter['conditions']];
                        } else {
                            $table['filters'][$filter['id']]['data']['queryFilterId'] = $this->getData()['filter'];
                            $table['postUrlParams'] = ['conditions' => '-:id:equals:0&'];
                        }
                    } else if ($filter['is_default'] === '1') {
                        $table['postUrlParams'] = ['conditions' => $filter['conditions']];
                    }
                }
            }

            $this->view->table = $table;

        } else if ($this->request->isPost()) {
            $pagedData =
                $package->getPaged(
                    [
                        'columns' => $columnsForTable
                    ]
                );

            $rows = $pagedData->getItems();

            if ($controlActions) {
                // add control action to each row
                foreach($rows as &$row) {
                    $actions = [];

                    foreach ($controlActions['actionsToEnable'] as $key => &$action) {
                        if (isset($controlActions['disableActionsForIds']) &&
                            is_array($controlActions['disableActionsForIds']) &&
                            count($controlActions['disableActionsForIds']) > 0
                        ) {
                            if (!in_array($row['id'], $controlActions['disableActionsForIds'])) {
                                $actions[$key] = $this->links->url($action . '/q/id/' . $row['id']);
                            }
                        } else {
                            $actions[$key] = $this->links->url($action . '/q/id/' . $row['id']);
                        }
                    }

                    $row["__control"] = $actions;
                }
            }

            $adminltetags = new AdminLTETags();

            $this->view->rows =
                $adminltetags->useTag('content/listing/table',
                    [
                        'componentId'                   => $this->view->componentId,
                        'dtRows'                        => $rows,
                        'dtNotificationTextFromColumn'  => $dtNotificationTextFromColumn,
                        'dtPagination'                  => true,
                        'dtPaginationCounters'          => $package->packagesData->paginationCounters,
                        'dtReplaceColumns'              => $dtReplaceColumns,
                        'dtAdditionControlButtons'      => $dtAdditionControlButtons
                    ]
                );
        }
    }
}