<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class UploadSiteHostedPicturesRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'PictureName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PictureName',
        ],
        'PictureSystemVersion' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PictureSystemVersion',
        ],
        'PictureSet' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PictureSetCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PictureSet',
        ],
        'PictureData' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\Base64BinaryType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PictureData',
        ],
        'PictureUploadPolicy' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PictureUploadPolicyCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PictureUploadPolicy',
        ],
        'ExternalPictureURL' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ExternalPictureURL',
        ],
        'PictureWatermark' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PictureWatermarkCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'PictureWatermark',
        ],
        'ExtensionInDays' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExtensionInDays',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'UploadSiteHostedPicturesRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}