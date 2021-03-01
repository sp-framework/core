<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class FeedbackSummaryType extends BaseType
{
    private static $propertyTypes = [
        'BidRetractionFeedbackPeriodArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeedbackPeriodArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidRetractionFeedbackPeriodArray',
        ],
        'NegativeFeedbackPeriodArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeedbackPeriodArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NegativeFeedbackPeriodArray',
        ],
        'NeutralFeedbackPeriodArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeedbackPeriodArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NeutralFeedbackPeriodArray',
        ],
        'PositiveFeedbackPeriodArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeedbackPeriodArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PositiveFeedbackPeriodArray',
        ],
        'TotalFeedbackPeriodArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeedbackPeriodArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalFeedbackPeriodArray',
        ],
        'NeutralCommentCountFromSuspendedUsers' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NeutralCommentCountFromSuspendedUsers',
        ],
        'UniqueNegativeFeedbackCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UniqueNegativeFeedbackCount',
        ],
        'UniquePositiveFeedbackCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UniquePositiveFeedbackCount',
        ],
        'UniqueNeutralFeedbackCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UniqueNeutralFeedbackCount',
        ],
        'SellerRatingSummaryArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerRatingSummaryArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerRatingSummaryArray',
        ],
        'SellerRoleMetrics' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerRoleMetricsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerRoleMetrics',
        ],
        'BuyerRoleMetrics' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BuyerRoleMetricsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerRoleMetrics',
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