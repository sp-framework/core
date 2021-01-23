<?php

namespace Applications\Dash\Components\Settings\Geo\States;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class StatesComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoStates;

    public function initialize()
    {
        $this->geoStates = $this->basepackages->geoStates->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $countriesArr = $this->basepackages->geoCountries->getAll()->geocountries;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $state =
                    $this->basepackages->geoStates->getById($this->getData()['id']);

                $this->view->state = $state;
            } else {
                $this->view->state = [];
            }

            $this->view->countries = $countriesArr;

            $this->view->pick('states/view');

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
                    'edit'      => 'settings/geo/states',
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
            $this->geoStates,
            'settings/geo/states/view',
            null,
            ['name', 'state_code', 'country_id'],
            true,
            ['name', 'state_code', 'country_id'],
            $controlActions,
            null,
            $replaceColumns,
            'name',
            // $dtAdditionControlButtons
        );

        $this->view->pick('states/list');
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

            $this->geoStates->updateState($this->postData());

            $this->view->responseCode = $this->geoStates->packagesData->responseCode;

            $this->view->responseMessage = $this->geoStates->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchStateAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
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
}