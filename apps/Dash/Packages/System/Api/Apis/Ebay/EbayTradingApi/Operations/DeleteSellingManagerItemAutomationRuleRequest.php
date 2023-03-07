<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class DeleteSellingManagerItemAutomationRuleRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'DeleteAutomatedRelistingRule' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeleteAutomatedRelistingRule',
        ],
        'DeleteAutomatedSecondChanceOfferRule' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeleteAutomatedSecondChanceOfferRule',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'DeleteSellingManagerItemAutomationRuleRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}