<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Filter
{
    public function register($db)
    {
        $filterComponent =
            $db->fetchAll(
                "SELECT * FROM components WHERE route LIKE :route",
                Enum::FETCH_ASSOC,
                [
                    "route" => "filters",
                ]
            );

        $filter =
            [
                'name'          => 'Exclude Auto Generated Filters',
                'component_id'  => $filterComponent[0]['id'],
                'conditions'    => '-:auto_generated:equals:0&',
                'type'          => 0,
                'auto_generated'=> 0,
                'is_default'    => 1,
                'account_id'    => 0
            ];

        $db->insertAsDict('filters', $filter);
    }
}