<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SiteBuyerRequirementDetailsType extends BaseType
{
    private static $propertyTypes = [
        'LinkedPayPalAccount' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LinkedPayPalAccount',
        ],
        'MaximumBuyerPolicyViolations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MaximumBuyerPolicyViolationsDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaximumBuyerPolicyViolations',
        ],
        'MaximumItemRequirements' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MaximumItemRequirementsDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaximumItemRequirements',
        ],
        'MaximumUnpaidItemStrikesInfo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MaximumUnpaidItemStrikesInfoDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaximumUnpaidItemStrikesInfo',
        ],
        'MinimumFeedbackScore' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MinimumFeedbackScoreDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MinimumFeedbackScore',
        ],
        'ShipToRegistrationCountry' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShipToRegistrationCountry',
        ],
        'VerifiedUserRequirements' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\VerifiedUserRequirementsDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VerifiedUserRequirements',
        ],
        'DetailVersion' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DetailVersion',
        ],
        'UpdateTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdateTime',
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