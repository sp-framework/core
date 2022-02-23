<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Geo;

use Phalcon\Helper\Json;

class Timezones
{
    protected $db;

    public function register($db, $localContent)
    {
        $this->db = $db;

        $timezonesData =
            Json::decode(
                $localContent->read(
                    '/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/TimeZones.json'
                ),
                true
            );

        $this->registerTimezones($timezonesData);
    }

    protected function registerTimezones($timezonesData)
    {
        foreach ($timezonesData as $key => $timezone) {
            $this->db->insertAsDict(
                'basepackages_geo_timezones',
                [
                    'zone_name'             => isset($timezone['zoneName']) ? $timezone['zoneName'] : null,
                    'tz_name'               => isset($timezone['tzName']) ? $timezone['tzName'] : null,
                    'gmt_offset'            => isset($timezone['gmtOffset']) ? $timezone['gmtOffset'] : null,
                    'gmt_offset_name'       => isset($timezone['gmtOffsetName']) ? $timezone['gmtOffsetName'] : null,
                    'gmt_offset_dst'        => isset($timezone['gmtOffsetDST']) ? $timezone['gmtOffsetDST'] : null,
                    'gmt_offset_name_dst'   => isset($timezone['gmtOffsetNameDST']) ? $timezone['gmtOffsetNameDST'] : null,
                    'abbreviation'          => isset($timezone['abbreviation']) ? $timezone['abbreviation'] : null
                ]
            );
        }
    }
}