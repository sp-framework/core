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
        if (isset($this->getData()['id'])) {
            $enabledCountries = $this->basepackages->geoCountries->isEnabled(true);

            $enabledStates = [];

            if ($enabledCountries && count($enabledCountries) > 0) {
                foreach ($enabledCountries as $enabledCountry) {
                    $states = $this->basepackages->geoStates->searchStatesByCountryId($enabledCountry['id']);

                    if ($states && count($states) > 0) {
                        $enabledStates = array_merge($enabledStates, $states);
                    }
                }
            }

            $this->view->states = $enabledStates;

            $this->view->tags = $this->basepackages->tags->getTagsByPackageName('GeoHolidays');

            $holidayTags = [];

            if ($this->getData()['id'] != 0) {
                $holiday = $this->basepackages->geoHolidays->getById($this->getData()['id']);

                $holiday['date'] = $holiday['year'] . '-' . $holiday['month'] . '-' . $holiday['date'];

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
            }

            $this->view->holidayTags = $holidayTags;

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

        $this->generateDTContent(
            $this->geoHolidays,
            'system/geo/holidays/view',
            null,
            ['name', 'date', 'is_national_holiday'],
            true,
            ['name', 'date', 'is_national_holiday'],
            $controlActions,
            [],
            null,
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