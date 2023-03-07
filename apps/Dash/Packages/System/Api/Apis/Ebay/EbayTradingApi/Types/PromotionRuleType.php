<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class PromotionRuleType extends BaseType
{
    private static $propertyTypes = [
        'PromotedStoreCategoryID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotedStoreCategoryID',
        ],
        'PromotedeBayCategoryID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotedeBayCategoryID',
        ],
        'PromotedKeywords' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotedKeywords',
        ],
        'ReferringItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReferringItemID',
        ],
        'ReferringStoreCategoryID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReferringStoreCategoryID',
        ],
        'ReferringeBayCategoryID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReferringeBayCategoryID',
        ],
        'ReferringKeywords' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReferringKeywords',
        ],
        'PromotionScheme' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PromotionSchemeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotionScheme',
        ],
        'PromotionMethod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PromotionMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotionMethod',
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