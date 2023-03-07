<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CharityType extends BaseType
{
    private static $propertyTypes = [
        'CharityName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityName',
        ],
        'CharityNumber' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityNumber',
        ],
        'DonationPercent' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DonationPercent',
        ],
        'CharityID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityID',
        ],
        'Mission' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Mission',
        ],
        'LogoURL' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LogoURL',
        ],
        'Status' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CharityStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'CharityListing' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityListing',
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