<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetSellerDashboardResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'SearchStanding' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SearchStandingDashboardType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SearchStanding',
        ],
        'SellerFeeDiscount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellerFeeDiscountDashboardType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerFeeDiscount',
        ],
        'PowerSellerStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PowerSellerDashboardType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PowerSellerStatus',
        ],
        'PolicyCompliance' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PolicyComplianceDashboardType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PolicyCompliance',
        ],
        'BuyerSatisfaction' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BuyerSatisfactionDashboardType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerSatisfaction',
        ],
        'SellerAccount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellerAccountDashboardType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerAccount',
        ],
        'Performance' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PerformanceDashboardType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Performance',
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