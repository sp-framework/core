<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SellerProfilesType extends BaseType
{
    private static $propertyTypes = [
        'SellerShippingProfile' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellerShippingProfileType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerShippingProfile',
        ],
        'SellerReturnProfile' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellerReturnProfileType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerReturnProfile',
        ],
        'SellerPaymentProfile' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellerPaymentProfileType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerPaymentProfile',
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