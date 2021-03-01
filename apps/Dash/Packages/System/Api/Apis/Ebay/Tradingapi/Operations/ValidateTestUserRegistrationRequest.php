<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class ValidateTestUserRegistrationRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'FeedbackScore' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackScore',
        ],
        'RegistrationDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RegistrationDate',
        ],
        'SubscribeSA' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SubscribeSA',
        ],
        'SubscribeSAPro' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SubscribeSAPro',
        ],
        'SubscribeSM' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SubscribeSM',
        ],
        'SubscribeSMPro' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SubscribeSMPro',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'ValidateTestUserRegistrationRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}