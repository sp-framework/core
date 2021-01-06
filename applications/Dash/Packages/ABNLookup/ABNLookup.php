<?php

namespace Applications\Dash\Packages\ABNLookup;

use System\Base\BasePackage;

class ABNLookup extends BasePackage
{
    protected $abn;

    protected $guid = 'd1ae7c59-fa67-4fbb-9154-b73b6f088e82';

    protected $url = 'https://abr.business.gov.au/abrxmlsearch/AbrXmlSearch.asmx/SearchByABNv202001';

    protected $getHistoricalData = 'N';

    protected $packageName = 'abnlookup';

    public $abnlookup;

    public function lookupABN(int $abn, string $historicalData = 'N')
    {
        $this->getHistoricalData = $historicalData;

        $this->abn = $abn;

        if ($this->abn) {
            $response = $this->remoteContent->request(
                'POST',
                $this->url,
                [
                    'form_params'   =>
                    [
                        'searchString'              => $abn,
                        'includeHistoricalDetails'  => $this->getHistoricalData,
                        'authenticationGuid'        => $this->guid
                    ]
                ]
            );

            $businessDetails = xmlToArray($response->getBody()->getContents(), 'string');

            $this->packagesData->businessDetails = $businessDetails['response'];

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Business details found.';

            return true;
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'ABN cannot be empty.';
        }
    }
}