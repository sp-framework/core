<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ShipmentType extends BaseType
{
    private static $propertyTypes = [
        'EstimatedDeliveryDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EstimatedDeliveryDate',
        ],
        'InsuredValue' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InsuredValue',
        ],
        'PackageDepth' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MeasureType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PackageDepth',
        ],
        'PackageLength' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MeasureType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PackageLength',
        ],
        'PackageWidth' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MeasureType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PackageWidth',
        ],
        'PayPalShipmentID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalShipmentID',
        ],
        'ShipmentID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShipmentID',
        ],
        'PostageTotal' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PostageTotal',
        ],
        'PrintedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PrintedTime',
        ],
        'ShipFromAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AddressType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShipFromAddress',
        ],
        'ShippingAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AddressType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingAddress',
        ],
        'ShippingCarrierUsed' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingCarrierUsed',
        ],
        'ShippingFeature' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingFeatureCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingFeature',
        ],
        'ShippingPackage' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingPackageCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingPackage',
        ],
        'ShippingServiceUsed' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingServiceUsed',
        ],
        'ShipmentTrackingNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShipmentTrackingNumber',
        ],
        'WeightMajor' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MeasureType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WeightMajor',
        ],
        'WeightMinor' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MeasureType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WeightMinor',
        ],
        'ItemTransactionID' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemTransactionIDType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ItemTransactionID',
        ],
        'DeliveryDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeliveryDate',
        ],
        'DeliveryStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShipmentDeliveryStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeliveryStatus',
        ],
        'RefundGrantedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundGrantedTime',
        ],
        'RefundRequestedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundRequestedTime',
        ],
        'Status' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShipmentStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'ShippedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippedTime',
        ],
        'Notes' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Notes',
        ],
        'ShipmentTrackingDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShipmentTrackingDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShipmentTrackingDetails',
        ],
        'ShipmentLineItem' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShipmentLineItemType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShipmentLineItem',
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