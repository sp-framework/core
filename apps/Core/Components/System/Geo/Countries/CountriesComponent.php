<?php

namespace Apps\Core\Components\System\Geo\Countries;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class CountriesComponent extends BaseComponent
{
    use DynamicTable;

    protected $geoCountries;

    public function initialize()
    {
        $this->geoCountries = $this->basepackages->geoCountries->init();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $country = $this->basepackages->geoCountries->getById($this->getData()['id']);

                $this->view->country = $country;
            }

            if (!$this->view->country) {
                return $this->throwIdNotFound();
            }

            $this->view->pick('countries/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'view'      => 'system/geo/countries',
                ]
            ];

        $replaceColumns =
            [
                'installed'  =>
                    [
                        'html' =>
                            [
                                '0' => 'No',
                                '1' => 'Yes'
                            ]
                    ],
                'enabled'  =>
                    [
                        'html' =>
                            [
                                '0' => 'No',
                                '1' => 'Yes'
                            ]
                    ]
                ];

        $this->generateDTContent(
            $this->geoCountries,
            'system/geo/countries/view',
            null,
            ['name', 'capital', 'currency', 'installed', 'enabled'],
            true,
            ['name', 'capital', 'currency', 'installed', 'enabled'],
            $controlActions,
            null,
            $replaceColumns,
            'name',
        );

        $this->view->pick('countries/list');
    }

    /**
     * @api_acl(name=view)
     */
    public function apiViewAction()
    {
        $this->initialize();

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $geoCountry = $this->geoCountries->getById($this->getData()['id']);

                if (!$geoCountry) {
                    return $this->throwIdNotFound();
                }
            }

            $this->addResponse('Ok', 0, ['data' => $geoCountry]);

            return;
        }

        if ($this->request->isPost()) {
            $data =
                $this->generateDTContent(
                    $this->geoCountries,
                    null,
                    null,
                    ['name', 'capital', 'currency', 'installed', 'enabled']
                );

            $this->addResponse('Ok', 0, ['data' => $data ?? []]);

            return;
        }
    }

    public function installAction()
    {
        $this->requestIsPost();

        $this->geoCountries->installCountry($this->postData());

        $this->addResponse(
            $this->geoCountries->packagesData->responseMessage,
            $this->geoCountries->packagesData->responseCode,
            $this->geoCountries->packagesData->responseData ?? []
        );
    }

    public function uninstallAction()
    {
        //
    }

    public function searchCountryAction()
    {
        $this->requestIsPost();

        if ($this->postData()['search']) {
            $searchQuery = $this->postData()['search'];

            if (strlen($searchQuery) < 3) {
                return;
            }

            $countries = $this->geoCountries->searchCountries($searchQuery);

            $countries = msort($countries, 'id');

            $this->addResponse(
                $this->geoCountries->packagesData->responseMessage,
                $this->geoCountries->packagesData->responseCode,
                ['countries' => $countries] ?? []
            );
        } else {
            $this->addResponse('Search Query Missing', 1);
        }
    }
}