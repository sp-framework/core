<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class PictureDetailsType extends BaseType
{
    private static $propertyTypes = [
        'GalleryType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\GalleryTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GalleryType',
        ],
        'PhotoDisplay' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PhotoDisplayCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PhotoDisplay',
        ],
        'PictureURL' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'PictureURL',
        ],
        'PictureSource' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PictureSourceCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PictureSource',
        ],
        'GalleryStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\GalleryStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GalleryStatus',
        ],
        'GalleryErrorInfo' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GalleryErrorInfo',
        ],
        'ExternalPictureURL' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ExternalPictureURL',
        ],
        'ExtendedPictureDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ExtendedPictureDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExtendedPictureDetails',
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