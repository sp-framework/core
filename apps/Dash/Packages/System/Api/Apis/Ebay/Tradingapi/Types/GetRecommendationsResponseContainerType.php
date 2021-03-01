<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class GetRecommendationsResponseContainerType extends BaseType
{
    private static $propertyTypes = [
        'ListingAnalyzerRecommendations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ListingAnalyzerRecommendationsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ListingAnalyzerRecommendations',
        ],
        'SIFFTASRecommendations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SIFFTASRecommendationsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SIFFTASRecommendations',
        ],
        'PricingRecommendations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PricingRecommendationsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PricingRecommendations',
        ],
        'AttributeRecommendations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AttributeRecommendationsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AttributeRecommendations',
        ],
        'ProductRecommendations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ProductRecommendationsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductRecommendations',
        ],
        'CorrelationID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CorrelationID',
        ],
        'Recommendations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\RecommendationsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Recommendations',
        ],
        'ProductListingDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ProductListingDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductListingDetails',
        ],
        'Title' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Title',
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