<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class RecommendationValidationRulesType extends BaseType
{
    private static $propertyTypes = [
        'ValueType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ValueTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ValueType',
        ],
        'MinValues' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MinValues',
        ],
        'MaxValues' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaxValues',
        ],
        'SelectionMode' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SelectionModeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SelectionMode',
        ],
        'AspectUsage' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AspectUsageCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AspectUsage',
        ],
        'MaxValueLength' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaxValueLength',
        ],
        'ProductRequired' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ProductRequiredCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductRequired',
        ],
        'UsageConstraint' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\UsageConstraintCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UsageConstraint',
        ],
        'Confidence' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Confidence',
        ],
        'Relationship' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\NameValueRelationshipType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Relationship',
        ],
        'VariationPicture' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\VariationPictureRuleCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VariationPicture',
        ],
        'VariationSpecifics' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\VariationSpecificsRuleCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VariationSpecifics',
        ],
        'ValueFormat' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ValueFormatCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ValueFormat',
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