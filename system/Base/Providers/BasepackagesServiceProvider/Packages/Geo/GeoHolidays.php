<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoHolidays as GeoHolidaysModel;

class GeoHolidays extends BasePackage
{
    protected $modelToUse = GeoHolidaysModel::class;

    protected $packageName = 'geoHolidays';

    public $geoHolidays;

    public function addHoliday($data)
    {
        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        $this->ffStore->setReadIndex(false);

        $data = $this->jsonData($data, true);

        try {
            \Carbon\Carbon::parse($data['date']);
        } catch (\throwable $e) {
            $this->addResponse('Unknown date format : ' . $data['date'], 1);

            return false;
        }

        if (!isset($data['is_national_holiday']) ||
            (isset($data['is_national_holiday']) &&
             $data['is_national_holiday'] == 0 &&
             $data['state_id'] === 0)
        ) {
            $this->addResponse('Please provide state information', 1);

            return false;
        }

        if (isset($data['tags']['data']) && count($data['tags']['data']) > 0) {
            foreach ($data['tags']['data'] as $oldTag) {
                if (!$this->basepackages->tags->checkTag(null, $oldTag)) {
                    $this->addResponse(
                        $this->basepackages->tags->packagesData->responseMessage,
                        $this->basepackages->tags->packagesData->responseCode
                    );

                    return false;
                }
            }
        }

        if (isset($data['tags']['newTags']) && count($data['tags']['newTags']) > 0) {
            foreach ($data['tags']['newTags'] as $newTag) {
                if (!$this->basepackages->tags->checkTag($newTag)) {
                    $this->addResponse(
                        $this->basepackages->tags->packagesData->responseMessage,
                        $this->basepackages->tags->packagesData->responseCode
                    );

                    return false;
                }
            }
        }

        $recurringDatesArr = [];

        if (isset($data['recurring_dates']) &&
            $data['recurring_dates'] !== ''
        ) {
            $data['recurring_dates'] = trim(trim($data['recurring_dates'], ','));

            if ($data['recurring_dates'] !== '') {
                $recurringDates = explode(',', $data['recurring_dates']);

                foreach ($recurringDates as $date) {
                    if ($date === '') {
                        continue;
                    }

                    try {
                        array_push($recurringDatesArr, (\Carbon\Carbon::parse($date))->toDateString());
                    } catch (\throwable $e) {
                        $this->addResponse('Unknown date format : ' . $date, 1);

                        return false;
                    }
                }
            }
        }

        unset($data['recurring_dates']);

        $dataArr = [$data];

        if (count($recurringDatesArr) > 0) {
            foreach ($recurringDatesArr as $recurringDate) {
                $recurringEntry = $data;
                $recurringEntry['date'] = $recurringDate;
                array_push($dataArr, $recurringEntry);
            }
        }

        foreach ($dataArr as $dataEntry) {
            if ($this->add($dataEntry)) {
                if (isset($data['tags']['data']) && count($data['tags']['data']) > 0) {
                    foreach ($data['tags']['data'] as $oldTag) {
                        if (!$this->basepackages->tags->updateTag(
                            ['id' => $oldTag, 'package_name' => 'GeoHolidays', 'package_row_id' => $this->packagesData->last['id']]
                        )) {
                            $this->addResponse(
                                $this->basepackages->tags->packagesData->responseMessage,
                                $this->basepackages->tags->packagesData->responseCode
                            );

                            return false;
                        }
                    }
                }

                if (isset($data['tags']['newTags']) && count($data['tags']['newTags']) > 0) {
                    foreach ($data['tags']['newTags'] as $newTag) {
                        if (!$this->basepackages->tags->addTag(
                            ['name' => $newTag, 'package_name' => 'GeoHolidays', 'package_row_id' => $this->packagesData->last['id']]
                        )) {
                            $this->addResponse(
                                $this->basepackages->tags->packagesData->responseMessage,
                                $this->basepackages->tags->packagesData->responseCode
                            );

                            return false;
                        }
                    }
                }
            } else {
                $this->addResponse('Error adding new holiday.', 1);
            }
        }
    }

