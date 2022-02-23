<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

use Phalcon\Db\Enum;
use Phalcon\Helper\Json;

class Filter
{
    public function register($db)
    {
        $filterComponent =
            $db->fetchAll(
                "SELECT * FROM modules_components WHERE route LIKE :route",
                Enum::FETCH_ASSOC,
                [
                    "route" => "system/filters",
                ]
            );

        $filter =
            [
                'name'              => 'Exclude Auto Generated Filters',
                'component_id'      => $filterComponent[0]['id'],
                'conditions'        => '-|auto_generated|equals|0&',
                'filter_type'       => 0,
                'auto_generated'    => 0,
                'is_default'        => 1,
                'account_id'        => 0
            ];

        $db->insertAsDict('basepackages_filters', $filter);
    }
}