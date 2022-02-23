<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Server extends BaseType
{
    private static $propertyTypes = [
        'id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'id',
        ],
        'name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'name',
        ],
        'memory' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'memory',
        ],
        'vcpus' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'vcpus',
        ],
        'disk' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'disk',
        ],
        'locked' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'locked',
        ],
        'vpc_id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'vpc_id',
        ],
        'created_at' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'created_at',
        ],
        'status' => [
          'attribute' => false,
          'elementName' => 'status',
        ],
        'backup_ids' => [
          'type' => 'integer',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'backup_ids',
        ],
        'snapshot_ids' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'snapshot_ids',
        ],
        'features' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'features',
        ],
        'region' => [
          'attribute' => false,
          'elementName' => 'region',
        ],
        'image' => [
          'attribute' => false,
          'elementName' => 'image',
        ],
        'size' => [
          'attribute' => false,
          'elementName' => 'size',
        ],
        'size_slug' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'size_slug',
        ],
        'selected_size_options' => [
          'attribute' => false,
          'elementName' => 'selected_size_options',
        ],
        'networks' => [
          'attribute' => false,
          'elementName' => 'networks',
        ],
        'kernel' => [
          'attribute' => false,
          'elementName' => 'kernel',
        ],
        'next_backup_window' => [
          'attribute' => false,
          'elementName' => 'next_backup_window',
        ],
        'tags' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'tags',
        ],
        'volume_ids' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'volume_ids',
        ],
        'disks' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\Disk',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'disks',
        ],
        'backup_settings' => [
          'attribute' => false,
          'elementName' => 'backup_settings',
        ],
        'cancelled_at' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'cancelled_at',
        ],
        'rescue_console' => [
          'attribute' => false,
          'elementName' => 'rescue_console',
        ],
        'failover_ips' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'failover_ips',
        ],
        'host' => [
          'attribute' => false,
          'elementName' => 'host',
        ],
        'partner_id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'partner_id',
        ],
        'password_change_supported' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'password_change_supported',
        ],
        'permalink' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'permalink',
        ],
        'attached_backup' => [
          'attribute' => false,
          'elementName' => 'attached_backup',
        ],
        'advanced_features' => [
          'attribute' => false,
          'elementName' => 'advanced_features',
        ],
      ];

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);
    }
}