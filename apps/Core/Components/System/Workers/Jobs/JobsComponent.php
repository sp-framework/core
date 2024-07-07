<?php

namespace Apps\Core\Components\System\Workers\Jobs;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
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
                $job = $this->jobs->getJobById($this->getData()['id']);

                if (!$job) {
                    return $this->throwIdNotFound();
                }

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
            'id',
            null,
            false,
            null,
            true
        );

        $this->view->pick('jobs/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->formatTask($dataKey, $data);
            $data = $this->formatWorker($dataKey, $data);
            $data = $this->formatRunon($dataKey, $data);
            $data = $this->formatStatus($dataKey, $data);
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
        } else if ($data['status'] == '5') {
            $data['status'] = '<span class="badge badge-warning text-uppercase">Rescheduled!</span>';
        }

        return $data;
    }

    protected function formatWorker($rowId, $data)
    {
        if ($data['worker_id'] != '0') {
            $worker = $this->basepackages->workers->workers->getById($data['worker_id']);

            if ($worker) {
                $data['worker_id'] = $worker['name'];
            }
        } else {
            $data['worker_id'] = '-';
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

    protected function formatRunon($rowId, $data)
    {
        if (is_string($data['run_on'])) {
            $data['run_on'] = $this->helper->decode($data['run_on']);
        }

        if (is_array($data['run_on']) && isset($data['run_on'][0])) {
            $data['run_on'] = $data['run_on'][0];
        }

        return $data;
    }
}