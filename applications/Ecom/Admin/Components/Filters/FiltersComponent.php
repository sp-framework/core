<?php

namespace Applications\Ecom\Admin\Components\Filters;

use Applications\Ecom\Common\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class FiltersComponent extends BaseComponent
{
    use DynamicTable;

    protected $filters;

    public function initialize()
    {
        $this->filters = $this->basepackages->filters;
    }
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $filter = $this->filters->getById($this->getData()['id']);

                $this->view->filter = $filter;
            }

            $components = $this->modules->components->components;

            foreach ($components as $key => $component) {
                $components[$key]['name'] = $component['name'] . ' (' . $component['category'] . '/' . $component['sub_category'] . ')';
            }
            $this->view->components = $components;

            $this->view->pick('filters/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'type' => ['html'  =>
                        [
                            '0' =>  'System',
                            '1' =>  'User',
                            '2' =>  'User',
                        ]
                    ],
                    'is_default' => ['html'  =>
                        [
                            '0' =>  'No',
                            '1' =>  'Yes'
                        ]
                    ],
                    'auto_generated' => ['html'  =>
                        [
                            '0' =>  'No',
                            '1' =>  'Yes'
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
                    'edit'      => 'filters',
                    'remove'    => 'filters/remove'
                ]
            ];

        $this->generateDTContent(
            $this->basepackages->filters,
            'filters/view',
            null,
            ['name', 'type', 'auto_generated', 'is_default'],
            true,
            ['name', 'type', 'auto_generated', 'is_default'],
            $controlActions,
            null,
            $replaceColumns,
            'name'
        );

        $this->view->pick('filters/list');
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

            //Adding close in add as cloning requires add permission so both add and clone can be performed in same action.
            if (isset($this->postData()['clone']) && $this->postData()['clone']) {
                $this->filters->cloneFilter($this->postData());
            } else {
                $this->filters->addFilter($this->postData());
            }

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            if (isset($this->postData()['component_id'])) {
                $this->view->filters = $this->filters->packagesData->filters;
            }

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

            $this->filters->updateFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            if (isset($this->postData()['component_id'])) {
                $this->view->filters = $this->filters->packagesData->filters;
            }

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

            if (!$this->checkCSRF()) {
                return;
            }

            $this->filters->removeFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            if (isset($this->postData()['component_id'])) {
                $this->view->filters = $this->filters->packagesData->filters;
            }

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function getdefaultfilterAction()
    {
        if ($this->request->isPost()) {

            if ($this->filters->getDefaultFilter($this->postData()['component_id'])) {
                $this->view->defaultFilter = $this->filters->packagesData->defaultFilter;
            }

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}