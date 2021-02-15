<?php

namespace Apps\Dash\Components\Business\Directory\Vendors;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\ABNLookup\ABNLookup;
use Apps\Dash\Packages\Business\Directory\Contacts\Contacts;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Ims\Brands\Brands;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class VendorsComponent extends BaseComponent
{
    use DynamicTable;

    protected $vendors;

    public function initialize()
    {
        $this->vendors = $this->usePackage(Vendors::class);
    }

    public function searchABNAction()
    {
        if ($this->postData()['abn']) {
            $abn = $this->usePackage(ABNLookup::class);

            $findDetails = $abn->lookupABN($this->postData()['abn']);

            if ($findDetails) {
                $this->view->vendorDetails = $abn->packagesData->businessDetails;
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
        if (isset($this->getData()['viatoken'])) {
            //Token auth and grab remote vendor
        }

        if (isset($this->getData()['id'])) {
            $this->view->brands = $this->usePackage(Brands::class)->getAll()->brands;

            $this->view->logoLink = '';

            if ($this->getData()['id'] != 0) {

                $vendor = $this->vendors->getById($this->getData()['id']);

                if ($vendor['address_ids'] && $vendor['address_ids'] !== '') {
                    $vendor['address_ids'] = Json::decode($vendor['address_ids'], true);

                    foreach ($vendor['address_ids'] as $addressTypeKey => $addressType) {
                        if (is_array($addressType) && count($addressType) > 0) {
                            foreach ($addressType as $addressKey => $address) {
                                $vendor['address_ids'][$addressTypeKey][$addressKey] =
                                    $this->basepackages->addressbook->getById($address);
                            }
                        }
                        $vendor['address_ids'][$addressTypeKey] =
                            msort($vendor['address_ids'][$addressTypeKey], 'is_primary', SORT_REGULAR, SORT_DESC);
                    }
                }

                if ($vendor['contact_ids'] && $vendor['contact_ids'] !== '') {
                    $vendor['contact_ids'] = Json::decode($vendor['contact_ids'], true);

                    $contacts = $this->usePackage(Contacts::class);

                    foreach ($vendor['contact_ids'] as $contactKey => $contact) {
                        $contactArr = $contacts->getById($contact);

                        $vendor['contact_ids'][$contactKey] = $contactArr;
                    }
                }

                $storages = $this->basepackages->storages;

                if ($vendor['logo'] && $vendor['logo'] !== '') {
                    $this->view->logoLink = $storages->getPublicLink($vendor['logo'], 200);
                }

                if ($vendor['brands']) {
                    $vendor['brands'] = Json::decode($vendor['brands'], true);
                }

                $this->view->vendor = $vendor;
            } else {
                $vendor = [];
                $vendor['contact_ids'] = [];
                $vendor['address_ids'] = [];
                $this->view->vendor = $vendor;
            }
            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->isEnabled()) {
                $this->view->geo = true;
            } else {
                $this->view->geo = false;
            }

            $storages = $this->basepackages->storages->getAppStorages();

            if ($storages && isset($storages['public'])) {
                $this->view->storages = $storages['public'];
            } else {
                $this->view->storages = [];
            }

            $this->view->pick('vendors/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'is_manufacturer'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ],
                    'does_dropship'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ],
                    'does_jobwork'   => ['html'  =>
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
                    'edit'      => 'business/directory/vendors',
                    'remove'    => 'business/directory/vendors/remove'
                ]
            ];

        $this->generateDTContent(
            $this->vendors,
            'business/directory/vendors/view',
            null,
            ['abn', 'name', 'is_manufacturer', 'does_dropship', 'does_jobwork'],
            true,
            ['abn', 'name', 'is_manufacturer', 'does_dropship', 'does_jobwork'],
            $controlActions,
            [],
            $replaceColumns,
            'name'
        );

        $this->view->pick('vendors/list');
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

            $this->vendors->addVendor($this->postData());

            $this->view->responseCode = $this->vendors->packagesData->responseCode;

            $this->view->responseMessage = $this->vendors->packagesData->responseMessage;

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

            $this->vendors->updateVendor($this->postData());

            $this->view->responseCode = $this->vendors->packagesData->responseCode;

            $this->view->responseMessage = $this->vendors->packagesData->responseMessage;

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

            $this->vendors->removeVendor($this->postData());

            $this->view->responseCode = $this->vendors->packagesData->responseCode;

            $this->view->responseMessage = $this->vendors->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function searchVendorNameAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchVendor = $this->vendors->searchByName($searchQuery);

                if ($searchVendor) {
                    $this->view->responseCode = $this->vendors->packagesData->responseCode;

                    $this->view->vendors = $this->vendors->packagesData->vendors;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}