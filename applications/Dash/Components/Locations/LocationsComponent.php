<?php

namespace Applications\Dash\Components\Locations;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Dash\Packages\Locations\Locations;
use System\Base\BaseComponent;

class LocationsComponent extends BaseComponent
{
    use DynamicTable;

    protected $locations;

    public function initialize()
    {
        $this->locations = $this->usePackage(Locations::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $location = $this->locations->getById($this->getData()['id']);

                $address = $this->basepackages->addressbook->getById($location['address_id']);

                unset($address['id']);
                unset($address['name']);

                $location = array_merge($location, $address);

                $this->view->location = $location;
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->isEnabled()) {
                $this->view->geo = true;
            } else {
                $this->view->geo = false;
            }

            $this->view->responseCode = $this->locations->packagesData->responseCode;

            $this->view->responseMessage = $this->locations->packagesData->responseMessage;

            $this->view->pick('locations/view');

            return;
        }

        $locations = $this->locations->init();

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'inbound_shipping'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ],
                    'outbound_shipping'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ],
                    'can_stock'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ]
                ];
        } else {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'locations',
                    'remove'    => 'locations/remove'
                ]
            ];

        $this->generateDTContent(
            $locations,
            'locations/view',
            null,
            ['name', 'inbound_shipping', 'outbound_shipping', 'can_stock'],
            true,
            ['name', 'inbound_shipping', 'outbound_shipping', 'can_stock'],
            $controlActions,
            null,
            $replaceColumns,
            'name'
        );

        $this->view->pick('locations/list');
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

            $this->locations->addLocation($this->postData());

            $this->view->responseCode = $this->locations->packagesData->responseCode;

            $this->view->responseMessage = $this->locations->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
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

            $this->locations->updateLocation($this->postData());

            $this->view->responseCode = $this->locations->packagesData->responseCode;

            $this->view->responseMessage = $this->locations->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->locations->removeLocation($this->postData());

            $this->view->responseCode = $this->locations->packagesData->responseCode;

            $this->view->responseMessage = $this->locations->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function getLocationByIdAction()
    {
        if ($this->request->isPost()) {

            $this->locations->getLocationById($this->postData());

            $this->view->locationAddress = $this->locations->packagesData->locationAddress;

            $this->view->responseCode = $this->locations->packagesData->responseCode;

            $this->view->responseMessage = $this->locations->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }

    }
}