<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SellingManagerAlertType extends BaseType
{
    private static $propertyTypes = [
        'AlertType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerAlertTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AlertType',
        ],
        'SoldAlert' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerSoldListingsPropertyTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SoldAlert',
        ],
        'InventoryAlert' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerInventoryPropertyTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InventoryAlert',
        ],
        'AutomationAlert' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerAutomationPropertyTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AutomationAlert',
        ],
        'PaisaPayAlert' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerPaisaPayPropertyTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaisaPayAlert',
        ],
        'GeneralAlert' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerGeneralPropertyTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GeneralAlert',
        ],
        'DurationInDays' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DurationInDays',
        ],
        'Count' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Count',
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