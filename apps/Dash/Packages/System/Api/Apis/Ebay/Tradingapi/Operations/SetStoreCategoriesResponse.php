<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class SetStoreCategoriesResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'TaskID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TaskID',
        ],
        'Status' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\TaskStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'CustomCategory' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\StoreCustomCategoryArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CustomCategory',
        ],
      ];

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        if (!array_key_exists(__CLASS__, self::$xmlNamespaces)) {
            self::$xmlNamespaces[__CLASS__] = 'xmlns="urn:ebay:apis:eBLBaseComponents"';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}