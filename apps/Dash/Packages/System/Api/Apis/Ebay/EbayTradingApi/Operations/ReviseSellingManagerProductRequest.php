<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class ReviseSellingManagerProductRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'SellingManagerProductDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerProductDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingManagerProductDetails',
        ],
        'SellingManagerFolderDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerFolderDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingManagerFolderDetails',
        ],
        'DeletedField' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DeletedField',
        ],
        'SellingManagerProductSpecifics' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerProductSpecificsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingManagerProductSpecifics',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'ReviseSellingManagerProductRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}