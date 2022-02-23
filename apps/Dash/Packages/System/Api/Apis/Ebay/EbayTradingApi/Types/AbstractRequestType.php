<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class AbstractRequestType extends BaseType
{
    private static $propertyTypes = [
        'DetailLevel' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DetailLevelCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DetailLevel',
        ],
        'ErrorLanguage' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ErrorLanguage',
        ],
        'MessageID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MessageID',
        ],
        'Version' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Version',
        ],
        'EndUserIP' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EndUserIP',
        ],
        'ErrorHandling' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ErrorHandlingCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ErrorHandling',
        ],
        'InvocationID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InvocationID',
        ],
        'OutputSelector' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'OutputSelector',
        ],
        'WarningLevel' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\WarningLevelCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WarningLevel',
        ],
        'BotBlock' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BotBlockRequestType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BotBlock',
        ],
        'RequesterCredentials' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CustomSecurityHeaderType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RequesterCredentials',
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