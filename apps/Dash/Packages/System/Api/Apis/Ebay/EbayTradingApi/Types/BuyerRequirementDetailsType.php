<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class BuyerRequirementDetailsType extends BaseType
{
    private static $propertyTypes = [
        'ShipToRegistrationCountry' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShipToRegistrationCountry',
        ],
        'ZeroFeedbackScore' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ZeroFeedbackScore',
        ],
        'MaximumItemRequirements' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MaximumItemRequirementsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaximumItemRequirements',
        ],
        'MaximumUnpaidItemStrikesInfo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MaximumUnpaidItemStrikesInfoType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaximumUnpaidItemStrikesInfo',
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