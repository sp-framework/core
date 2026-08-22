<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package System\Base\Installer\Packages\Setup\Register\Basepackages\Geo
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Geo;

use Phalcon\Db\Enum;

/**
 * Seeds ISO country definitions, regions, and currency settings.
 *
 * @package System\Base\Installer\Packages\Setup\Register\Basepackages\Geo
 */
class Countries
{
    /**
     * Geo source data directory path relative to framework root.
     *
     * @var string
     */
    protected string $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/Geo/';

    /**
     * FlatFile country store instance.
     *
     * @var mixed
     */
    protected mixed $countryStore = null;

    /**
     * FlatFile region store instance.
     *
     * @var mixed
     */
    protected mixed $regionStore = null;

    /**
     * Registers country and region dataset into database and FlatFile stores.
     *
     * @param mixed $db           PDO database connection adapter.
     * @param mixed $ff           FlatFile database manager.
     * @param mixed $localContent Flysystem local file storage adapter.
     * @param mixed $helper       Helpers service instance.
     *
     * @return bool True on success, false on error.
     */
    public function register(mixed $db, mixed $ff, mixed $localContent, mixed $helper): bool
    {
        $dir = base_path($this->sourceDir);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                return false;
            }
        }

        $filePath = $this->sourceDir . 'AllCountries.json';
        if ($localContent) {
            $countries = $helper->decode($localContent->read($filePath), true);
        } else {
            $countries = [];
        }

        if ($ff) {
            $this->countryStore = $ff->store('basepackages_geo_countries');
            $this->regionStore = $ff->store('basepackages_geo_regions');
        }

        if (is_array($countries)) {
            foreach ($countries as $country) {
                $countryToInsert = [
                    'id'               => $country['id'] ?? null,
                    'name'             => $country['name'] ?? '',
                    'native'           => $country['native'] ?? '',
                    'nationality'      => $country['nationality'] ?? '',
                    'capital'          => $country['capital'] ?? '',
                    'iso2'             => $country['iso2'] ?? '',
                    'iso3'             => $country['iso3'] ?? '',
                    'currency'         => $country['currency'] ?? '',
                    'currency_name'    => $country['currency_name'] ?? '',
                    'currency_symbol'  => $country['currency_symbol'] ?? '',
                    'currency_enabled' => 0,
                    'region_id'        => $country['region_id'] ?? null,
                    'region'           => $country['region'] ?? '',
                    'subregion_id'     => $country['subregion_id'] ?? null,
                    'subregion'        => $country['subregion'] ?? '',
                    'numeric_code'     => $country['numeric_code'] ?? '',
                    'phone_code'       => $country['phonecode'] ?? '',
                    'tld'              => $country['tld'] ?? '',
                    'emoji'            => $country['emoji'] ?? '',
                    'emojiU'           => $country['emojiU'] ?? '',
                    'latitude'         => (int) ($country['latitude'] ?? 0),
                    'longitude'        => (int) ($country['longitude'] ?? 0),
                    'translations'     => isset($country['translations']) ? $helper->encode($country['translations']) : $helper->encode([]),
                    'installed'        => 0,
                    'enabled'          => 0
                ];

                if ($db) {
                    $db->insertAsDict('basepackages_geo_countries', $countryToInsert);
                }

                if ($ff) {
                    $this->countryStore->updateOrInsert($countryToInsert);
                }

                if (strlen($country['region']) > 0 &&
                    strlen($country['subregion']) > 0
                ) {
                    $this->checkRegion($db, $ff, $country);
                }
            }
        }

        return true;
    }

    /**
     * Checks and registers geographic regions and subregions.
     *
     * @param mixed                $db              PDO database connection adapter.
     * @param mixed                $ff              FlatFile database manager.
     * @param array<string, mixed> $country         Country data structure.
     *
     * @return void
     */
    protected function checkRegion(mixed $db, mixed $ff, array $country): void
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