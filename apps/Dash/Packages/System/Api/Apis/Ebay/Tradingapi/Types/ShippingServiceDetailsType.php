<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ShippingServiceDetailsType extends BaseType
{
    private static $propertyTypes = [
        'Description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Description',
        ],
        'ExpeditedService' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpeditedService',
        ],
        'InternationalService' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalService',
        ],
        'ShippingService' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingService',
        ],
        'ShippingServiceID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingServiceID',
        ],
        'ShippingTimeMax' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingTimeMax',
        ],
        'ShippingTimeMin' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingTimeMin',
        ],
        'ShippingServiceCode' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingServiceCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingServiceCode',
        ],
        'ServiceType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingTypeCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ServiceType',
        ],
        'ShippingPackage' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingPackageCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingPackage',
        ],
        'DimensionsRequired' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DimensionsRequired',
        ],
        'ValidForSellingFlow' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ValidForSellingFlow',
        ],
        'SurchargeApplicable' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SurchargeApplicable',
        ],
        'ShippingCarrier' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingCarrierCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingCarrier',
        ],
        'CODService' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CODService',
        ],
        'DeprecationDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AnnouncementMessageType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DeprecationDetails',
        ],
        'MappedToShippingServiceID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MappedToShippingServiceID',
        ],
        'CostGroupFlat' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CostGroupFlat',
        ],
        'ShippingServicePackageDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingServicePackageDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingServicePackageDetails',
        ],
        'WeightRequired' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WeightRequired',
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
        'ShippingCategory' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingCategory',
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