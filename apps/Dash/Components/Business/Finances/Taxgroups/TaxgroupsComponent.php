<?php

namespace Apps\Dash\Components\Business\Finances\Taxgroups;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Finances\TaxGroups\TaxGroups;
use System\Base\BaseComponent;

class TaxgroupsComponent extends BaseComponent
{
    use DynamicTable;

    protected $taxgroupsPackage;

    public function initialize()
    {
        $this->taxgroupsPackage = $this->usePackage(TaxGroups::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {

                $taxGroup = $this->taxgroupsPackage->getById($this->getData()['id']);

                if (!$taxGroup) {
                    return $this->throwIdNotFound();
                }

                $this->view->taxGroup = $taxGroup;
            }

            $this->view->pick('taxgroups/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'business/finances/taxgroups',
                    'remove'    => 'business/finances/taxgroups/remove'
                ]
            ];

        $this->generateDTContent(
            $this->taxgroupsPackage,
            'business/finances/taxgroups/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('taxgroups/list');
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

            $this->taxgroupsPackage->addTaxGroup($this->postData());

            $this->addResponse(
                $this->taxgroupsPackage->packagesData->responseMessage,
                $this->taxgroupsPackage->packagesData->responseCode
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

            $this->taxgroupsPackage->updateTaxGroup($this->postData());

            $this->addResponse(
                $this->taxgroupsPackage->packagesData->responseMessage,
                $this->taxgroupsPackage->packagesData->responseCode
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

            $this->taxgroupsPackage->removeTaxGroup($this->postData());

            $this->addResponse(
                $this->taxgroupsPackage->packagesData->responseMessage,
                $this->taxgroupsPackage->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }
}