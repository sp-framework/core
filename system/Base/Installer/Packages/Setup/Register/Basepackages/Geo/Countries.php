<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Geo;

use Phalcon\Db\Enum;

class Countries
{
    public $trackCounter;

    public $progress;

    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/Geo/';

    protected $countryStore;

    protected $regionStore;

    public function register($db, $ff, $localContent, $helper)
    {
        if (!is_dir(base_path($this->sourceDir))) {
            if (!mkdir(base_path($this->sourceDir), 0777, true)) {
                $this->addResponse('Unable to create Geo directory', 1);

                return false;
            }
        }

        $countries = $helper->decode($localContent->read($this->sourceDir . 'AllCountries.json'), true);
        $this->countryStore = $ff->store('basepackages_geo_countries');
        $this->regionStore = $ff->store('basepackages_geo_regions');

        foreach ($countries as $key => $country) {
            $countryToInsert =
                [
                    'id'                => $country['id'],
                    'name'              => $country['name'],
                    'native'            => $country['native'],
                    'nationality'       => $country['nationality'],
                    'capital'           => $country['capital'],
                    'iso2'              => $country['iso2'],
                    'iso3'              => $country['iso3'],
                    'currency'          => $country['currency'],
                    'currency_name'     => $country['currency_name'],
                    'currency_symbol'   => $country['currency_symbol'],
                    'currency_enabled'  => 0,
                    'region_id'         => $country['region_id'],
                    'region'            => $country['region'],
                    'subregion_id'      => $country['subregion_id'],
                    'subregion'         => $country['subregion'],
                    'numeric_code'      => $country['numeric_code'],
                    'phone_code'        => $country['phonecode'],
                    'tld'               => $country['tld'],
                    'emoji'             => $country['emoji'],
                    'emojiU'            => $country['emojiU'],
                    'latitude'          => (int) $country['latitude'],
                    'longitude'         => (int) $country['longitude'],
                    'translations'      => $helper->encode($country['translations']),
                    'installed'         => 0,
                    'enabled'           => 0
                ];

            if ($db) {
                $db->insertAsDict('basepackages_geo_countries', $countryToInsert);
            }

            if ($ff) {
                $this->countryStore->updateOrInsert($countryToInsert, false);
            }

            if (strlen($country['region']) > 0 &&
                strlen($country['subregion']) > 0
            ) {
                $this->checkRegion($db, $ff, $country);
            }
        }

        return true;
    }

    protected function checkRegion($db, $ff, $country)
    {
        $subregion = false;

        if ($ff) {
            $subregion = $this->regionStore->findById($country['subregion_id']);
        }

        if ($db) {
            $subregion =
                $db->fetchAll(
                    "SELECT * FROM basepackages_geo_regions WHERE id LIKE :id",
                    Enum::FETCH_ASSOC,
                    [
                        "id" => $country['subregion_id'],
                    ]
                );

            if (isset($subregion[0])) {
                $subregion = $subregion[0];
            } else {
                $subregion = false;
            }
        }

        if (!$subregion) {
            $newSubRegion['id'] = $country['subregion_id'];
            $newSubRegion['name'] = $country['subregion'];
            $newSubRegion['parent_region_id'] = $country['region_id'];

            if ($ff) {
                $this->regionStore->updateOrInsert($newSubRegion, false);
            }

            if ($db) {
                $db->insertAsDict('basepackages_geo_regions', $newSubRegion);
            }
        }

        $region = false;

        if ($ff) {
            $region = $this->regionStore->findById($country['region_id']);
        }

        if ($db) {
            $region =
                $db->fetchAll(
                    "SELECT * FROM basepackages_geo_regions WHERE id LIKE :id",
                    Enum::FETCH_ASSOC,
                    [
                        "id" => $country['region_id'],
                    ]
                );

            if (isset($region[0])) {
                $region = $region[0];
            } else {
                $region = false;
            }
        }

        if (!$region) {
            $newRegion['id'] = $country['region_id'];
            $newRegion['name'] = $country['region'];
            $newRegion['parent_region_id'] = null;

            if ($ff) {
                $this->regionStore->updateOrInsert($newRegion, false);
            }

            if ($db) {
                $db->insertAsDict('basepackages_geo_regions', $newRegion);
            }
        }
    }
}