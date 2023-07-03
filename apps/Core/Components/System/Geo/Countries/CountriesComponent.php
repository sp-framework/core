<?php

namespace Apps\Core\Components\System\Geo\Countries;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoExtractData;

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

                if (!$country) {
                    return $this->throwIdNotFound();
                }

                $this->view->country = $country;
            } else {
                $this->view->country = [];
            }
            $this->view->pick('countries/view');

            return;
        }

        $controlActions =
            [
                // 'includeQ'              => true,
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/geo/countries',
                ]
            ];

        // $dtAdditionControlButtons =
        //     [
        //         'includeId'  => true,
        //         // 'includeQ'   => true, //Only true when not adding /q/ in link below.
        //         'buttons'    => [
        //             'states'    => [
        //                 'title'     => 'states',
        //                 'icon'      => 'map-marked',
        //                 'link'      => $this->links->url('system/geo/q/type/states')
        //             ],
        //             'cities'    => [
        //                 'title'     => 'cities',
        //                 'icon'      => 'map-marked-alt',
        //                 'link'      => $this->links->url('system/geo/q/type/cities')
        //             ]
        //         ]
        //     ];

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
            // $dtAdditionControlButtons
        );

        $this->view->pick('countries/list');
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

            $this->geoCountries->addCountry($this->postData());

            $this->addResponse(
                $this->geoCountries->packagesData->responseMessage,
                $this->geoCountries->packagesData->responseCode
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

            $this->geoCountries->updateCountry($this->postData());

            $this->addResponse(
                $this->geoCountries->packagesData->responseMessage,
                $this->geoCountries->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function installAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->geoCountries->installCountry($this->postData());

            $this->addResponse(
                $this->geoCountries->packagesData->responseMessage,
                $this->geoCountries->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function uninstallAction()
    {
        //
    }

    public function searchCountryAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchCountries = $this->geoCountries->searchCountries($searchQuery);

                if ($searchCountries) {
                    $this->view->responseCode = $this->geoCountries->packagesData->responseCode;

                    $countries = $this->geoCountries->packagesData->countries;

                    $countries = msort($countries, 'id');

                    $this->view->countries = $countries;
                }
            } else {
                $this->addResponse('Search Query Missing', 1);
            }
        }
    }
}