<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class GetSellingManagerSoldListingsResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'SaleRecord' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellingManagerSoldOrderType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SaleRecord',
        ],
        'PaginationResult' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaginationResultType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaginationResult',
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