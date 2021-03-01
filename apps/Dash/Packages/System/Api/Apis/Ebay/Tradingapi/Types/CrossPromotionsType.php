<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CrossPromotionsType extends BaseType
{
    private static $propertyTypes = [
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'PrimaryScheme' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PromotionSchemeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PrimaryScheme',
        ],
        'PromotionMethod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PromotionMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotionMethod',
        ],
        'SellerID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerID',
        ],
        'ShippingDiscount' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingDiscount',
        ],
        'StoreName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StoreName',
        ],
        'PromotedItem' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PromotedItemType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'PromotedItem',
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