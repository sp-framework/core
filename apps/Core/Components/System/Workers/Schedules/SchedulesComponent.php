<?php

namespace Apps\Core\Components\System\Workers\Schedules;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
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
            $this->view->weekdays = $this->schedules->getWeekdays();

            $this->view->months = $this->schedules->getMonths();

            if ($this->getData()['id'] != 0) {
                $schedule = $this->schedules->getById($this->getData()['id']);

                if (!$schedule) {
                    return $this->throwIdNotFound();
                }

                $schedule['schedule'] = $this->helper->decode($schedule['schedule'], true);

                if ($schedule['schedule']['type'] === 'everyxseconds') {
                    $schedule['schedule']['params']['seconds'] = implode(',', $schedule['schedule']['params']['seconds']);
                }

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