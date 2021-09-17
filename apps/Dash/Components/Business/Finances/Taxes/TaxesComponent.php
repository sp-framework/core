<?php

namespace Apps\Dash\Components\Business\Finances\Taxes;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Finances\TaxGroups\TaxGroups;
use Apps\Dash\Packages\Business\Finances\Taxes\Taxes;
use System\Base\BaseComponent;

class TaxesComponent extends BaseComponent
{
    use DynamicTable;

    protected $taxesPackage;

    public function initialize()
    {
        $this->taxesPackage = $this->usePackage(Taxes::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $taxgroupsArr = $this->usePackage(TaxGroups::class)->getAll()->taxgroups;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $tax = $this->taxesPackage->getById($this->getData()['id']);

                if (!$tax) {
                    return $this->throwIdNotFound();
                }

                $this->view->tax = $tax;
            }

            $this->view->taxgroups = $taxgroupsArr;

            $this->view->pick('taxes/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'business/finances/taxes',
                    'remove'    => 'business/finances/taxes/remove'
                ]
            ];

        $taxGroups = [];

        foreach ($taxgroupsArr as $key => $taxGroup) {
            $taxGroups[$taxGroup['id']] = $taxGroup['name'];
        }

        $replaceColumns =
            [
                'tax_group_id'  =>
                    [
                        'html' => $taxGroups
                    ]
                ];

        $this->generateDTContent(
            $this->taxesPackage,
            'business/finances/taxes/view',
            null,
            ['name', 'amount', 'tax_group_id'],
            true,
            ['name', 'amount', 'tax_group_id'],
            $controlActions,
            ['tax_group_id' => 'Tax Group'],
            $replaceColumns,
            'name',
        );

        $this->view->pick('taxes/list');
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

            $this->taxesPackage->addTax($this->postData());

            $this->addResponse(
                $this->taxesPackage->packagesData->responseMessage,
                $this->taxesPackage->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
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

            $this->taxesPackage->updateTax($this->postData());

            $this->addResponse(
                $this->taxesPackage->packagesData->responseMessage,
                $this->taxesPackage->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->taxesPackage->removeTax($this->postData());

            $this->addResponse(
                $this->taxesPackage->packagesData->responseMessage,
                $this->taxesPackage->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }
}