<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetMyMessagesResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'Summary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MyMessagesSummaryType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Summary',
        ],
        'Alerts' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MyMessagesAlertArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Alerts',
        ],
        'Messages' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MyMessagesMessageArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Messages',
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