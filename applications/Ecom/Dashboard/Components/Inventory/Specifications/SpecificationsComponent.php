<?php

namespace Applications\Ecom\Dashboard\Components\Inventory\Specifications;

use Applications\Ecom\Dashboard\Packages\ABNLookup\ABNLookup;
use Applications\Ecom\Dashboard\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Ecom\Dashboard\Packages\Inventory\Specifications\Specifications;
use System\Base\BaseComponent;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;

class SpecificationsComponent extends BaseComponent
{
    use DynamicTable;

    protected $specifications;

    public function initialize()
    {
        $this->specifications = $this->usePackage(Specifications::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $params =
            [
                'conditions'    => 'is_group = :is_group:',
                'bind'          =>
                    [
                        'is_group'    => '1'
                    ]
            ];

        $specifications = $this->specifications->getByParams($params);

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {

                $this->view->specification = $this->specifications->getById($this->getData()['id']);
            }

            if ($specifications) {
                $this->view->specifications = $specifications;
            } else {
                $this->view->specifications = [];
            }

            $this->view->pick('specifications/view');

            return;
        }

        if ($this->request->isPost()) {
            $groupToName = [];

            $params =
                [
                    'conditions'    => 'is_group != :is_group:',
                    'bind'          =>
                        [
                            'is_group'    => '1'
                        ]
                ];

            $specifications = $this->specifications->getByParams($params);

            if ($specifications) {
                $groupToName['0'] = '-';
                foreach ($specifications as $specificationKey => $specificationValue) {
                    if ($specificationValue['group_id'] != '0') {
                        $group = $this->specifications->getById($specificationValue['group_id']);
                        $groupToName[$specificationValue['group_id']] = $group['name'];
                    } else {
                    }
                }

                $replaceColumns =
                    [
                        'is_group' => ['html' => ['0' => 'No', '1' => 'Yes']],
                        'group_id' => ['html'  => $groupToName]
                    ];

            } else {
                $replaceColumns = null;
            }
        } else {
            $replaceColumns = null;
        }
        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'inventory/specifications',
                    'remove'    => 'inventory/specifications/remove'
                ]
            ];

        $this->generateDTContent(
            $this->specifications,
            'inventory/specifications/view',
            null,
            ['name', 'is_group', 'group_id', 'product_count'],
            true,
            ['name', 'is_group', 'group_id', 'product_count'],
            $controlActions,
            ['group_id'=>'Group Name'],
            $replaceColumns,
            'name'
        );

        $this->view->pick('specifications/list');
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

            $this->specifications->addSpecification($this->postData());

            $this->view->responseCode = $this->specifications->packagesData->responseCode;

            $this->view->responseMessage = $this->specifications->packagesData->responseMessage;

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

            $this->specifications->updateSpecification($this->postData());

            $this->view->responseCode = $this->specifications->packagesData->responseCode;

            $this->view->responseMessage = $this->specifications->packagesData->responseMessage;

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

            $this->specifications->removeSpecification($this->postData());

            $this->view->responseCode = $this->specifications->packagesData->responseCode;

            $this->view->responseMessage = $this->specifications->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}