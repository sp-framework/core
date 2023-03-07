<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class VariationsType extends BaseType
{
    private static $propertyTypes = [
        'Variation' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\VariationType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Variation',
        ],
        'Pictures' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PicturesType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Pictures',
        ],
        'VariationSpecificsSet' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\NameValueListArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VariationSpecificsSet',
        ],
        'ModifyNameList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ModifyNameArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ModifyNameList',
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