<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetCharitiesRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'CharityID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityID',
        ],
        'CharityName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityName',
        ],
        'Query' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Query',
        ],
        'CharityRegion' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityRegion',
        ],
        'CharityDomain' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityDomain',
        ],
        'IncludeDescription' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IncludeDescription',
        ],
        'MatchType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StringMatchCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MatchType',
        ],
        'Featured' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Featured',
        ],
        'CampaignID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CampaignID',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'GetCharitiesRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}