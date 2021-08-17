<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Model\SystemApiXeroOrganisations;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Model\SystemApiXeroOrganisationsAddresses;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Model\SystemApiXeroOrganisationsFinance;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Model\SystemApiXeroOrganisationsPhones;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetOrganisationsRestRequest;
use System\Base\BasePackage;

class Organisations extends BasePackage
{
    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    public function sync($apiId = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetOrganisationsRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if (!$apiId) {
            foreach ($xeroApis as $key => $xeroApi) {
                $this->syncWithXero($xeroApi['api_id']);
            }
        } else {
            $this->syncWithXero($apiId);
        }
    }

    protected function syncWithXero($apiId)
    {
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        $response = $this->xeroApi->getOrganisations($this->request);

        $this->api->refreshXeroCallStats($response->getHeaders());

        $responseArr = $response->toArray();

        if ((isset($responseArr['Status']) && $responseArr['Status'] === 'OK') &&
            isset($responseArr['Organisations'])
        ) {
            if (count($responseArr['Organisations']) > 0) {
                $this->addUpdateXeroOrganisations($apiId, $responseArr['Organisations']);
            }
        }
    }

    public function addUpdateXeroOrganisations($apiId, array $organisations)
    {
        if (count($organisations) > 0) {
            foreach ($organisations as $organisationKey => $organisation) {
                $model = SystemApiXeroOrganisations::class;

                $xeroOrganisation = $model::findFirst(
                    [
                        'conditions'    => 'OrganisationID = :aid:',
                        'bind'          =>
                            [
                                'aid'   => $organisation['OrganisationID']
                            ]
                    ]
                );

                $organisation['api_id'] = $apiId;

                if (!$xeroOrganisation) {
                    $modelToUse = new $model();

                    $modelToUse->assign($this->jsonData($organisation));

                    $modelToUse->create();

                    $thisOrganisation = $modelToUse->toArray();
                } else {
                    $xeroOrganisation->assign($this->jsonData($organisation));

                    $xeroOrganisation->update();

                    $thisOrganisation = $xeroOrganisation->toArray();
                }

                if (isset($organisation['Addresses']) && count($organisation['Addresses']) > 0) {
                    $this->addUpdateXeroOrganisationsAddresses($thisOrganisation, $organisation['Addresses']);
                }

                if (isset($organisation['Phones']) && count($organisation['Phones']) > 0) {
                    $this->addUpdateXeroOrganisationsPhones($thisOrganisation, $organisation['Phones']);
                }

                $this->addUpdateXeroOrganisationsFinance($thisOrganisation, $organisation);
            }
        }
    }

    protected function addUpdateXeroOrganisationsAddresses($organisation, $addresses)
    {
        $model = SystemApiXeroOrganisationsAddresses::class;

        foreach ($addresses as $addressKey => $address) {
            $xeroOrganisationAddress = $model::findFirst(
                [
                    'conditions'    => 'OrganisationID = :cid: AND AddressType = :at:',
                    'bind'          =>
                        [
                            'cid'   => $organisation['OrganisationID'],
                            'at'    => $address['AddressType']
                        ]
                ]
            );

            $address['OrganisationID'] = $organisation['OrganisationID'];

            if (!$xeroOrganisationAddress) {
                $modelToUse = new $model();

                $modelToUse->assign($address);

                $modelToUse->create();
            } else {
                $xeroOrganisationAddress->assign($address);

                $xeroOrganisationAddress->update();
            }
        }
    }

    protected function addUpdateXeroOrganisationsPhones($organisation, $phones)
    {
        $model = SystemApiXeroOrganisationsPhones::class;

        foreach ($phones as $phoneKey => $phone) {
            $xeroOrganisationPhone = $model::findFirst(
                [
                    'conditions'    => 'OrganisationID = :cid: AND PhoneType = :pt:',
                    'bind'          =>
                        [
                            'cid'   => $organisation['OrganisationID'],
                            'pt'    => $phone['PhoneType']
                        ]
                ]
            );

            $phone['OrganisationID'] = $organisation['OrganisationID'];

            if (!$xeroOrganisationPhone) {
                $modelToUse = new $model();

                $modelToUse->assign($phone);

                $modelToUse->create();
            } else {
                $xeroOrganisationPhone->assign($phone);

                $xeroOrganisationPhone->update();
            }
        }
    }

    protected function addUpdateXeroOrganisationsFinance($organisation, $finance)
    {
        $model = SystemApiXeroOrganisationsFinance::class;

        $xeroOrganisationFinance = $model::findFirst(
            [
                'conditions'    => 'OrganisationID = :cid:',
                'bind'          =>
                    [
                        'cid'   => $organisation['OrganisationID']
                    ]
            ]
        );

        $finance['OrganisationID'] = $organisation['OrganisationID'];

        if (isset($finance['PaymentTerms']) && count($finance['PaymentTerms']) > 0) {
            if (isset($finance['PaymentTerms']['Bills'])) {
                $finance['PaymentTermsBillsDay'] = $finance['PaymentTerms']['Bills']['Day'];
                $finance['PaymentTermsBillsType'] = $finance['PaymentTerms']['Bills']['Type'];
            }
            if (isset($finance['PaymentTerms']['Sales'])) {
                $finance['PaymentTermsSalesDay'] = $finance['PaymentTerms']['Sales']['Day'];
                $finance['PaymentTermsSalesType'] = $finance['PaymentTerms']['Sales']['Type'];
            }
        }

        if (!$xeroOrganisationFinance) {
            $modelToUse = new $model();

            $modelToUse->assign($this->jsonData($finance));

            $modelToUse->create();
        } else {
            $xeroOrganisationFinance->assign($this->jsonData($finance));

            $xeroOrganisationFinance->update();
        }
    }

    public function reSync()
    {
        //
    }

    public function syncWithLocal()
    {
        //Addressess
        //POBOX = Entity Address
        //STREET = New Location
        //SELIVERY = New location
    }
}