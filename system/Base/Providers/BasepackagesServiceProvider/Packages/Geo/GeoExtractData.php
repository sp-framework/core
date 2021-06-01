<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class GeoExtractData extends BasePackage
{
    protected $sourceFile;

    public function extractData(string $sourceFile)
    {
        $this->sourceFile = $sourceFile;

        $sourceFileArr = explode('/', trim($sourceFile, '/'));

        unset($sourceFileArr[Arr::lastKey($sourceFileArr)]);

        $sourceFilePath = implode('/', $sourceFileArr) . '/';

        $countriesArr = Json::decode($this->localContent->read($this->sourceFile), true);

        $countries = [];
        if ($countriesArr && is_array($countriesArr)) {
            foreach ($countriesArr as $key => &$country) {
                $countries[$country['iso3']] = $country;
                unset($country['states']);
            }
        }

        foreach ($countries as $key => $value) {
            $this->localContent->write($sourceFilePath . $key . '.json', JSON::encode($value));
        }

        $this->localContent->write($sourceFilePath . 'AllCountries.json', JSON::encode($countriesArr));

        $this->packagesData->responseMessage = 'Data Extracted Successfully!';

    }
}