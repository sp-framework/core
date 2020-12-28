<?php

namespace Applications\Ecom\Admin\Components\Locations;

use Applications\Ecom\Common\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Ecom\Common\Packages\Locations\Locations;
use System\Base\BaseComponent;

class LocationsComponent extends BaseComponent
{
    use DynamicTable;

    protected $locations;

    public function initialize()
    {
        $this->locations = $this->usePackage(Locations::class);
        $this->locationsTypes = $this->usePackage(LocationsTypes::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $this->view->location = $this->locations->getById($this->getData()['id']);
            }

            $this->view->locationsTypes = $this->locationsTypes->getAll()->locationstypes;

            $this->view->accounts = $this->accounts->getAll()->accounts;

            $this->view->responseCode = $this->locations->packagesData->responseCode;

            $this->view->responseMessage = $this->locations->packagesData->responseMessage;

            $this->view->pick('locations/view');

            return;
        }

        $locations = $this->locations->init();

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'type'   => ['html'  =>
                        [
                            '1' => 'Shop',
                            '2' => 'Warehouse',
                            '3' => 'Office',
                            '4' => 'Home Office',
                            '5' => 'Storage',
                            '6' => 'Show Grounds'
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
            ['name', 'type'],
            true,
            ['name', 'type'],
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
}