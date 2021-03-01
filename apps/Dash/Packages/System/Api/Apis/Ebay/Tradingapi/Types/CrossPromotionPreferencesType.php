<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CrossPromotionPreferencesType extends BaseType
{
    private static $propertyTypes = [
        'CrossPromotionEnabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CrossPromotionEnabled',
        ],
        'CrossSellItemFormatSortFilter' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemFormatSortFilterCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CrossSellItemFormatSortFilter',
        ],
        'CrossSellGallerySortFilter' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\GallerySortFilterCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CrossSellGallerySortFilter',
        ],
        'CrossSellItemSortFilter' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemSortFilterCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CrossSellItemSortFilter',
        ],
        'UpSellItemFormatSortFilter' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemFormatSortFilterCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpSellItemFormatSortFilter',
        ],
        'UpSellGallerySortFilter' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\GallerySortFilterCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpSellGallerySortFilter',
        ],
        'UpSellItemSortFilter' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemSortFilterCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpSellItemSortFilter',
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