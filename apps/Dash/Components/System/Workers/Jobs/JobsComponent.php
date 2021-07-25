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

                $job['job'] = Json::decode($job['job'], true);

                $this->view->job = $job;
            }
            $this->view->pick('jobs/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/workers/jobs',
                    'remove'    => 'system/workers/jobs/remove'
                ]
            ];

        $this->generateDTContent(
            $this->jobs,
            'system/workers/jobs/view',
            null,
            ['name', 'task_id', 'run_on', 'status', 'execution_time'],
            false,
            ['name', 'task_id', 'run_on', 'status', 'execution_time'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('jobs/list');
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