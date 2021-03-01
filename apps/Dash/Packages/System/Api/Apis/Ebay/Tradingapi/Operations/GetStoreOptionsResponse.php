<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class GetStoreOptionsResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'BasicThemeArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\StoreThemeArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BasicThemeArray',
        ],
        'AdvancedThemeArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\StoreThemeArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AdvancedThemeArray',
        ],
        'LogoArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\StoreLogoArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LogoArray',
        ],
        'SubscriptionArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\StoreSubscriptionArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SubscriptionArray',
        ],
        'MaxCategories' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaxCategories',
        ],
        'MaxCategoryLevels' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaxCategoryLevels',
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