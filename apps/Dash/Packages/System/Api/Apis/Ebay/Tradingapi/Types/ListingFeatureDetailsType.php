<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ListingFeatureDetailsType extends BaseType
{
    private static $propertyTypes = [
        'BoldTitle' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BoldTitleCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BoldTitle',
        ],
        'Border' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BorderCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Border',
        ],
        'Highlight' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\HighlightCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Highlight',
        ],
        'GiftIcon' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\GiftIconCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GiftIcon',
        ],
        'HomePageFeatured' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\HomePageFeaturedCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HomePageFeatured',
        ],
        'FeaturedFirst' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeaturedFirstCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeaturedFirst',
        ],
        'FeaturedPlus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeaturedPlusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeaturedPlus',
        ],
        'ProPack' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ProPackCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProPack',
        ],
        'DetailVersion' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DetailVersion',
        ],
        'UpdateTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdateTime',
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