<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class ManualJournalLine extends XeroType
{
    private static $propertyTypes = [
        'LineAmount' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LineAmount',
        ],
        'AccountCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AccountCode',
        ],
        'AccountID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AccountID',
        ],
        'Description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Description',
        ],
        'TaxType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TaxType',
        ],
        'Tracking' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\TrackingCategory',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Tracking',
        ],
        'TaxAmount' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TaxAmount',
        ],
        'IsBlank' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IsBlank',
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