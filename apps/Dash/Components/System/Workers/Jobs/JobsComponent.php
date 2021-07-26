<?php

namespace Apps\Dash\Components\System\Workers\Jobs;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class JobsComponent extends BaseComponent
{
    use DynamicTable;

    protected $jobs;

    public function initialize()
    {
        $this->jobs = $this->basepackages->workers->jobs;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $job = $this->jobs->getById($this->getData()['id']);

                $this->view->job = $job;
            }
            $this->view->pick('jobs/view');

            return;
        }

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    return $this->replaceColumns($dataArr);
                }

                return $dataArr;
            };

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'view'      => 'system/workers/jobs'
                ]
            ];

        $conditions =
            [
                'order'         => 'id desc'
            ];

        $this->generateDTContent(
            $this->jobs,
            'system/workers/jobs/view',
            $conditions,
            ['task_id', 'worker_id', 'run_on', 'status', 'execution_time'],
            true,
            ['task_id', 'worker_id', 'run_on', 'status', 'execution_time'],
            $controlActions,
            ['task_id'=>'task', 'worker_id'=>'worker'],
            $replaceColumns,
            'id'
        );

        $this->view->pick('jobs/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->formatStatus($dataKey, $data);
            $data = $this->formatWorker($dataKey, $data);
            $data = $this->formatTask($dataKey, $data);
        }

        return $dataArr;
    }

    protected function formatStatus($rowId, $data)
    {
        if ($data['status'] == '0') {
            $data['status'] = '-';
        } else if ($data['status'] == '1') {
            $data['status'] = '<span class="badge badge-secondary text-uppercase">Scheduled</span>';
        } else if ($data['status'] == '2') {
            $data['status'] = '<span class="badge badge-info text-uppercase">Running...</span>';
        } else if ($data['status'] == '3') {
            $data['status'] = '<span class="badge badge-success text-uppercase">Success</span>';
        } else if ($data['status'] == '4') {
            $data['status'] = '<span class="badge badge-danger text-uppercase">Error!</span>';
        }

        return $data;
    }

    protected function formatWorker($rowId, $data)
    {
        $worker = $this->basepackages->workers->workers->getById($data['worker_id']);

        if ($worker) {
            $data['worker_id'] = $worker['name'];
        }

        return $data;
    }

    protected function formatTask($rowId, $data)
    {
        $task = $this->basepackages->workers->tasks->getById($data['task_id']);

        if ($task) {
            $data['task_id'] = $task['name'];
        }

        return $data;
    }
    // /**
    //  * @acl(name="add")
    //  */
    // public function addAction()
    // {
    //     if ($this->request->isPost()) {
    //         if (!$this->checkCSRF()) {
    //             return;
    //         }

    //         $this->jobs->addSchedule($this->postData());

    //         $this->addResponse(
    //             $this->jobs->packagesData->responseMessage,
    //             $this->jobs->packagesData->responseCode
    //         );
    //     } else {
    //         $this->addResponse('Method Not Allowed', 1);
    //     }
    // }

    // /**
    //  * @acl(name="update")
    //  */
    // public function updateAction()
    // {
    //     if ($this->request->isPost()) {
    //         if (!$this->checkCSRF()) {
    //             return;
    //         }

    //         $this->jobs->updateSchedule($this->postData());

    //         $this->addResponse(
    //             $this->jobs->packagesData->responseMessage,
    //             $this->jobs->packagesData->responseCode
    //         );
    //     } else {
    //         $this->addResponse('Method Not Allowed', 1);
    //     }
    // }

    // /**
    //  * @acl(name="remove")
    //  */
    // public function removeAction()
    // {
    //     if ($this->request->isPost()) {

    //         $this->jobs->removeSchedule($this->postData());

    //         $this->addResponse(
    //             $this->jobs->packagesData->responseMessage,
    //             $this->jobs->packagesData->responseCode
    //         );
    //     } else {
    //         $this->addResponse('Method Not Allowed', 1);
    //     }
    // }
}