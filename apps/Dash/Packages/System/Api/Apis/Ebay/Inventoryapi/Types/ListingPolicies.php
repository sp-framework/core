<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ListingPolicies extends BaseType
{
    private static $propertyTypes = [
        'bestOfferTerms' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\BestOffer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'bestOfferTerms',
        ],
        'eBayPlusIfEligible' => [
          'attribute' => false,
          'elementName' => 'eBayPlusIfEligible',
        ],
        'fulfillmentPolicyId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'fulfillmentPolicyId',
        ],
        'paymentPolicyId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'paymentPolicyId',
        ],
        'returnPolicyId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'returnPolicyId',
        ],
        'shippingCostOverrides' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\ShippingCostOverride',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'shippingCostOverrides',
        ],
      ];

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);
    }
}