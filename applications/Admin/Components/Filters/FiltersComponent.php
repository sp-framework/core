<?php

namespace Applications\Admin\Components\Filters;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;
use Applications\Admin\Packages\Filters\Filters;
use Applications\Admin\Packages\Filters\Install\Package;
use System\Base\BaseComponent;

class FiltersComponent extends BaseComponent
{
    public function viewAction()
    {
        $columnsToGet = ['id', 'name', 'permission'];
        $filtersPackage = $this->usePackage(Filters::class);

        // $package = (new Package())->init();
        // $install = $package->installPackage(true);
        // $reInstall = $package->reInstallPackage();

        // if (!$install) {
        //     var_dump($package->packagesData);
        // } else {
        //     echo 'Installed';
        // }

        // $columnsToGet = [];
        // $filtersPackage = $this->modules->packages;

        // // $this->view->disable();
        if ($this->request->isGet()) {
            $table['columns'] = $filtersPackage->getModelsColumnMap($columnsToGet);
            $table['postUrl'] = $this->links->url('filters/view');

            $this->view->table = $table;

        } else if ($this->request->isPost()) {

            $pagedData =
                $filtersPackage->getPaged(
                    [
                        'columns' => $columnsToGet
                    ]
                );

            $rows = $pagedData->getItems();

            //add control action to each row
            foreach($rows as &$row) {
                $row["__control"] =
                    [
                        'view' => $this->links->url('filters/user/q/id/' . $row['id']),
                        'edit' => $this->links->url('filters/user/edit/q/id/' . $row['id']),
                        'remove'=>$this->links->url('filters/user/remove/q/id/' . $row["id"])
                    ];
            }

            $adminltetags = new AdminLTETags($this->view, $this->tag, $this->links, $this->escaper);

            $this->view->rows =
                $adminltetags->useTag('content/listing/table',
                    [
                        'componentId'                   => 'admin-filters',
                        'dtRows'                        => $rows,
                        'dtNotificationTextFromColumn'  => 'email',
                        'dtPagination'                  => true,
                        'dtPaginationCounters'          => $filtersPackage->packagesData->paginationCounters,
                        'dtReplaceColumns'              =>
                            [
                                'permission' =>
                                [
                                    'html'  =>
                                    [
                                        '0'    => '<span class="badge badge-primary">System</span>',
                                        '1'    => '<span class="badge badge-success">User</span>',
                                    ]
                                ]
                            ]
                    ]
                );
        }
    }
}
        // Installation Example to follow
        // $package = new Package();
        // $install = $package->installPackage(true);

        // var_dump($package->describe());
        // var_dump($package->dbViews());
        // var_dump($package->describe('filters'));
        // var_dump($package->describe('filters', true));
        // var_dump($package->describe('filters', false, true));
        // var_dump($package->tableExists('filters'));
        // var_dump($package->tableExists('filter'));
        // var_dump($package->createTable('filters', (new Filters)->columns()));

        // if (!$install) {
        //     var_dump($package->packagesData);
        // } else {
        //     echo 'Installed';
        // }

        // $this->view->disable();