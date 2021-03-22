<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ReturnPolicyDetailsType extends BaseType
{
    private static $propertyTypes = [
        'Refund' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RefundDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Refund',
        ],
        'ReturnsWithin' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ReturnsWithinDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ReturnsWithin',
        ],
        'ReturnsAccepted' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ReturnsAcceptedDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ReturnsAccepted',
        ],
        'Description' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Description',
        ],
        'WarrantyOffered' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\WarrantyOfferedDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'WarrantyOffered',
        ],
        'WarrantyType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\WarrantyTypeDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'WarrantyType',
        ],
        'WarrantyDuration' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\WarrantyDurationDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'WarrantyDuration',
        ],
        'EAN' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EAN',
        ],
        'ShippingCostPaidBy' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingCostPaidByDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingCostPaidBy',
        ],
        'RestockingFeeValue' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RestockingFeeValueDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'RestockingFeeValue',
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