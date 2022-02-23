<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SizeOptions extends BaseType
{
    private static $propertyTypes = [
        'disk_min' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'disk_min',
        ],
        'disk_max' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'disk_max',
        ],
        'disk_cost_per_additional_gigabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'disk_cost_per_additional_gigabyte',
        ],
        'restricted_disk_values' => [
          'type' => 'integer',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'restricted_disk_values',
        ],
        'memory_max' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'memory_max',
        ],
        'memory_cost_per_additional_megabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'memory_cost_per_additional_megabyte',
        ],
        'transfer_max' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'transfer_max',
        ],
        'transfer_cost_per_additional_gigabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'transfer_cost_per_additional_gigabyte',
        ],
        'ipv4_addresses_max' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ipv4_addresses_max',
        ],
        'ipv4_addresses_cost_per_address' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ipv4_addresses_cost_per_address',
        ],
        'discount_for_no_public_ipv4' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'discount_for_no_public_ipv4',
        ],
        'daily_backups' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'daily_backups',
        ],
        'weekly_backups' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'weekly_backups',
        ],
        'monthly_backups' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'monthly_backups',
        ],
        'backups_cost_per_backup_per_gigabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'backups_cost_per_backup_per_gigabyte',
        ],
        'offsite_backups_cost_per_gigabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'offsite_backups_cost_per_gigabyte',
        ],
        'offsite_backup_frequency_cost' => [
          'attribute' => false,
          'elementName' => 'offsite_backup_frequency_cost',
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