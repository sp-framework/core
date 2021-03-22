<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class StoreFontType extends BaseType
{
    private static $propertyTypes = [
        'NameFace' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreFontFaceCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NameFace',
        ],
        'NameSize' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreFontSizeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NameSize',
        ],
        'NameColor' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NameColor',
        ],
        'TitleFace' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreFontFaceCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TitleFace',
        ],
        'TitleSize' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreFontSizeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TitleSize',
        ],
        'TitleColor' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TitleColor',
        ],
        'DescFace' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreFontFaceCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DescFace',
        ],
        'DescSize' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreFontSizeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DescSize',
        ],
        'DescColor' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DescColor',
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