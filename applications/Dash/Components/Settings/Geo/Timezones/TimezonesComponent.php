<?php

namespace Applications\Dash\Components\Settings\Geo\Timezones;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class TimezonesComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoTimezones;

    public function initialize()
    {
        $this->geoTimezones = $this->basepackages->geoTimezones->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $countriesArr = $this->basepackages->geoCountries->getAll()->geocountries;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $timezone =
                    $this->basepackages->geoTimezones->getById($this->getData()['id']);

                $this->view->timezone = $timezone;
            } else {
                $this->view->timezone = [];
            }

            $this->view->countries = $countriesArr;

            $this->view->pick('timezones/view');

            return;
        }

        $countries = [];

        foreach ($countriesArr as $countriesKey => $country) {
            $countries[$country['id']] = $country['name'] . ' (' . $country['id'] . ')';
        }
        $controlActions =
            [
                // 'includeQ'              => true,
                'actionsToEnable'       =>
                [
                    'edit'      => 'settings/geo/timezones',
                ]
            ];

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'country_id'  =>
                        [
                            'html' => $countries
                        ]
                    ];

        } else {
            $replaceColumns = [];
        }

        $this->generateDTContent(
            $this->geoTimezones,
            'settings/geo/timezones/view',
            null,
            ['zone_name', 'tz_name', 'gmt_offset', 'gmt_offset_name', 'abbreviation', 'country_id'],
            true,
            ['zone_name', 'tz_name', 'gmt_offset', 'gmt_offset_name', 'abbreviation', 'country_id'],
            $controlActions,
            ['gmt_offset'=>'gmt offset (secs)', 'country_id'=>'country','tz_name'=>'time zone name'],
            $replaceColumns,
            'zone_name',
            // $dtAdditionControlButtons
        );

        $this->view->pick('timezones/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        //
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->geoTimezones->updateTimezone($this->postData());

            $this->view->responseCode = $this->geoTimezones->packagesData->responseCode;

            $this->view->responseMessage = $this->geoTimezones->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchTimezonesAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchTimezones = $this->basepackages->geoTimezones->searchTimezones($searchQuery);

                if ($searchTimezones) {
                    $this->view->responseCode = $this->basepackages->geoTimezones->packagesData->responseCode;

                    $this->view->timezones = $this->basepackages->geoTimezones->packagesData->timezones;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}