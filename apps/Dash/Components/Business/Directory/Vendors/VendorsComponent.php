<?php

namespace Apps\Dash\Components\Business\Directory\Vendors;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\ABNLookup\ABNLookup;
use Apps\Dash\Packages\Business\Directory\Contacts\Contacts;
use Apps\Dash\Packages\Business\Directory\VendorGroups\VendorGroups;
use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Business\Finances\TaxGroups\TaxGroups;
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

        $this->notes = $this->basepackages->notes;
    }

    public function searchABNAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->postData()['abn']) {
                $abn = $this->usePackage(ABNLookup::class);

                $findDetails = $abn->lookupABN($this->postData()['abn']);

                if ($findDetails) {
                    $this->view->vendorDetails = $abn->packagesData->businessDetails;
                }
                $this->view->responseCode = $abn->packagesData->responseCode;

                $this->view->responseMessage = $abn->packagesData->responseMessage;
            }
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
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
            $this->view->vendorgroups = $this->usePackage(VendorGroups::class)->getAll()->vendorgroups;

            //This should be moved to payments package
            $this->view->paymentTerms = $this->vendors->getPaymentTerms();

            $this->view->brands = $this->usePackage(Brands::class)->getAll()->brands;

            $this->view->logoLink = '';

            $this->view->taxGroups = $this->usePackage(TaxGroups::class)->getAll()->taxgroups;

            $this->view->b2bAccountManagers = [];

            if ($this->getData()['id'] != 0) {

                $vendor = $this->vendors->getVendorById($this->getData()['id']);

                if (!$vendor) {
                    return $this->throwIdNotFound();
                }

                $this->view->b2bAccountManagers = $vendor['b2bAccountManagers'];

                $vendor['activityLogs'] = $this->vendors->getActivityLogs($this->getData()['id']);

                $vendor['notes'] = $this->notes->getNotes('vendors', $this->getData()['id']);

                $storages = $this->basepackages->storages;

                if ($vendor['logo'] && $vendor['logo'] !== '') {
                    $this->view->logoLink = $storages->getPublicLink($vendor['logo'], 200);
                }

                if ($vendor['brands']) {
                    $vendor['brands'] = Json::decode($vendor['brands'], true);
                }

                $vendor['contact_phone'] = $this->formatNumbers($vendor['contact_phone']);
                $vendor['contact_fax'] = $this->formatNumbers($vendor['contact_fax']);

                $this->view->vendor = $vendor;
            } else {
                $vendor = [];
                // $vendor['contact_ids'] = [];
                $vendor['address_ids'] = [];
                $this->view->vendor = $vendor;
            }
            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->isEnabled()) {
                $this->view->geo = true;
            } else {
                $this->view->geo = false;
            }

            //Check Geo Locations Dependencies
            if ($this->basepackages->geoCountries->currencyEnabled()) {
                $this->view->currency = true;
            } else {
                $this->view->currency = false;
            }

            $this->view->currencies = $this->basepackages->geoCountries->currencyEnabled(true);

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
                    'is_supplier'   => ['html'  =>
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
                    'is_service_provider'   => ['html'  =>
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
            ['abn', 'business_name', 'is_manufacturer', 'is_supplier', 'does_dropship', 'is_service_provider', 'does_jobwork'],
            true,
            ['abn', 'business_name', 'is_manufacturer', 'is_supplier', 'does_dropship', 'is_service_provider', 'does_jobwork'],
            $controlActions,
            [],
            $replaceColumns,
            'business_name'
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

            $this->addResponse(
                $this->vendors->packagesData->responseMessage,
                $this->vendors->packagesData->responseCode
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

            $this->vendors->updateVendor($this->postData());

            $this->addResponse(
                $this->vendors->packagesData->responseMessage,
                $this->vendors->packagesData->responseCode
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

            $this->vendors->removeVendor($this->postData());

            $this->addResponse(
                $this->vendors->packagesData->responseMessage,
                $this->vendors->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function searchVendorNameAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 1) {
                    return;
                }

                $searchVendor = $this->vendors->searchByName($searchQuery);

                if ($searchVendor) {
                    $this->view->responseCode = $this->vendors->packagesData->responseCode;

                    $this->view->vendors = $this->vendors->packagesData->vendors;
                }
            } else {
                $this->addResponse('search query missing', 1);
            }
        }
    }

    public function searchSupplierManufacturerNameAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 1) {
                    return;
                }

                $searchVendor = $this->vendors->searchSuppliersManufacturersByName($searchQuery);

                if ($searchVendor) {
                    $this->view->responseCode = $this->vendors->packagesData->responseCode;

                    $this->view->vendors = $this->vendors->packagesData->vendors;
                }
            } else {
                $this->addResponse('search query missing', 1);
            }
        }
    }

    public function searchVendorIdAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['vendorId']) {
                $vendorId = $this->postData()['vendorId'];

                $searchVendor = $this->vendors->searchByVendorId($vendorId);

                $this->view->responseCode = $this->vendors->packagesData->responseCode;

                $this->view->vendor = $this->vendors->packagesData->vendor;
            } else {
                $this->addResponse('vendor id missing', 1);
            }
        }
    }
}