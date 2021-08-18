<?php

namespace Apps\Dash\Components\Business\Locations;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Entities\Entities;
use Apps\Dash\Packages\Business\Locations\Locations;
use Apps\Dash\Packages\Hrms\Employees\Employees;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class LocationsComponent extends BaseComponent
{
    use DynamicTable;

    protected $locations;

    public function initialize()
    {
        $this->locations = $this->usePackage(Locations::class);

        $this->entities = $this->usePackage(Entities::class);

        $this->employees = $this->usePackage(Employees::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $location = $this->locations->getById($this->getData()['id']);

                $location['activityLogs'] = $this->locations->getActivityLogs($this->getData()['id']);

                $address = $this->basepackages->addressbook->getById($location['address_id']);

                unset($address['id']);
                unset($address['name']);

                $location = array_merge($location, $address);

                if ($location['employee_ids'] && $location['employee_ids'] !== '') {
                    $location['employee_ids'] = Json::decode($location['employee_ids'], true);

                    foreach ($location['employee_ids'] as $employeeKey => $employee) {
                        if ($this->employees->searchById($employee)) {
                            $employeeArr = $this->employees->packagesData->employee;
                        }

                        $location['employee_ids'][$employeeKey] = $employeeArr;
                    }
                }

                $this->view->location = $location;
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->isEnabled()) {
                $this->view->geo = true;
            } else {
                $this->view->geo = false;
            }

            $this->view->entities = $this->entities->init()->getAll()->entities;

            $this->view->pick('locations/view');

            return;
        }

        $locations = $this->locations->init();

        if ($this->request->isPost()) {
            $entitiesArr = $this->entities->init()->getAll()->entities;

            if ($entitiesArr) {
                foreach ($entitiesArr as $entityKey => $entity) {
                    $entities[$entity['id']] = $entity['business_name'];
                }
            } else {
                $entities = [];
            }

            $replaceColumns =
                [
                    'entity_id'         => [
                        'html'  => $entities
                    ],
                    'inbound_shipping'   => [
                        'html'  =>
                            [
                                '0' => '<span class="badge badge-danger">No</span>',
                                '1' => '<span class="badge badge-success">Yes</span>'
                            ]
                    ],
                    'outbound_shipping'   => [
                        'html'  =>
                            [
                                '0' => '<span class="badge badge-danger">No</span>',
                                '1' => '<span class="badge badge-success">Yes</span>'
                            ]
                    ],
                    'can_stock'   => [
                        'html'  =>
                            [
                                '0' => '<span class="badge badge-danger">No</span>',
                                '1' => '<span class="badge badge-success">Yes</span>'
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
                    'edit'      => 'business/locations',
                    'remove'    => 'business/locations/remove'
                ]
            ];

        $this->generateDTContent(
            $locations,
            'business/locations/view',
            null,
            ['name', 'entity_id', 'inbound_shipping', 'outbound_shipping', 'can_stock'],
            true,
            ['name', 'entity_id', 'inbound_shipping', 'outbound_shipping', 'can_stock'],
            $controlActions,
            ['entity_id'=>'entity'],
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

            $this->addResponse(
                $this->locations->packagesData->responseMessage,
                $this->locations->packagesData->responseCode
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

            $this->locations->updateLocation($this->postData());

            $this->addResponse(
                $this->locations->packagesData->responseMessage,
                $this->locations->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->locations->removeLocation($this->postData());

            $this->addResponse(
                $this->locations->packagesData->responseMessage,
                $this->locations->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function getLocationByIdAction()
    {
        if ($this->request->isPost()) {

            $this->locations->getLocationById($this->postData());

            $this->addResponse(
                $this->locations->packagesData->responseMessage,
                $this->locations->packagesData->responseCode,
                ['location' => $this->locations->packagesData->responseData]
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function getLocationsByEntityIdAction()
    {
        if ($this->request->isPost()) {

            $locations = $this->locations->getLocationsByEntityId($this->postData());

            $this->addResponse(
                $this->locations->packagesData->responseMessage,
                $this->locations->packagesData->responseCode,
                ['locations' => $locations]
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}