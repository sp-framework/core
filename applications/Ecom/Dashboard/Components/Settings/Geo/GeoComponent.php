<?php

namespace Applications\Ecom\Dashboard\Components\Settings\Geo;

use System\Base\BaseComponent;

class GeoComponent extends BaseComponent
{
    public function initialize()
    {
        $this->basepackages->geoCities->init();

        $this->basepackages->geoStates->init();

        $this->basepackages->geoCountries->init();
    }

    public function searchCityAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 4) {
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

    public function searchStateAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 4) {
                    return;
                }

                $searchStates = $this->basepackages->geoStates->searchStates($searchQuery);

                if ($searchStates) {
                    $this->view->responseCode = $this->basepackages->geoStates->packagesData->responseCode;

                    $this->view->states = $this->basepackages->geoStates->packagesData->states;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }

    public function searchCountryAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 4) {
                    return;
                }

                $searchCountries = $this->basepackages->geoCountries->searchCountries($searchQuery);

                if ($searchCountries) {
                    $this->view->responseCode = $this->basepackages->geoCountries->packagesData->responseCode;

                    $this->view->countries = $this->basepackages->geoCountries->packagesData->countries;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}