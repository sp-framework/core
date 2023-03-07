<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class UserType extends BaseType
{
    private static $propertyTypes = [
        'AboutMePage' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AboutMePage',
        ],
        'EIASToken' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EIASToken',
        ],
        'Email' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Email',
        ],
        'FeedbackScore' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackScore',
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
        'PositiveFeedbackPercent' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PositiveFeedbackPercent',
        ],
        'FeedbackPrivate' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackPrivate',
        ],
        'FeedbackRatingStar' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\FeedbackRatingStarCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackRatingStar',
        ],
        'IDVerified' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IDVerified',
        ],
        'eBayGoodStanding' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayGoodStanding',
        ],
        'NewUser' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NewUser',
        ],
        'RegistrationAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RegistrationAddress',
        ],
        'RegistrationDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RegistrationDate',
        ],
        'Site' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SiteCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Site',
        ],
        'Status' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\UserStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'UserID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserID',
        ],
        'UserIDChanged' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserIDChanged',
        ],
        'UserIDLastChanged' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserIDLastChanged',
        ],
        'VATStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\VATStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VATStatus',
        ],
        'BuyerInfo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BuyerType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerInfo',
        ],
        'SellerInfo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellerType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerInfo',
        ],
        'BusinessRole' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BusinessRoleType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BusinessRole',
        ],
        'CharityAffiliations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CharityAffiliationsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityAffiliations',
        ],
        'PayPalAccountLevel' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PayPalAccountLevelCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalAccountLevel',
        ],
        'PayPalAccountType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PayPalAccountTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalAccountType',
        ],
        'PayPalAccountStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PayPalAccountStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalAccountStatus',
        ],
        'UserSubscription' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\EBaySubscriptionTypeCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'UserSubscription',
        ],
        'SiteVerified' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SiteVerified',
        ],
        'SkypeID' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SkypeID',
        ],
        'eBayWikiReadOnly' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayWikiReadOnly',
        ],
        'TUVLevel' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TUVLevel',
        ],
        'VATID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VATID',
        ],
        'SellerPaymentMethod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellerPaymentMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerPaymentMethod',
        ],
        'BiddingSummary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BiddingSummaryType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BiddingSummary',
        ],
        'UserAnonymized' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserAnonymized',
        ],
        'UniqueNeutralFeedbackCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UniqueNeutralFeedbackCount',
        ],
        'EnterpriseSeller' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EnterpriseSeller',
        ],
        'BillingEmail' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BillingEmail',
        ],
        'QualifiesForSelling' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QualifiesForSelling',
        ],
        'StaticAlias' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StaticAlias',
        ],
        'ShippingAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingAddress',
        ],
        'Membership' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MembershipDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Membership',
        ],
        'UserFirstName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserFirstName',
        ],
        'UserLastName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserLastName',
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