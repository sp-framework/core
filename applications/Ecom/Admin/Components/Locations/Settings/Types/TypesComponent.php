<?php

namespace Applications\Ecom\Admin\Components\Locations\Settings\Types;

use Applications\Ecom\Admin\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Ecom\Admin\Packages\Locations\Settings\Types\LocationsTypes;
use Phalcon\Mvc\View;
use System\Base\BaseComponent;

class TypesComponent extends BaseComponent
{
    use DynamicTable;

    protected $types;

    public function initialize()
    {
        $this->types = $this->usePackage(LocationsTypes::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $this->view->locationType = $this->types->getById($this->getData()['id']);
            }

            $this->view->responseCode = $this->types->packagesData->responseCode;

            $this->view->responseMessage = $this->types->packagesData->responseMessage;

            $this->view->pick('types/view');

            return;
        }

        $types = $this->types->init();

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'locations/settings/types',
                    'remove'    => 'locations/settings/types/remove'
                ]
            ];

        $this->generateDTContent(
            $types,
            'locations/settings/types/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            null,
            [],
            'name'
        );

        $this->view->pick('types/list');
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

            $this->types->addLocationsType($this->postData());

            $this->view->responseCode = $this->types->packagesData->responseCode;

            $this->view->responseMessage = $this->types->packagesData->responseMessage;

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

            $this->types->updateLocationsType($this->postData());

            $this->view->responseCode = $this->types->packagesData->responseCode;

            $this->view->responseMessage = $this->types->packagesData->responseMessage;

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

            $this->types->removeLocationsType($this->postData());

            $this->view->responseCode = $this->types->packagesData->responseCode;

            $this->view->responseMessage = $this->types->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}