<?php

namespace Apps\Dash\Components\System\Workers\Schedules;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class SchedulesComponent extends BaseComponent
{
    use DynamicTable;

    protected $schedules;

    public function initialize()
    {
        $this->schedules = $this->basepackages->workers->schedules;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $schedule = $this->schedules->getById($this->getData()['id']);

                $schedule['schedule'] = Json::decode($schedule['schedule'], true);

                $this->view->schedule = $schedule;
            }
            $this->view->pick('schedules/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/workers/schedules',
                    'remove'    => 'system/workers/schedules/remove'
                ]
            ];

        $this->generateDTContent(
            $this->schedules,
            'system/workers/schedules/view',
            null,
            ['name'],
            false,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('schedules/list');
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

            $this->schedules->addSchedule($this->postData());

            $this->addResponse(
                $this->schedules->packagesData->responseMessage,
                $this->schedules->packagesData->responseCode
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

            $this->schedules->updateSchedule($this->postData());

            $this->addResponse(
                $this->schedules->packagesData->responseMessage,
                $this->schedules->packagesData->responseCode
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

            $this->schedules->removeSchedule($this->postData());

            $this->addResponse(
                $this->schedules->packagesData->responseMessage,
                $this->schedules->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}