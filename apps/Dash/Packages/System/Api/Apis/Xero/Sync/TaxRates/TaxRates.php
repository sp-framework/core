<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\TaxRates;

use Apps\Dash\Packages\Business\Finances\TaxGroups\Model\BusinessFinancesTaxGroups;
use Apps\Dash\Packages\Business\Finances\TaxGroups\TaxGroups;
use Apps\Dash\Packages\Business\Finances\Taxes\Model\BusinessFinancesTaxes;
use Apps\Dash\Packages\Business\Finances\Taxes\Taxes;
use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\TaxRates\Model\SystemApiXeroTaxRates;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetTaxRatesRestRequest;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class TaxRates extends BasePackage
{
    protected $taxGroupsPackage;

    protected $taxesPackage;

    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    public function sync($apiId = null)
    {
        $this->apiPackage = new Api;

        $this->request = new GetTaxRatesRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if ($xeroApis && count($xeroApis) > 0) {
            if (!$apiId) {
                foreach ($xeroApis as $key => $xeroApi) {
                    $this->syncWithXero($xeroApi['api_id']);
                }
            } else {
                $this->syncWithXero($apiId);
            }

            $this->syncWithLocal();
        } else {
            $this->addResponse('Sync Error. No API Configuration Found', 1);
        }
    }

    protected function syncWithXero($apiId)
    {
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->xeroApi = $this->api->useService('XeroAccountingApi');

        $response = $this->xeroApi->getTaxRates($this->request);

        $responseArr = $response->toArray();

        $this->api->refreshXeroCallStats($response->getHeaders());

        if ((isset($responseArr['Status']) && $responseArr['Status'] === 'OK') &&
            isset($responseArr['TaxRates'])
        ) {
            if (count($responseArr['TaxRates']) > 0) {
                $this->addUpdateXeroTaxRates($apiId, $responseArr['TaxRates']);
            }
        }
    }

    protected function addUpdateXeroTaxRates($apiId, $taxRates)
    {
        foreach ($taxRates as $taxRateKey => $taxRate) {
            $model = SystemApiXeroTaxRates::class;

            $xeroTaxRate = $model::findFirst(
                [
                    'conditions'    => 'Name = :name:',
                    'bind'          =>
                        [
                            'name'  => $taxRate['Name']
                        ]
                ]
            );

            $taxRate['api_id'] = $apiId;

            if (!$xeroTaxRate) {
                $modelToUse = new $model();

                $modelToUse->assign($this->jsonData($taxRate));

                $modelToUse->create();

                $thisItem = $modelToUse->toArray();
            } else {
                if ($xeroTaxRate->baz_tax_group_id) {
                    $taxRate['resync_local'] = '1';
                }

                $xeroTaxRate->assign($this->jsonData($taxRate));

                $xeroTaxRate->update();

                $thisItem = $xeroTaxRate->toArray();
            }
        }
    }

    public function syncWithLocal()
    {
        $model = SystemApiXeroTaxRates::class;

        $xeroTaxRates = $model::find(
            [
                'conditions'    => 'baz_tax_group_id IS NULL OR resync_local = :rl:',
                'bind'          =>
                    [
                        'rl'    => '1',
                    ]
            ]
        );

        if ($xeroTaxRates) {
            $this->taxGroupsPackage = $this->usePackage(TaxGroups::class);

            $this->taxesPackage = $this->usePackage(Taxes::class);

            $xTaxGroups = $xeroTaxRates->toArray();

            if ($xTaxGroups && count($xTaxGroups) > 0) {
                foreach ($xTaxGroups as $xTaxGroupKey => $xTaxGroup) {
                    $this->generateTaxGroupData($xTaxGroup);
                }
            }
        }
    }

    protected function generateTaxGroupData($xTaxGroup)
    {
        if ($xTaxGroup['TaxComponents'] && $xTaxGroup['TaxComponents'] !== '') {
            $xTaxGroup['TaxComponents'] = Json::decode($xTaxGroup['TaxComponents'], true);
        }

        $taxGroupsModel = BusinessFinancesTaxGroups::class;

        $taxGroup = $taxGroupsModel::findFirstByName($xTaxGroup['Name']);

        if (!$taxGroup) {
            $newTaxGroup['name'] = $xTaxGroup['Name'];
            $newTaxGroup['description'] = 'Added via Xero API.';

            if ($this->taxGroupsPackage->add($newTaxGroup)) {
                if ($this->taxGroupsPackage->packagesData->last) {
                    $tGId = $this->taxGroupsPackage->packagesData->last['id'];
                }
            }
        } else {
            $tGId = $taxGroup->id;
        }
        if (is_array($xTaxGroup['TaxComponents']) && count($xTaxGroup['TaxComponents']) > 0) {
            $this->generateTaxData($xTaxGroup, $tGId);
        }
    }

    protected function generateTaxData($xTaxGroup, $tGId)
    {
        $taxesModel = BusinessFinancesTaxes::class;

        foreach ($xTaxGroup['TaxComponents'] as $xTaxKey => $xTax) {
            $tax = $taxesModel::findFirst(
                [
                    'conditions'        => 'name = :name: AND amount = :amount: AND tax_group_id = :tgid:',
                    'bind'              =>
                        [
                            'name'      => $xTax['Name'],
                            'amount'    => $xTax['Rate'],
                            'tgid'      => $tGId
                        ]
                ]
            );

            if (!$tax) {
                $newTax['name'] = $xTax['Name'];
                $newTax['amount'] = $xTax['Rate'];
                $newTax['tax_group_id'] = $tGId;
                $newTax['description'] = 'Added via Xero API.';

                $this->taxesPackage->add($newTax);
            }
        }
    }
}