<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class MoveSellingManagerInventoryFolderRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'FolderID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FolderID',
        ],
        'NewParentFolderID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NewParentFolderID',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'MoveSellingManagerInventoryFolderRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}