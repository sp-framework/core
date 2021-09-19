<?php

namespace Apps\Dash\Components\System\Geo\Timezones;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
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
        $countriesArr = $this->basepackages->geoCountries->getAll()->geoCountries;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $timezone = $this->basepackages->geoTimezones->getById($this->getData()['id']);

                if (!$timezone) {
                    return $this->throwIdNotFound();
                }

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
                    'edit'      => 'system/geo/timezones',
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
            'system/geo/timezones/view',
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
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->geoTimezones->addTimezone($this->postData());

            $this->addResponse(
                $this->geoTimezones->packagesData->responseMessage,
                $this->geoTimezones->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
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

            $this->addResponse(
                $this->geoStates->packagesData->responseMessage,
                $this->geoStates->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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
                $this->addResponse('Search Query Missing', 1);
            }
        }
    }
}