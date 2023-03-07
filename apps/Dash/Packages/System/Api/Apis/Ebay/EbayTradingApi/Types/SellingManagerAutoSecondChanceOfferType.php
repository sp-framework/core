<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SellingManagerAutoSecondChanceOfferType extends BaseType
{
    private static $propertyTypes = [
        'SecondChanceOfferCondition' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerAutoSecondChanceOfferTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SecondChanceOfferCondition',
        ],
        'Amount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Amount',
        ],
        'ProfitPercent' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProfitPercent',
        ],
        'Duration' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SecondChanceOfferDurationCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Duration',
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