<?php

namespace Applications\Ecom\Admin\Components\Geo;

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

    // *
    //  * @acl(name=view)
    //
    // public function viewAction()
    // {
    //     if (isset($this->getData()['id'])) {
    //         if ($this->getData()['id'] != 0) {
    //             $domain = $this->basepackages->domains->generateViewData($this->getData()['id']);
    //         } else {
    //             $domain = $this->basepackages->domains->generateViewData();
    //         }
    //         if ($domain) {
    //             $this->view->domain = $this->basepackages->domains->packagesData->domain;
    //         }
    //         $this->view->emailservices = $this->basepackages->domains->packagesData->emailservices;

    //         $this->view->applications = $this->basepackages->domains->packagesData->applications;

    //         $this->view->pick('domains/view');

    //         return;
    //     }

    //     $domains = $this->basepackages->domains->init();

    //     $controlActions =
    //         [
    //             // 'disableActionsForIds'  => [1],
    //             'actionsToEnable'       =>
    //             [
    //                 'edit'      => 'domains',
    //                 'remove'    => 'domains/remove'
    //             ]
    //         ];

    //     $this->generateDTContent(
    //         $domains,
    //         'domains/view',
    //         null,
    //         ['name'],
    //         false,
    //         ['name'],
    //         $controlActions,
    //         null,
    //         null,
    //         'name'
    //     );

    //     $this->view->pick('domains/list');
    // }

    // /**
    //  * @acl(name="add")
    //  */
    // public function addAction()
    // {
    //     if ($this->request->isPost()) {
    //         if (!$this->checkCSRF()) {
    //             return;
    //         }
    //         $this->basepackages->domains->addDomain($this->postData());

    //         $this->view->responseCode = $this->basepackages->domains->packagesData->responseCode;

    //         $this->view->responseMessage = $this->basepackages->domains->packagesData->responseMessage;

    //     } else {
    //         $this->view->responseCode = 1;

    //         $this->view->responseMessage = 'Method Not Allowed';
    //     }
    // }

    // /**
    //  * @acl(name="update")
    //  */
    // public function updateAction()
    // {
    //     if ($this->request->isPost()) {
    //         if (!$this->checkCSRF()) {
    //             return;
    //         }
    //         $this->basepackages->domains->updateDomain($this->postData());

    //         $this->view->responseCode = $this->basepackages->domains->packagesData->responseCode;

    //         $this->view->responseMessage = $this->basepackages->domains->packagesData->responseMessage;

    //     } else {
    //         $this->view->responseCode = 1;

    //         $this->view->responseMessage = 'Method Not Allowed';
    //     }
    // }

    // /**
    //  * @acl(name="remove")
    //  */
    // public function removeAction()
    // {
    //     if ($this->request->isPost()) {

    //         $this->basepackages->domains->removeDomain($this->postData());

    //         $this->view->responseCode = $this->basepackages->domains->packagesData->responseCode;

    //         $this->view->responseMessage = $this->basepackages->domains->packagesData->responseMessage;

    //     } else {
    //         $this->view->responseCode = 1;

    //         $this->view->responseMessage = 'Method Not Allowed';
    //     }
    // }
}