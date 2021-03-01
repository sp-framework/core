<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class SetSellingManagerTemplateAutomationRuleRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'SaleTemplateID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SaleTemplateID',
        ],
        'AutomatedListingRule' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellingManagerAutoListType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AutomatedListingRule',
        ],
        'AutomatedRelistingRule' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellingManagerAutoRelistType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AutomatedRelistingRule',
        ],
        'AutomatedSecondChanceOfferRule' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellingManagerAutoSecondChanceOfferType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AutomatedSecondChanceOfferRule',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'SetSellingManagerTemplateAutomationRuleRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}