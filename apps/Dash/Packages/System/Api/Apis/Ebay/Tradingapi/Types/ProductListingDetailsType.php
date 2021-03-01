<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ProductListingDetailsType extends BaseType
{
    private static $propertyTypes = [
        'IncludeStockPhotoURL' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IncludeStockPhotoURL',
        ],
        'UseStockPhotoURLAsGallery' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UseStockPhotoURLAsGallery',
        ],
        'StockPhotoURL' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StockPhotoURL',
        ],
        'Copyright' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Copyright',
        ],
        'ProductReferenceID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductReferenceID',
        ],
        'DetailsURL' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DetailsURL',
        ],
        'ProductDetailsURL' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductDetailsURL',
        ],
        'ReturnSearchResultOnDuplicates' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReturnSearchResultOnDuplicates',
        ],
        'ISBN' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ISBN',
        ],
        'UPC' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UPC',
        ],
        'EAN' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EAN',
        ],
        'BrandMPN' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BrandMPNType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BrandMPN',
        ],
        'TicketListingDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\TicketListingDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TicketListingDetails',
        ],
        'UseFirstProduct' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UseFirstProduct',
        ],
        'IncludeeBayProductDetails' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IncludeeBayProductDetails',
        ],
        'NameValueList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\NameValueListType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'NameValueList',
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