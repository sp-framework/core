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
            [
                'schedule_id'   =>
                    [
                        'html'  => $schedulesArr
                    ],
                'enabled'       =>
                    [
                        'html'  =>
                            [
                                '0' => '<span class="badge badge-secondary">No</span>',
                                '1' => '<span class="badge badge-success">Yes</span>'
                            ]
                    ],
                'status'       =>
                    [
                        'html'  =>
                            [
                                '0' => '-',
                                '1' => '<span class="badge badge-secondary">Scheduled</span>',
                                '2' => '<span class="badge badge-info">Running...</span>',
                                '3' => '<span class="badge badge-danger">Error!</span>'
                            ]
                    ],
                'previous_run'  =>
                    [
                        'html'  =>
                            [
                                '0' => '-'
                            ]
                    ],
                'next_run'  =>
                    [
                        'html'  =>
                            [
                                '0' => '-'
                            ]
                    ]
            ];

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