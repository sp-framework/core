<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoPostcodes;
use System\Base\BasePackage;

class GeoPostcodes extends BasePackage
{
    protected $modelToUse = BasepackagesGeoPostcodes::class;

    protected $packageName = 'geoPostcodes';

    public $geoPostcodes;

    protected $countries = [];

    protected $states = [];

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('geoPostcodes', 'core')) {
                $this->geoPostcodes = $this->opCache->getCache('geoPostcodes', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('geoPostcodes', $this->geoPostcodes, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

    public function searchPostcodesByName(string $postcodeQueryString)
    {
        if ($this->config->databasetype === 'db') {
            $searchPostcodes = $this->getByParams(
                [
                    'conditions'    => 'name LIKE :cName:',
                    'bind'          => [
                        'cName'     => '%' . $postcodeQueryString . '%'
                    ]
                ]
            );
        } else {
            $searchPostcodes = $this->getByParams(['conditions' => ['name', 'LIKE', '%' . $postcodeQueryString . '%']]);
        }

        $postcodes = [];

        if ($searchPostcodes) {
            foreach ($searchPostcodes as $postCodeKey => $postCodeValue) {
                if (!isset($this->countries[$postCodeValue['country_id']])) {
                    $this->countries[$postCodeValue['country_id']] = $this->basepackages->geoCountries->getById($postCodeValue['country_id']);
                }

                if ($this->countries[$postCodeValue['country_id']]['enabled'] == 1 && $this->countries[$postCodeValue['country_id']]['installed'] == 1) {
                    $postcodes[$postCodeKey] = $postCodeValue;
                    if (!isset($this->states[$postCodeValue['state_id']])) {
                        $this->states[$postCodeValue['state_id']] = $this->basepackages->geoStates->getById($postCodeValue['state_id']);

                        if (!$this->states[$postCodeValue['state_id']]) {
                            continue;
                        }
                    }

                    $postcodes[$postCodeKey]['state_id'] = $this->states[$postCodeValue['state_id']]['id'];
                    $postcodes[$postCodeKey]['state_name'] = $this->states[$postCodeValue['state_id']]['name'];
                    $postcodes[$postCodeKey]['country_id'] = $this->countries[$postCodeValue['country_id']]['id'];
                    $postcodes[$postCodeKey]['country_name'] = $this->countries[$postCodeValue['country_id']]['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['postcodes' => $postcodes]);

        return $postcodes;
    }

    public function searchPostcodesByCountryId($countryId)
    {
        if ($this->config->databasetype === 'db') {
            $searchPostcodes =
                $this->getByParams(
                    [
                        'conditions'    => 'country_id = :countryId:',
                        'bind'          => [
                            'countryId'     => $countryId
                        ]
                    ]
                );
        } else {
            $searchPostcodes = $this->getByParams(['conditions' => ['country_id', '=', (int) $countryId]]);
        }

        $this->addResponse('Ok', 0, ['postcodes' => $searchPostcodes]);

        return $searchPostcodes;
    }

    public function searchPostCodes(string $postCodeQueryString)
    {
        if ($this->config->databasetype === 'db') {
            $searchPostCodes = $this->getByParams(
                [
                    'conditions'    => 'code LIKE :cPostCode:',
                    'bind'          => [
                        'cPostCode'     => '%' . $postCodeQueryString . '%'
                    ]
                ]
            );
        } else {
            $searchPostCodes = $this->getByParams(['conditions' => ['code', 'LIKE', '%' . $postCodeQueryString . '%']]);
        }

        $postCodes = [];

        if ($searchPostCodes) {
            foreach ($searchPostCodes as $postCodeKey => $postCodeValue) {
                if (!isset($this->countries[$postCodeValue['country_id']])) {
                    $this->countries[$postCodeValue['country_id']] = $this->basepackages->geoCountries->getById($postCodeValue['country_id']);
                }

                if ($this->countries[$postCodeValue['country_id']]['enabled'] == 1 && $this->countries[$postCodeValue['country_id']]['installed'] == 1) {
                    $postCodes[$postCodeKey] = $postCodeValue;
                    if (!isset($this->states[$postCodeValue['state_id']])) {
                        $this->states[$postCodeValue['state_id']] = $this->basepackages->geoStates->getById($postCodeValue['state_id']);

                        if (!$this->states[$postCodeValue['state_id']]) {
                            continue;
                        }
                    }

                    $postCodes[$postCodeKey]['state_id'] = $this->states[$postCodeValue['state_id']]['id'];
                    $postCodes[$postCodeKey]['state_name'] = $this->states[$postCodeValue['state_id']]['name'];
                    $postCodes[$postCodeKey]['country_id'] = $this->countries[$postCodeValue['country_id']]['id'];
                    $postCodes[$postCodeKey]['country_name'] = $this->countries[$postCodeValue['country_id']]['name'];
                }
            }
        }

        $this->addResponse('Ok', 0, ['postCodes' => $postCodes]);

        return $postCodes;
    }

    public function addPostcode(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('Postcode added');

            return true;
        }

        $this->addResponse('Error Adding Postcode', 1);
    }

    public function updatePostcode(array $data)
    {
        $city = $this->getById($data['id']);

        if (!$city) {
            $this->addResponse('Postcode with ID does not exists', 1);

            return;
        }

        if ($this->update($data)) {
            $this->addResponse('Postcode updated');

            return true;
        }

        $this->addResponse('Error Updating Postcode', 1);
    }
}