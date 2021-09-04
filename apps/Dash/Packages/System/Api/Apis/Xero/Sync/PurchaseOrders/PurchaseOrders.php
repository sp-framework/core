<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders;

use Apps\Dash\Packages\Business\Directory\Vendors\Vendors;
use Apps\Dash\Packages\Business\Entities\Model\BusinessEntities;
use Apps\Dash\Packages\Business\Finances\TaxGroups\Model\BusinessFinancesTaxGroups;
use Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Model\ImsStockPurchaseOrdersProducts;
use Apps\Dash\Packages\Ims\Stock\PurchaseOrders\PurchaseOrders as ImsPurchaseOrders;
use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Attachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Model\SystemApiXeroAttachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\ContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Contacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model\SystemApiXeroContacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\History;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\Model\SystemApiXeroHistory;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Items\Items;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Organisations;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model\SystemApiXeroPurchaseOrders;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Model\SystemApiXeroPurchaseOrdersLineitems;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\TaxRates\TaxRates;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentByIdRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderAttachmentsRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrderHistoryRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetPurchaseOrdersRestRequest;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class PurchaseOrders extends BasePackage
{
    protected $poPackage;

    protected $vendorPackage;

    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    protected $addCounter = 0;

    protected $addedIds = [];

    protected $updateCounter = 0;

    protected $updatedIds = [];

    protected $errors = [];

    protected $errorIds = [];

    protected $responseData = [];

    protected $responseMessage = '';

    public function sync($apiId = null, $parameters = null)
    {
        $this->apiPackage = new Api;

        // $this->syncWithLocal();return;

        $this->request = new GetPurchaseOrdersRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if ($xeroApis && count($xeroApis) > 0) {
            if (!$apiId) {
                foreach ($xeroApis as $key => $xeroApi) {
                    $this->syncWithXero($xeroApi['api_id'], $parameters);
                }
            } else {
                $this->syncWithXero($apiId, $parameters);
            }

            $this->syncWithLocal();

            $this->responseData = array_merge($this->responseData,
                [
                    'purchaseOrders' =>
                        [
                            'addedIds' => $this->addedIds,
                            'updatedIds' => $this->updatedIds
                        ]
                ]
            );

            $this->responseMessage =
                $this->responseMessage . ' ' . 'Purchase Orders Sync Ok. Added: ' . $this->addCounter . '. Updated: ' . $this->updateCounter . '.';

            $this->addResponse(
                $this->responseMessage,
                0,
                $this->responseData
            );
        } else {
            $this->addResponse('Sync Error. No API Configuration Found. Please disable this task or configure the API.', 1);
        }
    }

    protected function syncWithXero($apiId, $parameters = null)
    {
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->syncDependencies($apiId, $parameters);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        if ($parameters && isset($parameters[$apiId]['PurchaseOrders']['modifiedSince'])) {
            $modifiedSince = $parameters[$apiId]['PurchaseOrders']['modifiedSince'];
        } else {
            $modifiedSince = $this->apiPackage->getApiCallMethodStat('GetPurchaseOrders', $apiId);
        }
        if ($modifiedSince) {
            $this->xeroApi->setOptionalHeader(['If-Modified-Since' => $modifiedSince]);
        }

        if ($parameters && isset($parameters[$apiId]['PurchaseOrders']['dateFrom'])) {
            $this->request->DateFrom = $parameters[$apiId]['PurchaseOrders']['dateFrom'];
        }

        if ($parameters && isset($parameters[$apiId]['PurchaseOrders']['dateTo'])) {
            $this->request->DateTo = $parameters[$apiId]['PurchaseOrders']['dateTo'];
        }

        if ($parameters && isset($parameters[$apiId]['PurchaseOrders']['status'])) {
            $this->request->Status = $parameters[$apiId]['PurchaseOrders']['status'];
        }

        $page = 1;

        do {
            $this->request->page = $page;

            $response = $this->xeroApi->getPurchaseOrders($this->request);

            $this->api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if ((isset($responseArr['Status']) && $responseArr['Status'] === 'OK') &&
                isset($responseArr['PurchaseOrders'])
            ) {
                if (count($responseArr['PurchaseOrders']) > 0) {
                    $this->addUpdateXeroPurchaseOrders($apiId, $responseArr['PurchaseOrders']);
                }
            }

            $page++;
        } while (isset($responseArr['PurchaseOrders']) && count($responseArr['PurchaseOrders']) > 0);
    }

    protected function syncDependencies($apiId, $parameters)
    {
        $organisations = new Organisations;

        $organisations->sync($apiId);

        $contactGroups = new ContactGroups;

        $contactGroups->sync($apiId);

        $contacts = new Contacts;

        $contacts->sync($apiId, $parameters);

        $this->responseData = array_merge($this->responseData, ['contacts' => $contacts->packagesData->responseData]);

        $this->responseMessage .= $contacts->packagesData->responseMessage;

        $items = new Items;

        $items->sync($apiId);

        $taxRates = new TaxRates;

        $taxRates->sync($apiId);
    }

    protected function getPurchaseOrderAttachments($purchaseOrderId)
    {
        $request = new GetPurchaseOrderAttachmentsRestRequest;

        $request->PurchaseOrderID = $purchaseOrderId;

        $response = $this->xeroApi->getPurchaseOrderAttachments($request);

        if ($response) {
            $this->api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if (isset($responseArr['Status']) && $responseArr['Status'] === 'OK') {
                if (isset($responseArr['Attachments'])) {
                    return $responseArr['Attachments'];
                }
            }
        }

        return [];
    }

    protected function getPurchaseOrderHistory($purchaseOrderId)
    {
        $request = new GetPurchaseOrderHistoryRestRequest;

        $request->PurchaseOrderID = $purchaseOrderId;

        $response = $this->xeroApi->getPurchaseOrderHistory($request);

        if ($response) {
            $this->api->refreshXeroCallStats($response->getHeaders());

            $responseArr = $response->toArray();

            if (isset($responseArr['Status']) && $responseArr['Status'] === 'OK') {
                if (isset($responseArr['HistoryRecords'])) {
                    return $responseArr['HistoryRecords'];
                }
            }
        }

        return [];
    }

    protected function addUpdateXeroPurchaseOrders($apiId, array $purchaseOrders)
    {
        foreach ($purchaseOrders as $purchaseOrderKey => $purchaseOrder) {
            $model = SystemApiXeroPurchaseOrders::class;

            $xeroPo = $model::findFirst(
                [
                    'conditions'    => 'PurchaseOrderID = :poid:',
                    'bind'          =>
                        [
                            'poid'  => $purchaseOrder['PurchaseOrderID']
                        ]
                ]
            );

            $purchaseOrder['api_id'] = $apiId;

            $purchaseOrder['ContactID'] = $purchaseOrder['Contact']['ContactID'];

            if (!$xeroPo) {
                $modelToUse = new $model();

                $modelToUse->assign($this->jsonData($purchaseOrder));

                $modelToUse->create();

                $this->addCounter = $this->addCounter + 1;

                array_push($this->addedIds, $purchaseOrder['PurchaseOrderID']);

                $thisPo = $modelToUse->toArray();
            } else {
                // if ($purchaseOrder['UpdatedDateUTC'] !== $xeroPo->UpdatedDateUTC) {
                    if ($xeroPo->baz_po_id) {
                        $purchaseOrder['resync_local'] = '1';
                    }

                    $xeroPo->assign($this->jsonData($purchaseOrder));

                    $xeroPo->update();

                    $this->updateCounter = $this->updateCounter + 1;

                    array_push($this->updatedIds, $purchaseOrder['PurchaseOrderID']);

                    $thisPo = $xeroPo->toArray();
                // } else {
                //     continue;
                // }
            }

            if (isset($purchaseOrder['LineItems']) && count($purchaseOrder['LineItems']) > 0) {
                $this->addUpdateXeroPurchaseOrderLineItems($purchaseOrder);
            }

            if (isset($purchaseOrder['HasAttachments']) && $purchaseOrder['HasAttachments'] == true) {
                $xeroAttachments = new Attachments;

                $xeroAttachments->sync(
                    $apiId,
                    $this->packageName,
                    $thisPo['PurchaseOrderID'],
                    $this->getPurchaseOrderAttachments($thisPo['PurchaseOrderID'])
                );
            }

            $xeroHistory = new History;

            $xeroHistory->sync(
                $apiId,
                $this->packageName,
                $thisPo['PurchaseOrderID'],
                $this->getPurchaseOrderHistory($thisPo['PurchaseOrderID'])
            );
        }
    }

    protected function addUpdateXeroPurchaseOrderLineItems($purchaseOrder)
    {
        $model = SystemApiXeroPurchaseOrdersLineitems::class;

        $poLineItems = [];

        foreach ($purchaseOrder['LineItems'] as $lineItemKey => $lineItem) {
            array_push($poLineItems, $lineItem['LineItemID']);
        }

        $xeroPoLineItem = $model::find(
            [
                'conditions'    => 'PurchaseOrderID = :poid:',
                'bind'          =>
                    [
                        'poid'  => $purchaseOrder['PurchaseOrderID']
                    ]
            ]
        );

        if ($xeroPoLineItem) {
            $xeroPoLineItemArr = $xeroPoLineItem->toArray();

            foreach ($xeroPoLineItemArr as $xPLineItem) {
                if (!in_array($xPLineItem['LineItemID'], $poLineItems)) {
                    $itemObj = $model::findFirstByLineItemID($xPLineItem['LineItemID']);

                    $itemObj->delete();
                }
            }
        }

        foreach ($purchaseOrder['LineItems'] as $lineItemKey => $lineItem) {
            $xeroPoLineItem = $model::findFirst(
                [
                    'conditions'    => 'PurchaseOrderID = :poid: AND LineItemID = :liid:',
                    'bind'          =>
                        [
                            'poid'  => $purchaseOrder['PurchaseOrderID'],
                            'liid'  => $lineItem['LineItemID']
                        ]
                ]
            );

            if (!$xeroPoLineItem) {
                $modelToUse = new $model();

                $lineItem['PurchaseOrderID'] = $purchaseOrder['PurchaseOrderID'];

                $modelToUse->assign($this->jsonData($lineItem));

                $modelToUse->create();
            } else {
                $xeroPoLineItem->assign($this->jsonData($lineItem));

                $xeroPoLineItem->update();
            }
        }
    }

    public function syncWithLocal()
    {
        $model = SystemApiXeroPurchaseOrders::class;

        $xeroPo = $model::find(
            [
                'conditions'    => 'baz_po_id IS NULL OR resync_local = :rl:',
                'bind'          =>
                    [
                        'rl'    => '1',
                    ]
            ]
        );

        if ($xeroPo) {
            $this->poPackage = $this->usePackage(ImsPurchaseOrders::class);

            $this->vendorPackage = $this->usePackage(Vendors::class);

            $pos = $xeroPo->toArray();

            if ($pos && count($pos) > 0) {
                foreach ($pos as $poKey => $po) {
                    $this->errors = [];

                    $productsModel = SystemApiXeroPurchaseOrdersLineitems::class;

                    $poProducts = $productsModel::find(
                        [
                            'conditions'    => 'PurchaseOrderID = :poid:',
                            'bind'          =>
                                [
                                    'poid'  => $po['PurchaseOrderID']
                                ]
                        ]
                    );

                    if ($poProducts) {
                        $po['products'] = $poProducts->toArray();
                    }

                    $this->generatePoData($po);

                    if (count($this->errors) > 0) {
                        $this->poPackage->errorPurchaseOrder('Errors in purchase orders. Please check details for more information.', Json::encode($this->errors));
                    }
                }
            }
        }
    }

    protected function generatePoData(array $po)
    {
        $purchaseOrder = [];

        $entityModel = BusinessEntities::class;

        $entity = $entityModel::findFirst(
            [
                'conditions'    => 'api_id = :aid:',
                'bind'          =>
                    [
                        'aid'   => $po['api_id']
                    ]
            ]
        );

        if ($entity) {
            $purchaseOrder['entity_id'] = $entity->id;
        } else {
            $purchaseOrder['entity_id'] = '0';
        }

        $purchaseOrder['references'] = $po['PurchaseOrderNumber'] . ',' . $po['Reference'];

        $purchaseOrder['status'] = $this->getOrderStatusId($po['Status']);

        $model = SystemApiXeroContacts::class;

        $xeroContactObj = $model::findFirst(
            [
                'conditions'    => 'ContactID = :cid:',
                'bind'          =>
                    [
                        'cid'   => $po['ContactID']
                    ]
            ]
        );

        $purchaseOrder['vendor_id'] = '0';
        $purchaseOrder['vendor_address_id'] = '0';
        $purchaseOrder['vendor_contact_id'] = '0';

        if ($xeroContactObj) {
            $xeroContact = $xeroContactObj->toArray();

            if ($xeroContact['baz_vendor_id'] &&
                $xeroContact['baz_vendor_id'] !== '' &&
                $xeroContact['baz_vendor_id'] != '0'
            ) {
                $vendor = $this->vendorPackage->getVendorById($xeroContact['baz_vendor_id']);

                if ($vendor) {
                    $purchaseOrder['vendor_id'] = $xeroContact['baz_vendor_id'];

                    if ($vendor['address_ids']['2'] && count($vendor['address_ids']['2']) > 0) {
                        $purchaseOrder['vendor_address_id'] = $vendor['address_ids']['2']['0']['id'];
                    }
                }
            }
        }

        $dDate = \DateTime::createFromFormat("U",str_replace('/Date(', '', str_replace('000+0000)/', '', $po['DeliveryDate'])));

        if ($dDate) {
            $dDate = $dDate->format('Y-m-d');
        } else {
            $dDate = '';
        }

        $purchaseOrder['delivery_date'] = $dDate;

        $purchaseOrder['delivery_type'] = '3';

        $purchaseOrder['address_id'] = $this->addPoAddress($po, $purchaseOrder);
        $purchaseOrder['contact_fullname'] = $po['AttentionTo'];
        $purchaseOrder['contact_phone'] = $po['Telephone'];

        $purchaseOrder['total_tax'] = $po['TotalTax'];
        $purchaseOrder['total_amount'] = $po['Total'];
        $purchaseOrder['delivery_instructions'] = $po['DeliveryInstructions'];

        if ($po['HasAttachments'] == '1') {
            $purchaseOrder['attachments'] = Json::encode($this->addPoAttachments($po, $purchaseOrder));
        } else {
            $purchaseOrder['attachments'] = Json::encode([]);
        }

        if ($po['baz_po_id'] && $po['baz_po_id'] != '0') {
            if ($this->poPackage->getById($po['baz_po_id'])) {

                $purchaseOrder = array_merge($this->poPackage->getById($po['baz_po_id']), $purchaseOrder);

                if ($this->poPackage->update($purchaseOrder)) {
                    $purchaseOrder = $this->poPackage->packagesData->last;
                } else {
                    $this->errors = array_merge($this->errors, ['Could not update purchase order data - ' . $po['AttentionTo']]);
                }
            } else {
                if ($this->poPackage->add($purchaseOrder)) {
                    $purchaseOrder = $this->poPackage->packagesData->last;

                    $purchaseOrder = $this->poPackage->addRefId($purchaseOrder);
                } else {
                    $this->errors = array_merge($this->errors, ['Could not add purchase order data - ' . $po['AttentionTo']]);
                }
            }
        } else {
            if ($this->poPackage->add($purchaseOrder)) {
                $purchaseOrder = $this->poPackage->packagesData->last;

                $purchaseOrder = $this->poPackage->addRefId($purchaseOrder);
            } else {
                $this->errors = array_merge($this->errors, ['Could not add purchase order data - ' . $po['AttentionTo']]);
            }
        }

        if ($purchaseOrder['entity_id'] === '0') {
            $this->errors = array_merge($this->errors, ['Entity missing for purchase order - ' . $purchaseOrder['id']]);
        }
        if ($purchaseOrder['vendor_id'] === '0') {
            $this->errors = array_merge($this->errors, ['Vendor missing for purchase order - ' . $purchaseOrder['id']]);
        } else if ($purchaseOrder['vendor_contact_id'] === '0') {
            $this->errors = array_merge($this->errors, ['Vendor contact missing for purchase order - ' . $purchaseOrder['id']]);
        }

        if ($po['products'] && count($po['products']) > 0) {
            $totalQty = $this->generatePurchaseOrderProducts($po, $purchaseOrder['id']);

            if ($totalQty) {
                $purchaseOrder['total_quantity'] = $totalQty;

                $this->poPackage->update($purchaseOrder);
            }
        } else {
            $this->errors = array_merge($this->errors, ['Products missing for purchase order - ' . $purchaseOrder['id']]);
        }

        $this->addPurchaseOrderHistory($po, $purchaseOrder);

        $po['baz_po_id'] = $purchaseOrder['id'];

        $model = SystemApiXeroPurchaseOrders::class;

        $xeroPurchaseOrder = $model::findFirst(
            [
                'conditions'    => 'PurchaseOrderID = :poid:',
                'bind'          =>
                    [
                        'poid'  => $po['PurchaseOrderID']
                    ]
            ]
        );

        $xeroPurchaseOrder->assign($this->jsonData($po));

        $xeroPurchaseOrder->update();
    }

    protected function getOrderStatusId($poStatus)
    {
        $statuses = $this->poPackage->getOrderStatuses();

        foreach ($statuses as $statusKey => $status) {
            if ($status['name'] === $poStatus) {
                return $status['id'];
            }
        };

        return '1';//default DRAFT
    }

    protected function addPoAddress($po, $purchaseOrder)
    {
        $po['DeliveryAddress'] = explode(PHP_EOL, trim($po['DeliveryAddress']));

        if (!is_array($po['DeliveryAddress']) ||
            count($po['DeliveryAddress']) === 0
        ) {
            return '0';
        }

        $poPostCodeKey = null;
        $poPostCode = null;

        foreach ($po['DeliveryAddress'] as $addressKey => $addressLine) {
            if ((int) $addressLine !== 0) {
                $poPostCodeKey = $addressKey;
                $poPostCode = $addressLine;
            }
        }

        if (!$poPostCodeKey) {
            return '0';
        }

        if ($poPostCodeKey === Arr::last($po['DeliveryAddress'])) {
            $poCountry = null;
        } else if ($poPostCodeKey === array_key_last($po['DeliveryAddress']) - 1) {
            $poCountry = Arr::last($po['DeliveryAddress']);
        } else {
            return '0';
        }

        $poState = $po['DeliveryAddress'][$poPostCodeKey - 1];
        $poCityKey = $poPostCodeKey - 2;
        $poCity = $po['DeliveryAddress'][$poCityKey];

        if ($poCityKey === 1) {
            $street_address = $po['DeliveryAddress'][0];
            $street_address_2 = '';
        } else if ($poCityKey === 2) {
            $street_address = $po['DeliveryAddress'][0];
            $street_address_2 = $po['DeliveryAddress'][1];
        } else if ($poCityKey > 2) {
            $street_address = $po['DeliveryAddress'][0];
            $street_address_2 = $po['DeliveryAddress'][1];
            $street_address_2 = $street_address_2 . ' ' . $po['DeliveryAddress'][2];
        }

        if (!$poCity && !$poState && !$poCountry) {
            return '0';
        }

        $found = false;

        if ($this->basepackages->geoCities->searchCities($poCity)) {
            $cityData = $this->basepackages->geoCities->packagesData->cities;
            if (count($cityData) > 0) {
                foreach ($cityData as $cityKey => $city) {
                    if (strtolower($city['name']) === strtolower($poCity)) {
                        $found = true;

                        $newAddress['city_id'] = $city['id'];
                        $newAddress['city_name'] = $city['name'];
                        $newAddress['state_id'] = $city['state_id'];
                        $newAddress['state_name'] = $city['state_name'];
                        $newAddress['country_id'] = $city['country_id'];
                        $newAddress['country_name'] = $city['country_name'];
                    }

                    if ($found) {
                        break;
                    }
                }
            }
        }

        if (!$found) {
            //Country
            $foundCountry = null;

            if ($this->basepackages->geoCountries->searchCountries($poCountry, true)) {
                $countryData = $this->basepackages->geoCountries->packagesData->countries;

                if (count($countryData) > 0) {
                    foreach ($countryData as $countryKey => $country) {
                        if (strtolower($country['name']) === strtolower($poCountry)) {
                            $foundCountry = $country;
                            $vendor['currency'] = $country['currency'];
                            break;
                        }
                    }
                }
            }

            if (!$foundCountry) {
                $newCountry['name'] = $poCountry;
                $newCountry['installed'] = '1';
                $newCountry['enabled'] = '1';
                $newCountry['user_added'] = '1';

                if ($this->basepackages->geoCountries->add($newCountry)) {
                    $newAddress['country_id'] = $this->basepackages->geoCountries->packagesData->last['id'];
                    $newAddress['country_name'] = $newCountry['name'];
                } else {

                    $this->errors = array_merge($this->errors, ['Could not add country data.']);
                }
            } else {
                //We check if country is installed or not, if not, we install and enable it
                if ($foundCountry['installed'] != '1') {
                    $foundCountry['enabled'] = '1';

                    $this->basepackages->geoCountries->installCountry($foundCountry);
                } else if ($foundCountry['enabled'] != '1') {
                    $foundCountry['enabled'] = '1';

                    $this->basepackages->geoCountries->update($foundCountry);
                }

                $newAddress['country_id'] = $foundCountry['id'];
                $newAddress['country_name'] = $foundCountry['name'];
            }

            //State (Region in Xero Address)
            $foundState = null;

            if ($this->basepackages->geoStates->searchStatesByCode($poState, true)) {
                $stateData = $this->basepackages->geoStates->packagesData->states;

                if (count($stateData) > 0) {
                    foreach ($stateData as $stateKey => $state) {
                        if (strtolower($state['state_code']) === strtolower($poState)) {
                            $foundState = $state;
                            break;
                        }
                    }
                }
            }

            if (!$foundState) {
                $newState['name'] = $poState;
                $newState['state_code'] = substr($poState, 0, 3);
                $newState['user_added'] = '1';
                $newState['country_id'] = $newAddress['country_id'];

                if ($this->basepackages->geoStates->add($newState)) {
                    $newAddress['state_id'] = $this->basepackages->geoStates->packagesData->last['id'];
                    $newAddress['state_name'] = $newState['name'];
                } else {

                    $this->errors = array_merge($this->errors, ['Could not add state data.']);
                }
            } else {
                $newAddress['state_id'] = $foundState['id'];
                $newAddress['state_name'] = $foundState['name'];
            }

            //New City
            $newCity['name'] = $poCity;
            $newCity['state_id'] = $newAddress['state_id'];
            $newCity['country_id'] = $newAddress['country_id'];
            $newCity['user_added'] = '1';

            if ($this->basepackages->geoCities->add($newCity)) {
                $newAddress['city_id'] = $this->basepackages->geoCities->packagesData->last['id'];
                $newAddress['city_name'] = $newCity['name'];
            } else {

                $this->errors = array_merge($this->errors, ['Could not add city data.']);
            }
        }

        $newAddress['seq'] = 0;
        $newAddress['new'] = 1;
        $newAddress['name'] = $po['AttentionTo'];
        $newAddress['package_name'] = 'purchaseorders';
        $newAddress['attention_to'] = $po['AttentionTo'];
        $newAddress['street_address'] = $street_address;
        $newAddress['street_address_2'] = $street_address_2;

        $this->errors = array_merge($this->errors, ['We have tried to match the delivery address. Still, please check address and verify with Xero PO.']);

        $this->basepackages->addressbook->addAddress($newAddress);

        if ($this->basepackages->addressbook->packagesData->last) {
            return $this->basepackages->addressbook->packagesData->last['id'];
        }

        return '0';
    }

    protected function addPoAttachments($po, $purchaseOrder)
    {
        $model = SystemApiXeroAttachments::class;

        $xeroAttachment = $model::find(
            [
                'conditions'    => 'baz_storage_local_id IS NULL AND xero_package = :xp: AND xero_package_row_id = :xpri:',
                'bind'          =>
                    [
                        'xp'    => 'purchaseorders',
                        'xpri'  => $po['PurchaseOrderID']
                    ]
            ]
        );

        if ($xeroAttachment) {
            $attachments = $xeroAttachment->toArray();

            if (count($attachments) > 0) {

                $request = new GetPurchaseOrderAttachmentByIdRestRequest;

                $poAttachments = [];

                foreach ($attachments as $attachmentKey => $attachment) {
                    $request->PurchaseOrderID = $attachment['xero_package_row_id'];

                    $request->AttachmentID = $attachment['AttachmentID'];

                    $response = $this->xeroApi->getPurchaseOrderAttachmentById($request);

                    if ($response) {
                        $this->api->refreshXeroCallStats($response->getHeaders());

                        if ($response->getStatusCode() === 200) {
                            $storageId = $this->addAttachmentToStorage($attachment, $po, $purchaseOrder, $response);

                            if ($storageId) {

                                array_push($poAttachments, $storageId['uuid']);

                                $xA = $model::findFirst(
                                    [
                                        'conditions'    => 'AttachmentID = :aid:',
                                        'bind'          =>
                                            [
                                                'aid'   => $attachment['AttachmentID']
                                            ]
                                    ]
                                );

                                if ($xA) {
                                    $xA->baz_storage_local_id = $storageId['id'];

                                    $xA->update();

                                    $this->basepackages->storages->changeOrphanStatus($storageId['uuid'], null, false, 0);
                                }
                            }
                        }
                    }
                }

                return $poAttachments;
            }
        }

        return [];
    }

    protected function addAttachmentToStorage($attachment, $po, $purchaseOrder, $response)
    {
        if ($this->basepackages->storages->storeFile(
                'private',
                'purchaseorders',
                $response->getBody()->getContents(),
                $attachment['FileName'],
                $attachment['ContentLength'],
                $attachment['MimeType'],
            )
        ) {
            return $this->basepackages->storages->packagesData->storageData;
        }

        return false;
    }

    protected function generatePurchaseOrderProducts($po, $purchaseOrderId)
    {
        $totalQty = 0;

        foreach ($po['products'] as $productKey => $product) {
            $newItem['purchase_order_id'] = $purchaseOrderId;
            $newItem['seq'] = $productKey;

            if (!$product['ItemCode'] || $product['ItemCode'] === '') {
                $newItem['mpn'] = '0000';
                $this->errors = array_merge($this->errors, ['Product MPN missing for purchase order - ' . $purchaseOrderId]);
            } else {
                $newItem['mpn'] = $product['ItemCode'];
            }

            if ($product['Description'] === '') {
                $newItem['product_title'] = '000';
                $this->errors = array_merge($this->errors, ['Product Title missing for purchase order - ' . $purchaseOrderId]);
            } else {
                $newItem['product_title'] = $product['Description'];
            }

            $newItem['use_vendor_tax'] = 'false';

            $newItem['tax'] = '0';
            $newItem['tax_rate'] = '0';

            $taxGroupsModel = new BusinessFinancesTaxGroups;

            if ($product['TaxType'] === 'OUTPUT') {
                $taxGroupObj = $taxGroupsModel::findFirstByName('GST on Income');
                $newItem['tax'] = 'GST on Income';
            } else if ($product['TaxType'] === 'INPUT') {
                $taxGroupObj = $taxGroupsModel::findFirstByName('GST on Expenses');
                $newItem['tax'] = 'GST on Expenses';
            } else if ($product['TaxType'] === 'EXEMPTEXPENSES') {
                $taxGroupObj = $taxGroupsModel::findFirstByName('GST Free Expenses');
                $newItem['tax'] = 'GST Free Expenses';
            } else if ($product['TaxType'] === 'EXEMPTOUTPUT') {
                $taxGroupObj = $taxGroupsModel::findFirstByName('GST Free Income');
                $newItem['tax'] = 'GST Free Income';
            } else if ($product['TaxType'] === 'BASEXCLUDED') {
                $taxGroupObj = $taxGroupsModel::findFirstByName('BAS Excluded');
                $newItem['tax'] = 'BAS Excluded';
            } else if ($product['TaxType'] === 'GSTONIMPORTS') {
                $taxGroupObj = $taxGroupsModel::findFirstByName('GST on Imports');
                $newItem['tax'] = 'GST on Imports';
            }

            if ($taxGroupObj) {
                $newItem['tax_rate'] = $taxGroupObj->id;
            }

            if ($newItem['tax_rate'] === '0') {
                $this->errors = array_merge($this->errors, ['Tax missing for a product purchase order - ' . $purchaseOrderId]);
            }

            $newItem['use_vendor_discount'] = 'false';
            $newItem['product_discount_rate'] = $product['DiscountRate'];

            if ($product['DiscountRate'] && $product['DiscountRate'] !== '') {
                $newItem['product_discount'] = $product['DiscountRate'] . '%';
            }

            $newItem['product_qty'] = $product['Quantity'];
            $totalQty = $totalQty + (int) $product['Quantity'];

            $newItem['product_unit_price'] = $product['UnitAmount'];

            $newItem['product_amount'] = $product['LineAmount'];

            if ($po['LineAmountTypes'] === '') {
                $newItem['product_unit_price_incl_tax'] = 'true';
            } else {
                $newItem['product_unit_price_incl_tax'] = 'false';
            }

            $poProductModel = ImsStockPurchaseOrdersProducts::class;

            $poProductObj = $poProductModel::findFirst(
                [
                    'conditions'    => 'purchase_order_id = :poid: AND mpn = :mpn:',
                    'bind'          =>
                        [
                            'mpn'   => $newItem['mpn'],
                            'poid'  => $newItem['purchase_order_id']
                        ]
                ]
            );

            if ($poProductObj) {
                $poProductObj->assign($newItem);

                $poProductObj->update();
            } else {
                $newProductObj = new $poProductModel;

                $newProductObj->assign($newItem);

                $newProductObj->create();
            }
        }

        return $totalQty;
    }

    protected function addPurchaseOrderHistory($po, $purchaseOrder)
    {
        $model = SystemApiXeroHistory::class;

        $xeroHistory = $model::find(
            [
                'conditions'    => 'baz_note_id IS NULL AND xero_package = :xp: AND xero_package_row_id = :xpri:',
                'bind'          =>
                    [
                        'xp'    => 'purchaseorders',
                        'xpri'  => $po['PurchaseOrderID']
                    ]
            ]
        );

        if ($xeroHistory) {
            $histories = $xeroHistory->toArray();

            if (count($histories) > 0) {
                foreach ($histories as $historyKey => $history) {

                    $note = $this->addHistoryToNote($history, $purchaseOrder);

                    if ($note) {
                        $xH = $model::findFirstById($history['id']);

                        if ($xH) {
                            $xH->baz_note_id = $note['id'];

                            $xH->update();
                        }
                    }
                }
            }
        }
    }

    protected function addHistoryToNote($history, $purchaseOrder)
    {
        $newNote['package_row_id'] = $purchaseOrder['id'];
        $newNote['note_type'] = '1';
        $newNote['note_app_visibility']['data'] = [];
        $newNote['is_private'] = '0';
        $newNote['note'] =
            'Added via Xero API.' .
            '<br>Change Type: ' . $history['Changes'] .
            '<br>Created At: ' . \DateTime::createFromFormat('Y-m-d\TH:i:s', $history['DateUTCString'])->format('Y-m-d H:i:s') .
            '<br>Details: ' . $history['Details'];

        $this->basepackages->notes->addNote('purchaseorders', $newNote);

        if ($this->basepackages->notes->packagesData->last) {
            return $this->basepackages->notes->packagesData->last;
        }

        return false;
    }
}