<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages\Geo
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Geo;

/**
 * Seeds global timezone records into basepackages_geo_timezones.
 */
class Timezones
{
    /**
     * Timezones data file path relative to framework root.
     *
     * @var string
     */
    protected string $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/Geo/';

    /**
     * Registers timezones dataset into database and FlatFile stores.
     *
     * @param mixed $db           PDO database connection adapter.
     * @param mixed $ff           FlatFile database manager.
     * @param mixed $localContent Flysystem local file storage adapter.
     * @param mixed $helper       Helpers service instance.
     *
     * @return bool True on success.
     */
    public function register(mixed $db, mixed $ff, mixed $localContent, mixed $helper): bool
    {
        $filePath = $this->sourceDir . 'TimeZones.json';
        if ($localContent) {
            $timezonesData = $helper->decode($localContent->read($filePath), true);
        } else {
            $timezonesData = [];
        }

        if (is_array($timezonesData)) {
            foreach ($timezonesData as $timezone) {
                $zone = [
                    'zone_name'           => $timezone['zoneName'] ?? null,
                    'tz_name'             => $timezone['tzName'] ?? null,
                    'gmt_offset'          => $timezone['gmtOffset'] ?? null,
                    'gmt_offset_name'     => $timezone['gmtOffsetName'] ?? null,
                    'abbreviation'        => $timezone['abbreviation'] ?? null,
                    'gmt_offset_dst'      => $timezone['gmtOffsetDST'] ?? null,
                    'gmt_offset_name_dst' => $timezone['gmtOffsetNameDST'] ?? null,
                    'abbreviation_dst'    => $timezone['abbreviationDST'] ?? null
                ];

                if ($db) {
                    $db->insertAsDict('basepackages_geo_timezones', $zone);
                }

                if ($ff) {
                    $zoneStore = $ff->store('basepackages_geo_timezones');

                    $zoneStore->updateOrInsert($zone);
                }
            }
        }

        return true;
    }
}