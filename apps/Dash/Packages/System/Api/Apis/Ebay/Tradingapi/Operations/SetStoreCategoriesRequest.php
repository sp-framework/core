<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class SetStoreCategoriesRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'Action' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\StoreCategoryUpdateActionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Action',
        ],
        'ItemDestinationCategoryID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemDestinationCategoryID',
        ],
        'DestinationParentCategoryID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DestinationParentCategoryID',
        ],
        'StoreCategories' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\StoreCustomCategoryArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StoreCategories',
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

        if (!array_key_exists(__CLASS__, self::$requestXmlRootElementNames)) {
            self::$requestXmlRootElementNames[__CLASS__] = 'SetStoreCategoriesRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}