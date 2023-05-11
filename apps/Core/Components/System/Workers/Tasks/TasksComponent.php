<?php

namespace Apps\Core\Components\System\Workers\Tasks;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class TasksComponent extends BaseComponent
{
    use DynamicTable;

    protected $tasks;

    protected $schedules;

    public function initialize()
    {
        $this->tasks = $this->basepackages->workers->tasks;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->schedules = $this->basepackages->workers->schedules->schedules;

        if (isset($this->getData()['id'])) {
            $functions = $this->tasks->getAllFunctions();

            $this->view->functions = $functions;

            $this->view->schedules = $this->schedules;

            if ($this->getData()['id'] != 0) {
                $task = $this->tasks->getById($this->getData()['id']);

                if (!$task) {
                    return $this->throwIdNotFound();
                }

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

        $dtAdditionControlButtons =
            [
                'buttons'                   => [
                    'forcenextrun'          => [
                        'title'             => 'Force Next Run',
                        'icon'              => 'forward',
                        'additionalClass'   => 'rowForceNextRun',
                        'link'              => '/' . $this->app['route'] . '/system/workers/tasks/forceNextRun'
                    ]
                ]
            ];

        $this->generateDTContent(
            $this->tasks,
            'system/workers/tasks/view',
            null,
            ['name', 'is_on_demand', 'schedule_id', 'enabled', 'status', 'previous_run', 'next_run', 'force_next_run', 'priority'],
            true,
            ['name', 'is_on_demand', 'schedule_id', 'enabled', 'status', 'previous_run', 'next_run', 'force_next_run', 'priority'],
            $controlActions,
            ['schedule_id' => 'schedule', 'is_on_demand' => 'Runs On Demand'],
            $replaceColumns,
            'name',
            $dtAdditionControlButtons,
            false,
            null,
            true
        );

        $this->view->pick('tasks/list');
    }

    protected function replaceColumns($dataArr)
    {
        $schedulesArr = [];

        foreach ($this->schedules as $scheduleKey => $schedule) {
            $schedulesArr[$schedule['id']] = $schedule['name'];
        }

        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->formatSchedule($dataKey, $data, $schedulesArr);
            $data = $this->formatStatus($dataKey, $data);
            $data = $this->formatPreviousRun($dataKey, $data);
            $data = $this->formatNextRun($dataKey, $data);
            $data = $this->formatEnabled($dataKey, $data);
            $data = $this->formatForceNextRun($dataKey, $data);
        }

        return $dataArr;
    }

    protected function formatSchedule($rowId, $data, $schedulesArr)
    {
        if ($data['is_on_demand'] && $data['is_on_demand'] == '1') {
            $data['is_on_demand'] = '<span class="badge badge-info text-uppercase">Yes</span>';
        } else {
            $data['is_on_demand'] = '-';
        }

        if ($data['schedule_id']) {
            $data['schedule_id'] = $schedulesArr[$data['schedule_id']];
        } else {
            $data['schedule_id'] = '-';
        }

        return $data;
    }

    protected function formatStatus($rowId, $data)
    {
        if ($data['enabled'] == '0' && $data['status'] != '2') {
            $data['status'] = '-';

            return $data;
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

        if ($data['force_next_run'] == '1') {
            return $data;
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
        if ($data['force_next_run'] == '1') {
            return $data;
        }

        if (!$data['next_run'] || $data['next_run'] == '0' || $data['enabled'] == '0') {
            $data['next_run'] = '-';
        }

        return $data;
    }

    protected function formatEnabled($rowId, $data)
    {
        if ($data['force_next_run'] ||
            (($data['status'] == '1' ||
              $data['status'] == '<span class="badge badge-info text-uppercase">Running...</span>') &&
             $data['enabled'] == '0')
        ) {
            $data['enabled'] = '<span class="badge badge-warning text-uppercase">Yes</span>';

            return $data;
        }

        if ($data['enabled'] == '0') {
            $data['enabled'] = '<span class="badge badge-secondary text-uppercase">No</span>';
        } else if ($data['enabled'] == '1') {
            $data['enabled'] = '<span class="badge badge-success text-uppercase">Yes</span>';
        }

        return $data;
    }

    protected function formatForceNextRun($rowId, $data)
    {
        if ($data['force_next_run'] == '1') {
            $data['force_next_run'] = '<span class="badge badge-success text-uppercase">Yes</span>';
        } else if (!$data['force_next_run'] || $data['force_next_run'] == '0') {
            $data['force_next_run'] = '<span class="badge badge-secondary text-uppercase">No</span>';
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

    public function forceNextRunAction()
    {
        if ($this->request->isPost()) {

            $this->tasks->forceNextRun($this->postData());

            $this->addResponse(
                $this->tasks->packagesData->responseMessage,
                $this->tasks->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}