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
        if (isset($this->getData()['id'])) {
            $availableFunctions = $this->tasks->getAllFunctions();

            if ($this->getData()['id'] != 0) {
                $tasks = $this->tasks->getById($this->getData()['id']);

                $this->view->tasks = $tasks;
            }
            $this->view->pick('tasks/view');

            return;
        }

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
            ['name', 'schedule_id', 'priority', 'enabled', 'status'],
            true,
            ['name', 'schedule_id', 'priority', 'enabled', 'status'],
            $controlActions,
            ['schedule_id' => 'schedule'],
            null,
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