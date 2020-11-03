<?php

namespace Applications\Admin\Components\Users;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class UsersComponent extends BaseComponent
{
    public function viewAction()
    {
        $columnsToGet = [];

        // $users = $this->users->init();
        $users = $this->modules->components;

        // $this->view->disable();
        if ($this->request->isGet()) {
            $table['columns'] = $users->getModelsColumnMap($columnsToGet);
            $table['postUrl'] = $this->links->url('users/view');

            $this->view->table = $table;

        } else if ($this->request->isPost()) {

            $pagedData =
                $users->getPaged(
                    [
                        'columns' => $columnsToGet
                    ]
                );

            $rows = $pagedData->getItems();

            //add control action to each row
            foreach($rows as &$row) {
                $row["__control"] =
                    [
                        'view' => $this->links->url('users/user/q/id/' . $row['id']),
                        'edit' => $this->links->url('users/user/edit/q/id/' . $row['id']),
                        'remove'=>$this->links->url('users/user/remove/q/id/' . $row["id"])
                    ];
            }

            $adminltetags = new AdminLTETags($this->view, $this->tag, $this->links, $this->escaper);

            $this->view->rows =
                $adminltetags->useTag('content/listing/table',
                    [
                        'componentId'                   => 'admin-users',
                        'dtRows'                        => $rows,
                        'dtNotificationTextFromColumn'  => 'email',
                        'dtPagination'                  => true,
                        'dtPaginationCounters'          => $users->packagesData->paginationCounters
                    ]
                );
        }


        // $columnsToGet = ['id', 'email', 'can_login'];

        // $users = $this->users->init();

        // if ($this->request->isGet()) {
        //     $table['columns'] = $users->getModelsColumnMap($columnsToGet);
        //     $table['postUrl'] = $this->links->url('users/view');

        //     $this->view->table = $table;

        // } else if ($this->request->isPost()) {

        //     $pagedData =
        //         $users->getPaged(
        //             [
        //                 'columns' => $columnsToGet
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