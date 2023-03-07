<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetSellingManagerInventoryRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'Sort' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerProductSortCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Sort',
        ],
        'FolderID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FolderID',
        ],
        'Pagination' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Pagination',
        ],
        'SortOrder' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SortOrderCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SortOrder',
        ],
        'Search' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerSearchType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Search',
        ],
        'StoreCategoryID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StoreCategoryID',
        ],
        'Filter' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerInventoryPropertyTypeCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Filter',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'GetSellingManagerInventoryRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}