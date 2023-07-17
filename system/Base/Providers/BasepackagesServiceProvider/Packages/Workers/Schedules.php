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

        if (isset($data['type']) && $data['type'] == 0) {
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

        if (isset($data['type']) && $data['type'] == 0) {
            $this->addResponse('Cannot edit system schedule.', 1);

            return false;
        }

        $data = $this->createScheduleObject($data);

        if ($this->update($data)) {
            $this->addResponse('Updated schedule ' . $data['name']);
        } else {
            $this->addResponse('Error updating schedule', 1);
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
        } else if ($data['schedule'] === 'everyxminutesbetween') {
            $objData['schedule']['type'] = $data['schedule'];
            $objData['schedule']['params']['minutes'] = $data['everyxminutesbetween_minutes'];
            $objData['schedule']['params']['start'] = $data['everyxminutesbetween_start'];
            $objData['schedule']['params']['end'] = $data['everyxminutesbetween_end'];
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

    public function getSchedulesSchedule($id)
    {
        if ($this->config->databasetype === 'db') {
            $filter =
                $this->model->filter(
                    function($function) use ($id) {
                        $function = $function->toArray();

                        if ($function['id'] == $id) {
                            $function['schedule'] = Json::decode($function['schedule'], true);

                            return $function['schedule'];
                        }
                    }
                );

            return $filter[0];
        } else {
            if (!$this->{$this->packageName}) {
                $this->init();
            }

            foreach ($this->schedules as $key => $function) {
                if ($function['id'] == $id) {

                    if (is_string($function['schedule'])) {
                        $function['schedule'] = Json::decode($function['schedule'], true);
                    }

                    return $function['schedule'];
                }
            }
        }

        return false;
    }

    public function getWeekdays()
    {
        return
            [
                '0'                 =>
                    [
                        'id'        => '0',
                        'name'      => 'Sunday'
                    ],
                '1'                 =>
                    [
                        'id'        => '1',
                        'name'      => 'Monday'
                    ],
                '2'                 =>
                    [
                        'id'        => '2',
                        'name'      => 'Tuesday'
                    ],
                '3'                 =>
                    [
                        'id'        => '3',
                        'name'      => 'Wednesday'
                    ],
                '4'                 =>
                    [
                        'id'        => '4',
                        'name'      => 'Thursday'
                    ],
                '5'                 =>
                    [
                        'id'        => '5',
                        'name'      => 'Friday'
                    ],
                '6'                 =>
                    [
                        'id'        => '6',
                        'name'      => 'Saturday'
                    ]
            ];
    }

    public function getMonths()
    {
        return
            [
                '1'                 =>
                    [
                        'id'        => '1',
                        'name'      => 'January'
                    ],
                '2'                 =>
                    [
                        'id'        => '2',
                        'name'      => 'February'
                    ],
                '3'                 =>
                    [
                        'id'        => '3',
                        'name'      => 'March'
                    ],
                '4'                 =>
                    [
                        'id'        => '4',
                        'name'      => 'April'
                    ],
                '5'                 =>
                    [
                        'id'        => '5',
                        'name'      => 'May'
                    ],
                '6'                 =>
                    [
                        'id'        => '6',
                        'name'      => 'June'
                    ],
                '7'                 =>
                    [
                        'id'        => '7',
                        'name'      => 'July'
                    ],
                '8'                 =>
                    [
                        'id'        => '8',
                        'name'      => 'August'
                    ],
                '9'                 =>
                    [
                        'id'        => '9',
                        'name'      => 'September'
                    ],
                '10'                =>
                    [
                        'id'        => '10',
                        'name'      => 'October'
                    ],
                '11'                =>
                    [
                        'id'        => '11',
                        'name'      => 'November'
                    ],
                '12'                =>
                    [
                        'id'        => '12',
                        'name'      => 'December'
                    ]
            ];
    }
}