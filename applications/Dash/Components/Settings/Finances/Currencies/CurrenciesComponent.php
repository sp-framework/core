<?php

namespace Applications\Dash\Components\Settings\Finances\Currencies;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class CurrenciesComponent extends BaseComponent
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
                $country =
                    $this->basepackages->geoCountries->getById($this->getData()['id']);

                $this->view->country = $country;
            } else {
                $this->view->country = [];
            }
            $this->view->pick('currencies/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'settings/finances/currencies',
                ]
            ];

        $replaceColumns =
            [
                'currency_enabled'  =>
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
            'settings/finances/currencies/view',
            null,
            ['name', 'currency', 'currency_symbol', 'currency_enabled'],
            true,
            ['name', 'currency', 'currency_symbol', 'currency_enabled'],
            $controlActions,
            null,
            $replaceColumns,
            'name',
        );

        $this->view->pick('currencies/list');
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

            $this->geoCountries->updateCountry($this->postData());

            $this->view->responseCode = $this->geoCountries->packagesData->responseCode;

            $this->view->responseMessage = 'Updated ' . $this->postData()['name'] . ' currency';

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}