<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class PricingSummary extends BaseType
{
    private static $propertyTypes = [
        'auctionReservePrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Amount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'auctionReservePrice',
        ],
        'auctionStartPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Amount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'auctionStartPrice',
        ],
        'minimumAdvertisedPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Amount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'minimumAdvertisedPrice',
        ],
        'originallySoldForRetailPriceOn' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'originallySoldForRetailPriceOn',
        ],
        'originalRetailPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Amount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'originalRetailPrice',
        ],
        'price' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Amount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'price',
        ],
        'pricingVisibility' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'pricingVisibility',
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