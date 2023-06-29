<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Geo;

use Phalcon\Helper\Json;

class Timezones
{
    public function register($db, $ff, $localContent)
    {
        $timezonesData =
            Json::decode(
                $localContent->read(
                    '/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/TimeZones.json'
                ),
                true
            );

        foreach ($timezonesData as $key => $timezone) {
            $zone =
                [
                    'zone_name'             => isset($timezone['zoneName']) ? $timezone['zoneName'] : null,
                    'tz_name'               => isset($timezone['tzName']) ? $timezone['tzName'] : null,
                    'gmt_offset'            => isset($timezone['gmtOffset']) ? $timezone['gmtOffset'] : null,
                    'gmt_offset_name'       => isset($timezone['gmtOffsetName']) ? $timezone['gmtOffsetName'] : null,
                    'gmt_offset_dst'        => isset($timezone['gmtOffsetDST']) ? $timezone['gmtOffsetDST'] : null,
                    'gmt_offset_name_dst'   => isset($timezone['gmtOffsetNameDST']) ? $timezone['gmtOffsetNameDST'] : null,
                    'abbreviation'          => isset($timezone['abbreviation']) ? $timezone['abbreviation'] : null
                ];

            if ($db) {
                $db->insertAsDict('basepackages_geo_timezones', $zone);
            }

            if ($ff) {
                $zoneStore = $ff->store('basepackages_geo_timezones');

                $zoneStore->updateOrInsert($zone);
            }
        }

        return true;
    }
}