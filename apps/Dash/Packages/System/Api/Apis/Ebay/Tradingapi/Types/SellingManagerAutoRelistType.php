<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SellingManagerAutoRelistType extends BaseType
{
    private static $propertyTypes = [
        'Type' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellingManagerAutoRelistTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Type',
        ],
        'RelistCondition' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellingManagerAutoRelistOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RelistCondition',
        ],
        'RelistAfterDays' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RelistAfterDays',
        ],
        'RelistAfterHours' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RelistAfterHours',
        ],
        'RelistAtSpecificTimeOfDay' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RelistAtSpecificTimeOfDay',
        ],
        'BestOfferDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BestOfferDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOfferDetails',
        ],
        'ListingHoldInventoryLevel' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ListingHoldInventoryLevel',
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