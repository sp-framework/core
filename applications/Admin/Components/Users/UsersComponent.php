<?php

namespace Applications\Admin\Components\Users;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;
use Applications\Admin\Packages\Filters\Filters;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class UsersComponent extends BaseComponent
{
    public function viewAction()
    {
        $columnsForTable = [];
        $columnsForFilter = [];

        $users = $this->users->init();
        // $users = $this->modules->components;

        // $this->view->disable();
        if ($this->request->isGet()) {
            $table['columns'] = $users->getModelsColumnMap($columnsForTable);
            $table['filterColumns'] = $users->getModelsColumnMap($columnsForFilter);
            $table['postUrl'] = $this->links->url('users/view');
            $table['component'] = $this->component;

            // $filtersPackage = $this->usePackage(Filters::class)->getFiltersForComponent($this->component['id']);
            $filtersArr = $this->usePackage(Filters::class)->getFiltersForComponent(5);

            $table['postUrlParams'] = [];
            foreach ($filtersArr as $key => $filter) {
                $table['filters'][$filter['id']] = $filter;
                $table['filters'][$filter['id']]['data']['name'] = $filter['name'];
                $table['filters'][$filter['id']]['data']['id'] = $filter['id'];
                $table['filters'][$filter['id']]['data']['component_id'] = $filter['component_id'];
                $table['filters'][$filter['id']]['data']['conditions'] = $filter['conditions'];
                $table['filters'][$filter['id']]['data']['permission'] = $filter['permission'];
                $table['filters'][$filter['id']]['data']['is_default'] = $filter['is_default'];
                $table['filters'][$filter['id']]['data']['shared_ids'] = $filter['shared_ids'];

                if ($filter['is_default'] === '1') {
                    $table['postUrlParams'] = ['conditions' => $filter['conditions']];
                }
            }

            $this->view->table = $table;

            // var_dump($filters);
            // $this->view->disable();

        } else if ($this->request->isPost()) {

            $pagedData =
                $users->getPaged(
                    [
                        'columns' => $columnsForTable
                    ]
                );

            $rows = $pagedData->getItems();

            //add control action to each row
            foreach($rows as &$row) {
                $row["__control"] =
                    [
                        'view' => $this->links->url('users/user/q/id/' . $row['id']),
                        'edit' => $this->links->url('users/user/edit/q/id/' . $row['id']),
                        'remove'=>$this->links->url('users/user/remove/q/id/' . $row['id'])
                    ];
            }

            $adminltetags = new AdminLTETags($this->view, $this->tag, $this->links, $this->escaper);

            $this->view->rows =
                $adminltetags->useTag('content/listing/table',
                    [
                        'componentId'                   => $this->view->componentId,
                        'dtRows'                        => $rows,
                        'dtNotificationTextFromColumn'  => 'email',
                        'dtPagination'                  => true,
                        'dtPaginationCounters'          => $users->packagesData->paginationCounters
                    ]
                );
        }
        // $this->view->disable();

        // $columnsForTable = ['id', 'email', 'can_login'];

        // $users = $this->users->init();

        // if ($this->request->isGet()) {
        //     $table['columns'] = $users->getModelsColumnMap($columnsForTable);
        //     $table['postUrl'] = $this->links->url('users/view');

        //     $this->view->table = $table;

        // } else if ($this->request->isPost()) {

        //     $pagedData =
        //         $users->getPaged(
        //             [
        //                 'columns' => $columnsForTable
        //             ]
        //         );

        //     $rows = $pagedData->getItems();

        //     //add control action to each row
        //     foreach($rows as &$row) {
        //         $row["__control"] =
        //             [
        //                 'view' => $this->links->url('users/user/q/id/' . $row['id']),
        //                 'edit' => $this->links->url('users/user/edit/q/id/' . $row['id']),
        //                 'remove'=>$this->links->url('users/user/remove/q/id/' . $row["id"])
        //             ];
        //     }

        //     $adminltetags = new AdminLTETags($this->view, $this->tag, $this->links, $this->escaper);

        //     $this->view->rows =
        //         $adminltetags->useTag('content/listing/table',
        //             [
        //                 'componentId'                   => 'admin-users',
        //                 'dtRows'                        => $rows,
        //                 'dtNotificationTextFromColumn'  => 'email',
        //                 'dtPagination'                  => true,
        //                 'dtPaginationCounters'          => $users->packagesData->paginationCounters,
        //                 'dtAdditionControlButtons'      => [
        //                                                     'view_role_accounts' => [
        //                                                         'title'             => 'View Role Accounts',
        //                                                         'additionalClass'   => 'contentAjaxLink',
        //                                                         'icon'              => 'user-friends',
        //                                                         'buttonType'        => 'info'
        //                                                     ]
        //                                                 ]
        //             ]
        //         );
    }
}