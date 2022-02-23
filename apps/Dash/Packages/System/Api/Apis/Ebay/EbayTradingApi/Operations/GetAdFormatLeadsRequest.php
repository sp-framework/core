<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetAdFormatLeadsRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'Status' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MessageStatusTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'IncludeMemberMessages' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IncludeMemberMessages',
        ],
        'StartCreationTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StartCreationTime',
        ],
        'EndCreationTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EndCreationTime',
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

        if (!array_key_exists(__CLASS__, self::$requestXmlRootElementNames)) {
            self::$requestXmlRootElementNames[__CLASS__] = 'GetAdFormatLeadsRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}