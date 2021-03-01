<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ShipPackageDetailsType extends BaseType
{
    private static $propertyTypes = [
        'MeasurementUnit' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MeasurementSystemCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MeasurementUnit',
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
        'ShippingIrregular' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingIrregular',
        ],
        'ShippingPackage' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingPackageCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingPackage',
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