<?php

namespace Applications\Ecom\Admin\Components\Businesses;

use Applications\Ecom\Common\Packages\ABNLookup\ABNLookup;
use Applications\Ecom\Common\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Ecom\Common\Packages\Businesses\Businesses;
use System\Base\BaseComponent;

class BusinessesComponent extends BaseComponent
{
    use DynamicTable;

    protected $businesses;

    public function initialize()
    {
        $this->businesses = $this->usePackage(Businesses::class);
    }

    public function searchABNAction()
    {
        if ($this->postData()['abn']) {
            $abn = $this->usePackage(ABNLookup::class);

            $findDetails = $abn->lookupABN($this->postData()['abn']);

            if ($findDetails) {
                $this->view->businessDetails = $abn->packagesData->businessDetails;
            }
            $this->view->responseCode = $abn->packagesData->responseCode;

            $this->view->responseMessage = $abn->packagesData->responseMessage;
        }
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $businessesArr = $this->businesses->getAll()->businesses;
            $businesses = [];

            foreach ($businessesArr as $key => $value) {
                $businesses[$value['id']] = $value;
            }

            if ($this->getData()['id'] != 0) {
                $this->view->business = $businesses[$this->getData()['id']];

                unset($businesses[$this->getData()['id']]);

                $storages = $this->basepackages->storages;

                $this->view->logoLink = $storages->getPublicLink($this->view->business['logo'], 200);
            }

            $this->view->businesses = $businesses;

            $this->view->pick('businesses/view');

            return;
        }

        if ($this->request->isPost()) {
            $parentToName = [];
            foreach ($this->businesses->getAll()->businesses as $businessKey => $businessValue) {
                if ($businessValue['parent'] !== '0') {
                    $parentToName[$businessValue['parent']] = $businessValue['name'];
                } else {
                    $parentToName[$businessValue['parent']] = '-';
                }
            }

            $replaceColumns =
                [
                    'parent' => ['html'  => $parentToName],
                    'type'   => ['html'  =>
                        [
                            '1' => 'Primary',
                            '2' => 'Subsidiary',
                            '3' => 'Branch'
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
                    'edit'      => 'businesses',
                    'remove'    => 'businesses/remove'
                ]
            ];

        $this->generateDTContent(
            $this->businesses,
            'businesses/view',
            null,
            ['abn', 'name', 'type', 'parent'],
            true,
            [],
            $controlActions,
            [],
            $replaceColumns,
            'name'
        );

        $this->view->pick('businesses/list');
    }

    public function getAllBusinessesAction()
    {
        $this->view->businesses = $this->businesses->getAll()->businesses;
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

            $this->businesses->addBusiness($this->postData());

            $this->view->responseCode = $this->businesses->packagesData->responseCode;

            $this->view->responseMessage = $this->businesses->packagesData->responseMessage;

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

            $this->businesses->updateBusiness($this->postData());

            $this->view->responseCode = $this->businesses->packagesData->responseCode;

            $this->view->responseMessage = $this->businesses->packagesData->responseMessage;

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

            $this->businesses->removeBusiness($this->postData());

            $this->view->responseCode = $this->businesses->packagesData->responseCode;

            $this->view->responseMessage = $this->businesses->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}