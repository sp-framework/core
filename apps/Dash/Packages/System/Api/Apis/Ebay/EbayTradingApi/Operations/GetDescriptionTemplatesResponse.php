<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetDescriptionTemplatesResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'DescriptionTemplate' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DescriptionTemplateType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DescriptionTemplate',
        ],
        'LayoutTotal' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LayoutTotal',
        ],
        'ObsoleteLayoutID' => [
          'type' => 'integer',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ObsoleteLayoutID',
        ],
        'ObsoleteThemeID' => [
          'type' => 'integer',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ObsoleteThemeID',
        ],
        'ThemeGroup' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ThemeGroupType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ThemeGroup',
        ],
        'ThemeTotal' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ThemeTotal',
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