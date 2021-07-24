<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersSchedules;

class Schedules extends BasePackage
{
    protected $modelToUse = BasepackagesWorkersSchedules::class;

    protected $packageName = 'schedules';

    public $schedules;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }


    public function addSchedule(array $data)
    {
        if (!isset($data['schedule'])) {
            $this->addResponse('Schedule parameter missing', 1);

            return false;
        }

        if ($data['type'] == 0) {
            $this->addResponse('Cannot add system schedule.', 1);

            return false;
        }

        $data = $this->createScheduleObject($data);

        if ($this->add($data)) {
            $this->addResponse('Added new schedule ' . $data['name']);
        } else {
            $this->addResponse('Error adding new schedule', 1);
        }
    }

    public function updateSchedule(array $data)
    {
        if (!isset($data['schedule'])) {
            $this->addResponse('Schedule parameter missing', 1);

            return false;
        }

        if ($data['type'] == 0) {
            $this->addResponse('Cannot edit system schedule.', 1);

            return false;
        }

        $data = $this->createScheduleObject($data);

        if ($this->update($data)) {
            $this->addResponse('Updated schedule ' . $data['name']);
        } else {
            $this->addResponse('Error adding schedule', 1);
        }
    }

    public function removeSchedule(array $data)
    {
        $schedule = $this->getById($data['id']);

        if ($schedule['type'] == 0) {
            $this->addResponse('Cannot delete system schedule.', 1);

            return false;
        }

        $assignedToTasks =
            $this->basepackages->workers->tasks->getByParams(
                [
                    'conditions'    => 'schedule_id = :sid:',
                    'bind'          =>
                        [
                            'sid'   => $schedule['id']
                        ]
                ]
            );

        if ($assignedToTasks && count($assignedToTasks) > 0) {
            $this->addResponse('Schedule assigned to task. Cannot remove schedule.', 1);

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->addResponse('Schedule removed');
        } else {
            $this->addResponse('Error removing schedule', 1);
        }

    }

    protected function createScheduleObject(array $data)
    {
        if (isset($data['id'])) {
            $objData['id'] = $data['id'];
        }
        $objData['name'] = $data['name'];
        $objData['description'] = $data['description'];
        $objData['type'] = $data['type'];//user(1) or system(0)

        if ($data['schedule'] === 'everyminute') {
            $objData['schedule']['type'] = $data['schedule'];
        } else if ($data['schedule'] === 'everyxminutes') {
            $objData['schedule']['type'] = $data['schedule'];
            $objData['schedule']['params']['minutes'] = $data['everyxminutes_minutes'];
        } else if ($data['schedule'] === 'hourly') {
            $objData['schedule']['type'] = $data['schedule'];
            $objData['schedule']['params']['hourly_minutes'] = $data['hourly_minutes'];
        } else if ($data['schedule'] === 'daily') {
            $objData['schedule']['type'] = $data['schedule'];
            $objData['schedule']['params']['daily_hours'] = $data['daily_hours'];
            $objData['schedule']['params']['daily_minutes'] = $data['daily_minutes'];
        } else if ($data['schedule'] === 'weekly') {
            $objData['schedule']['type'] = $data['schedule'];
            $data['weekly_days'] = Json::decode($data['weekly_days'], true);
            $data['weekly_days'] = $data['weekly_days']['data'];
            $objData['schedule']['params']['weekly_days'] = $data['weekly_days'];
            $objData['schedule']['params']['weekly_hours'] = $data['weekly_hours'];
            $objData['schedule']['params']['weekly_minutes'] = $data['weekly_minutes'];
        } else if ($data['schedule'] === 'monthly') {
            $objData['schedule']['type'] = $data['schedule'];
            $data['monthly_months'] = Json::decode($data['monthly_months'], true);
            $data['monthly_months'] = $data['monthly_months']['data'];
            $objData['schedule']['params']['monthly_months'] = $data['monthly_months'];
            $objData['schedule']['params']['monthly_day'] = $data['monthly_day'];
            $objData['schedule']['params']['monthly_hours'] = $data['monthly_hours'];
            $objData['schedule']['params']['monthly_minutes'] = $data['monthly_minutes'];
        }

        return $objData;
    }
}