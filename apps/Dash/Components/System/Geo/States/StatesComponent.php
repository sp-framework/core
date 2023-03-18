<?php

namespace Apps\Dash\Components\System\Geo\States;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
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
        $countriesArr = $this->basepackages->geoCountries->getAll()->geoCountries;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $state = $this->basepackages->geoStates->getById($this->getData()['id']);

                if (!$state) {
                    return $this->throwIdNotFound();
                }

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
                    'edit'      => 'system/geo/states',
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
            'system/geo/states/view',
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
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->geoStates->addState($this->postData());

            $this->addResponse(
                $this->geoStates->packagesData->responseMessage,
                $this->geoStates->packagesData->responseCode
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

            $this->geoStates->updateState($this->postData());

            $this->addResponse(
                $this->geoStates->packagesData->responseMessage,
                $this->geoStates->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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
                $this->addResponse('Search Query Missing', 1);
            }
        }
    }

    public function removeAction()
    {
        return;
    }
}