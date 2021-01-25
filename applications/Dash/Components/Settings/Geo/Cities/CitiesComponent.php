<?php

namespace Applications\Dash\Components\Settings\Geo\Cities;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class CitiesComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoCities;

    public function initialize()
    {
        $this->geoCities = $this->basepackages->geoCities->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $countriesArr = $this->basepackages->geoCountries->getAll()->geoCountries;
        $statesArr = $this->basepackages->geoStates->getAll()->geoStates;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $city =
                    $this->basepackages->geoCities->getById($this->getData()['id']);

                $this->view->city = $city;
            } else {
                $this->view->city = [];
            }
            $this->view->pick('cities/view');

            $this->view->countries = $countriesArr;
            $this->view->states = $statesArr;
            return;
        }

        $countries = [];
        foreach ($countriesArr as $countriesKey => $country) {
            $countries[$country['id']] = $country['name'] . ' (' . $country['id'] . ')';
        }

        $states = [];
        foreach ($statesArr as $statesKey => $state) {
            $states[$state['id']] = $state['name'] . ' (' . $state['id'] . ')';
        }
        $controlActions =
            [
                // 'includeQ'              => true,
                'actionsToEnable'       =>
                [
                    'edit'      => 'settings/geo/cities',
                ]
            ];

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'country_id'  =>
                        [
                            'html' => $countries
                        ],
                    'state_id'  =>
                        [
                            'html' => $states
                        ]
                ];
        } else {
            $replaceColumns = [];
        }

        $this->generateDTContent(
            $this->geoCities,
            'settings/geo/cities/view',
            null,
            ['name', 'longitude', 'latitude', 'state_id', 'country_id'],
            true,
            ['name', 'longitude', 'latitude', 'state_id', 'country_id'],
            $controlActions,
            null,
            $replaceColumns,
            'name',
            // $dtAdditionControlButtons
        );

        $this->view->pick('cities/list');
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

            $this->geoCities->updateCity($this->postData());

            $this->view->responseCode = $this->geoCities->packagesData->responseCode;

            $this->view->responseMessage = $this->geoCities->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchCityAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchCities = $this->basepackages->geoCities->searchCities($searchQuery);

                if ($searchCities) {
                    $this->view->responseCode = $this->basepackages->geoCities->packagesData->responseCode;

                    $this->view->cities = $this->basepackages->geoCities->packagesData->cities;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}