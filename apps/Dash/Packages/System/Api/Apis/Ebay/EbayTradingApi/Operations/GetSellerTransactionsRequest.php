<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetSellerTransactionsRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'ModTimeFrom' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ModTimeFrom',
        ],
        'ModTimeTo' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ModTimeTo',
        ],
        'Pagination' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Pagination',
        ],
        'IncludeFinalValueFee' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IncludeFinalValueFee',
        ],
        'IncludeContainingOrder' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IncludeContainingOrder',
        ],
        'SKUArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SKUArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SKUArray',
        ],
        'Platform' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TransactionPlatformCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Platform',
        ],
        'NumberOfDays' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NumberOfDays',
        ],
        'InventoryTrackingMethod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\InventoryTrackingMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InventoryTrackingMethod',
        ],
        'IncludeCodiceFiscale' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IncludeCodiceFiscale',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'GetSellerTransactionsRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}