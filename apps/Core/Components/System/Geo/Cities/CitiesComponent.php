<?php

namespace Apps\Core\Components\System\Geo\Cities;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
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
                $city = $this->basepackages->geoCities->getById($this->getData()['id']);

                if (!$city) {
                    return $this->throwIdNotFound();
                }

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
        $states = [];

        if ($countriesArr) {
            foreach ($countriesArr as $countriesKey => $country) {
                $countries[$country['id']] = $country['name'] . ' (' . $country['id'] . ')';
            }
        }

        if ($statesArr) {
            foreach ($statesArr as $statesKey => $state) {
                $states[$state['id']] = $state['name'] . ' (' . $state['id'] . ')';
            }
        }

        $controlActions =
            [
                // 'includeQ'              => true,
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/geo/cities',
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
            'system/geo/cities/view',
            null,
            ['name', 'longitude', 'latitude', 'postcode', 'state_id', 'country_id'],
            true,
            ['name', 'longitude', 'latitude', 'postcode', 'state_id', 'country_id'],
            $controlActions,
            ['state_id'=>'State','postcode'=>'post code', 'country_id'=>'country'],
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
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->geoCities->addCity($this->postData());

            $this->addResponse(
                $this->geoCities->packagesData->responseMessage,
                $this->geoCities->packagesData->responseCode
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

            $this->geoCities->updateCity($this->postData());

            $this->addResponse(
                $this->geoCities->packagesData->responseMessage,
                $this->geoCities->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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
                $this->addResponse('Search Query Missing', 1);
            }
        }
    }
}