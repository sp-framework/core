<?php

namespace Apps\Dash\Components\System\Workers\Tasks;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class TasksComponent extends BaseComponent
{
    use DynamicTable;

    protected $tasks;

    public function initialize()
    {
        $this->tasks = $this->basepackages->workers->tasks;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $schedules = $this->basepackages->workers->schedules->schedules;

        if (isset($this->getData()['id'])) {
            $functions = $this->tasks->getAllFunctions();

            $this->view->functions = $functions;

            $this->view->schedules = $schedules;

            if ($this->getData()['id'] != 0) {
                $task = $this->tasks->getById($this->getData()['id']);

                $this->view->task = $task;
            }
            $this->view->pick('tasks/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'apps',
                    'remove'    => 'apps/remove',
                ]
            ];

        $schedulesArr = [];

        foreach ($schedules as $scheduleKey => $schedule) {
            $schedulesArr[$schedule['id']] = $schedule['name'];
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
                    'edit'      => 'system/workers/tasks',
                    'remove'    => 'system/workers/tasks/remove'
                ]
            ];

        $this->generateDTContent(
            $this->tasks,
            'system/workers/tasks/view',
            null,
            ['name', 'schedule_id', 'priority', 'enabled', 'status', 'previous_run', 'next_run'],
            true,
            ['name', 'schedule_id', 'priority', 'enabled', 'status', 'previous_run', 'next_run'],
            $controlActions,
            ['schedule_id' => 'schedule'],
            $replaceColumns,
            'name'
        );

        $this->view->pick('tasks/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->formatStatus($dataKey, $data);
            $data = $this->formatPreviousRun($dataKey, $data);
            $data = $this->formatNextRun($dataKey, $data);
            $data = $this->formatEnabled($dataKey, $data);
        }

        return $dataArr;
    }

    protected function formatEnabled($rowId, $data)
    {
        if ($data['enabled'] == '0') {
            $data['enabled'] = '<span class="badge badge-secondary text-uppercase">No</span>';
        } else if ($data['enabled'] == '1') {
            $data['enabled'] = '<span class="badge badge-success text-uppercase">Yes</span>';
        }

        return $data;
    }

    protected function formatPreviousRun($rowId, $data)
    {
        if (!$data['previous_run'] || $data['previous_run'] == '0') {
            $data['previous_run'] = '-';
        }

        return $data;
    }

    protected function formatNextRun($rowId, $data)
    {
        if (!$data['next_run'] || $data['next_run'] == '0' || $data['enabled'] == '0') {
            $data['next_run'] = '-';
        }

        return $data;
    }

    protected function formatStatus($rowId, $data)
    {
        if ($data['enabled'] == '0') {
            $data['status'] = '-';
        }

        if ($data['status'] == '0') {
            $data['status'] = '-';
        } else if ($data['status'] == '1') {
            $data['status'] = '<span class="badge badge-secondary text-uppercase">Scheduled</span>';
        } else if ($data['status'] == '2') {
            $data['status'] = '<span class="badge badge-info text-uppercase">Running...</span>';
        } else if ($data['status'] == '3') {
            $data['status'] = '<span class="badge badge-danger text-uppercase">Error!</span>';
        }

        return $data;
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->tasks->addTask($this->postData());

            $this->addResponse(
                $this->tasks->packagesData->responseMessage,
                $this->tasks->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->tasks->updateTask($this->postData());

            $this->addResponse(
                $this->tasks->packagesData->responseMessage,
                $this->tasks->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->tasks->removeTask($this->postData());

            $this->addResponse(
                $this->tasks->packagesData->responseMessage,
                $this->tasks->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}