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

        $this->tasks = $this->basepackages->workers->tasks;
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

                $task = $this->tasks->getById((int) $job['task_id']);

                if ($task) {
                    $this->view->task = $task;
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
            ['task_id', 'worker_id', 'run_on', 'status', 'total_execution_time', 'can_terminate'],
            true,
            ['task_id', 'worker_id', 'run_on', 'status', 'total_execution_time', 'can_terminate'],
            $controlActions,
            ['task_id' => 'task', 'worker_id' => 'worker', 'can_terminate' => 'terminate'],
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
            $data = $this->formatTerminate($dataKey, $data);
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
            $data['status'] = '<span class="badge badge-warning text-uppercase">Rescheduled (No worker)</span>';
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

        if ($data['run_on'] && is_array($data['run_on'])) {
            $data['run_on'] = $this->helper->last($data['run_on']);
        }

        return $data;
    }

    protected function formatTerminate($rowId, $data)
    {
        if ($data['can_terminate'] && $data['status'] == '2') {
            $data['can_terminate'] =
                '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-remove-__control-' . $rowId . '" href="' . $this->links->url('system/workers/jobs/terminate/q/id/' . $data['id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="ml-1 mr-1 text-white btn btn-danger btn-xs rowTerminate text-uppercase" data-notificationtextfromcolumn="id">
                    <i class="fas fa-fw fa-xs fa-circle-xmark"></i>
                </a>';
        } else {
            $data['can_terminate'] = '-';
        }

        return $data;
    }

    public function terminateAction()
    {
        $this->requestIsPost();

        $this->jobs->terminateJob($this->postData());

        $this->addResponse(
            $this->jobs->packagesData->responseMessage,
            $this->jobs->packagesData->responseCode
        );
    }

    public function getJobLogsAction()
    {
        $this->requestIsPost();

        $this->jobs->getJobLogs($this->postData());

        $this->addResponse(
            $this->jobs->packagesData->responseMessage,
            $this->jobs->packagesData->responseCode,
            $this->jobs->packagesData->responseData ?? [],
        );
    }
}