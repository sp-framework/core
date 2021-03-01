<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class AddItemsRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'AddItemRequestContainer' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AddItemRequestContainerType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'AddItemRequestContainer',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'AddItemsRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}