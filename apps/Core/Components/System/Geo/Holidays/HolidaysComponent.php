<?php

namespace Apps\Core\Components\System\Geo\Holidays;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class HolidaysComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoHolidays;

    public function initialize()
    {
        $this->geoHolidays = $this->basepackages->geoHolidays->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $enabledCountries = $this->basepackages->geoCountries->isEnabled(null, true);

        $enabledStates = [];

        if ($enabledCountries && count($enabledCountries) > 0) {
            $enabledCountries = msort($enabledCountries, 'name');

            foreach ($enabledCountries as $enabledCountry) {
                $states = $this->basepackages->geoStates->searchStatesByCountryId($enabledCountry['id']);

                $states = msort($states, 'name');

                if ($states && count($states) > 0) {
                    $enabledStates[$enabledCountry['id']] = $states;
                }
            }
        }

        if (isset($this->getData()['id'])) {
            $holidayTags = [];

            if ($this->getData()['id'] != 0) {
                $holiday = $this->basepackages->geoHolidays->getById($this->getData()['id']);

                $this->view->holiday = $holiday;

                if (!$this->view->holiday) {
                    return $this->throwIdNotFound();
                }

                $holidayTagsArr = $this->basepackages->tags->getTagsByPackageNameAndPackageRowId('GeoHolidays', $holiday['id']);

                if (count($holidayTagsArr) > 0) {
                    foreach ($holidayTagsArr as $tag) {
                        array_push($holidayTags, $tag['id']);
                    }
                }
            } else {
                $this->view->weekdays = $this->basepackages->workers->schedules->getWeekdays();
            }

            $this->view->countries = $enabledCountries;

            $this->view->states = $enabledStates;

            $this->view->holidayTags = $holidayTags;

            $this->view->tags = $this->basepackages->tags->getTagsByPackageName('GeoHolidays');

            $this->view->pick('holidays/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/geo/holidays',
                    'remove'    => 'system/geo/holidays/remove',
                ]
            ];

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    $states = [];

                    foreach ($dataArr as &$data) {
                        if ($data['is_national_holiday'] == '1') {
                            $data['is_national_holiday'] = 'Yes';
                            $data['state_id'] = '-';
                        } else {
                            $data['is_national_holiday'] = 'No';

                            if (isset($states[$data['state_id']])) {
                                $state = $states[$data['state_id']];
                            } else {
                                $state = $this->basepackages->geoStates->getById($data['state_id']);
                            }

                            if ($state) {
                                if (!isset($states[$state['id']])) {
                                    $states[$state['id']] = $state;
                                }

                                $data['state_id'] = $state['name'];
                            } else {
                                $data['state_id'] = '-';
                            }
                        }
                    }
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->geoHolidays,
            'system/geo/holidays/view',
            null,
            ['name', 'date', 'is_national_holiday', 'state_id'],
            true,
            ['name', 'date', 'is_national_holiday', 'state_id'],
            $controlActions,
            ['state_id' => 'State'],
            $replaceColumns,
            'name'
        );

        $this->view->pick('holidays/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->geoHolidays->addHoliday($this->postData());

        $this->addResponse(
            $this->geoHolidays->packagesData->responseMessage,
            $this->geoHolidays->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->geoHolidays->updateHoliday($this->postData());

        $this->addResponse(
            $this->geoHolidays->packagesData->responseMessage,
            $this->geoHolidays->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->geoHolidays->removeHoliday($this->postData()['id']);

        $this->addResponse(
            $this->geoHolidays->packagesData->responseMessage,
            $this->geoHolidays->packagesData->responseCode
        );
    }

    public function generateRecurringDatesAction()
    {
        $this->requestIsPost();

        $this->geoHolidays->generateRecurringDates($this->postData());

        $this->addResponse(
            $this->geoHolidays->packagesData->responseMessage,
            $this->geoHolidays->packagesData->responseCode,
            $this->geoHolidays->packagesData->responseData ?? []
        );
    }
}