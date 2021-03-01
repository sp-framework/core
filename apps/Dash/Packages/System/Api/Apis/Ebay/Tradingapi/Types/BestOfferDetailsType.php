<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class BestOfferDetailsType extends BaseType
{
    private static $propertyTypes = [
        'BestOfferCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOfferCount',
        ],
        'BestOfferEnabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOfferEnabled',
        ],
        'BestOffer' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOffer',
        ],
        'BestOfferStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BestOfferStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOfferStatus',
        ],
        'BestOfferType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BestOfferTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOfferType',
        ],
        'NewBestOffer' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NewBestOffer',
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