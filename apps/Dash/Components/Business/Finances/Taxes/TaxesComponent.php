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
        if (isset($this->getData()['id'])) {

            $this->view->taxgroups = $this->usePackage(TaxGroups::class)->getAll()->taxgroups;

            if ($this->getData()['id'] != 0) {
                $tax = $this->taxes->getById($this->getData()['id']);

                $this->view->tax = $tax;
            }

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

        $this->generateDTContent(
            $this->taxesPackage,
            'business/finances/taxes/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            null,
            null,
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