    public function generateRecurringDates($data)
    {
        if (!isset($data['type']) ||
            isset($data['type']) && $data['type'] === ''
        ) {
            $this->addResponse('Please provide type', 1);

            return false;
        }

        if (!isset($data['start_date']) ||
            isset($data['start_date']) && $data['start_date'] === ''
        ) {
            $this->addResponse('Please provide start date', 1);

            return false;
        }

        if (!isset($data['end_date']) ||
            isset($data['end_date']) && $data['end_date'] === ''
        ) {
            $this->addResponse('Please provide end date', 1);

            return false;
        }

        try {
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        $type = $data['type'];

        $recurringDates = [];

        $recurr = 1;

        if ($type === 'yearly') {
            $recurr = (int) floor($startDate->diffInYears($endDate));
        } else if ($type === 'monthly') {
            $recurr = (int) floor($startDate->diffInMonths($endDate));
        } else if ($type === 'biweekly') {
            $recurr = (int) floor($startDate->diffInWeeks($endDate));
        } else if ($type === 'weekly') {
            $recurr = (int) floor($startDate->diffInWeeks($endDate));
        } else if ($type === 'xth') {
            if (!isset($data['xth']) ||
                isset($data['xth']) && $data['xth'] === ''
            ) {
                $this->addResponse('Please provide xth value', 1);

                return false;
            }

            if (!isset($data['weekly_day']) ||
                isset($data['weekly_day']) && $data['weekly_day'] === ''
            ) {
                $this->addResponse('Please provide weekly day', 1);

                return false;
            }

            $recurr = (int) floor($startDate->diffInMonths($endDate)) + 1;
        }

        if ($recurr > 0) {
            $counter = 1;

            for ($generateRecurring = 0; $generateRecurring < $recurr; $generateRecurring++) {
                if (($type === 'yearly' && $generateRecurring === 9) ||
                    ($type === 'monthly' && $generateRecurring === 23) ||
                    ($type === 'biweekly' && $generateRecurring === 25) ||
                    ($type === 'weekly' && $generateRecurring === 51)
                ) {
                    break;
                }

                if ($type === 'yearly') {
                    $recurringDate = (\Carbon\Carbon::parse($data['start_date']))->addYear($counter);
                } else if ($type === 'monthly') {
                    $recurringDate = (\Carbon\Carbon::parse($data['start_date']))->addMonth($counter);
                } else if ($type === 'biweekly') {
                    if ($generateRecurring === 0) {
                        $counter++;
                    }

                    $recurringDate = (\Carbon\Carbon::parse($data['start_date']))->addWeeks($counter);

                    $counter++;
                } else if ($type === 'weekly') {
                    $recurringDate = (\Carbon\Carbon::parse($data['start_date']))->addWeek($counter);
                } else if ($type === 'xth') {
                    if ($generateRecurring === 0) {
                        $startOfMonth = (\Carbon\Carbon::parse($data['start_date']))->startOfMonth();
                    } else {
                        $startOfMonth = (\Carbon\Carbon::parse($data['start_date']))->addMonth($counter - 1)->startOfMonth();
                    }

                    $startOfMonthMonth = $startOfMonth->month;

                    if ($startOfMonth->dayOfWeek == $data['weekly_day']) {
                        $weekDays = $this->basepackages->workers->schedules->getWeekdays();

                        if ($data['xth'] == '1') {
                            $week = $startOfMonth->startOfWeek(\Carbon\Carbon::{strtoupper($weekDays[$data['weekly_day']]['name'])});
                        } else {
                            $week = $startOfMonth->addWeek((int) $data['xth'] - 1)->startOfWeek(\Carbon\Carbon::{strtoupper($weekDays[$data['weekly_day']]['name'])});
                        }
                    } else {
                        if ($data['xth'] == '1') {
                            $week = $startOfMonth;
                        } else {
                            if ($startOfMonth->dayOfWeek === 0) {
                                if ($data['xth'] == '1') {
                                    $week = $startOfMonth;
                                } else {
                                    $week = $startOfMonth->addWeek((int) $data['xth'])->startOfWeek();
                                }
                            } else {
                                $week = $startOfMonth->addWeek((int) $data['xth'] - 1)->startOfWeek();
                            }
                        }
                    }

                    if ($week->dayOfWeek != $data['weekly_day']) {
                        while ($week->dayOfWeek != $data['weekly_day']) {
                            $week->addDay();
                        }
                    }

                    if ($startOfMonthMonth !== $startOfMonth->month) {
                        $counter++;

                        continue;
                    }

                    $recurringDate = $week;
                }

                if ($recurringDate->gt(\Carbon\Carbon::parse($data['end_date']))) {
                    break;
                }

                array_push($recurringDates, $recurringDate->toDateString());

                $counter++;
            }
        }

        $this->addResponse('Generated', 0, ['recurringDates' => $recurringDates]);

        return $recurringDates;
    }

    public function updateHoliday($data)
    {
        $data = $this->jsonData($data, true);

        if (isset($data['tags']['newTags']) && count($data['tags']['newTags']) > 0) {
            foreach ($data['tags']['newTags'] as $newTag) {
                if (!$this->basepackages->tags->checkTag($newTag)) {
                    $this->addResponse(
                        $this->basepackages->tags->packagesData->responseMessage,
                        $this->basepackages->tags->packagesData->responseCode
                    );

                    return false;
                }
            }
        }

        $holiday = $this->getById($data['id']);

        if (!$holiday) {
            $this->addResponse('Holiday with ID not found.', 1);

            return false;
        }

        $holiday = array_replace($holiday, $data);

        if ($this->update($holiday)) {
            $currentTags = $this->basepackages->tags->getTagsByPackageNameAndPackageRowId('GeoHolidays', $holiday['id']);

            if (count($currentTags) > 0) {
                foreach ($currentTags as $currentTag) {
                    if (isset($data['tags']['data']) && is_array($data['tags']['data'])) {
                        if (!in_array($currentTag['id'], $data['tags']['data'])) {
                            $key = array_search($data['id'], $currentTag['package_row_ids']);
                            unset($currentTag['package_row_ids'][$key]);

                            if (!$this->basepackages->tags->updateTag($currentTag)) {
                                $this->addResponse(
                                    $this->basepackages->tags->packagesData->responseMessage,
                                    $this->basepackages->tags->packagesData->responseCode
                                );

                                return false;
                            }
                        }
                    }
                }
            }

            if (isset($data['tags']['data']) && count($data['tags']['data']) > 0) {
                foreach ($data['tags']['data'] as $oldTag) {
                    if (!$this->basepackages->tags->updateTag(
                        ['id' => $oldTag, 'package_name' => 'GeoHolidays', 'package_row_id' => $this->packagesData->last['id']]
                    )) {
                        $this->addResponse(
                            $this->basepackages->tags->packagesData->responseMessage,
                            $this->basepackages->tags->packagesData->responseCode
                        );

                        return false;
                    }
                }
            }

            if (isset($data['tags']['newTags']) && count($data['tags']['newTags']) > 0) {
                foreach ($data['tags']['newTags'] as $newTag) {
                    if (!$this->basepackages->tags->addTag(
                        ['name' => $newTag, 'package_name' => 'GeoHolidays', 'package_row_id' => $this->packagesData->last['id']]
                    )) {
                        $this->addResponse(
                            $this->basepackages->tags->packagesData->responseMessage,
                            $this->basepackages->tags->packagesData->responseCode
                        );

                        return false;
                    }
                }
            }

            $this->addResponse('Updated ' . $holiday['name'] . ' holiday');
        } else {
            $this->addResponse('Error updating holiday.', 1);
        }
    }

    public function removeHoliday($id)
    {
        $holiday = $this->getById($id);

        if (!$holiday) {
            $this->addResponse('Holiday with ID not found.', 1);

            return false;
        }

        if ($this->remove($holiday['id'])) {
            $currentTags = $this->basepackages->tags->getTagsByPackageNameAndPackageRowId('GeoHolidays', $holiday['id']);

            if (count($currentTags) > 0) {
                foreach ($currentTags as $currentTag) {
                    $key = array_search($holiday['id'], $currentTag['package_row_ids']);
                    unset($currentTag['package_row_ids'][$key]);

                    if (!$this->basepackages->tags->updateTag($currentTag)) {
                        $this->addResponse(
                            $this->basepackages->tags->packagesData->responseMessage,
                            $this->basepackages->tags->packagesData->responseCode
                        );

                        return false;
                    }
                }
            }

            $this->addResponse('Removed ' . $holiday['name'] . ' holiday');
        } else {
            $this->addResponse('Error removing holiday.', 1);
        }
    }
}