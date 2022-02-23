<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class DisputeType extends BaseType
{
    private static $propertyTypes = [
        'DisputeID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeID',
        ],
        'DisputeRecordType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DisputeRecordTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeRecordType',
        ],
        'DisputeState' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DisputeStateCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeState',
        ],
        'DisputeStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DisputeStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeStatus',
        ],
        'OtherPartyRole' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TradingRoleCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OtherPartyRole',
        ],
        'OtherPartyName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OtherPartyName',
        ],
        'UserRole' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TradingRoleCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserRole',
        ],
        'BuyerUserID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerUserID',
        ],
        'SellerUserID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerUserID',
        ],
        'TransactionID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TransactionID',
        ],
        'Item' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Item',
        ],
        'DisputeReason' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DisputeReasonCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeReason',
        ],
        'DisputeExplanation' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DisputeExplanationCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeExplanation',
        ],
        'DisputeCreditEligibility' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DisputeCreditEligibilityCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeCreditEligibility',
        ],
        'DisputeCreatedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeCreatedTime',
        ],
        'DisputeModifiedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisputeModifiedTime',
        ],
        'DisputeResolution' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DisputeResolutionType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DisputeResolution',
        ],
        'DisputeMessage' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DisputeMessageType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DisputeMessage',
        ],
        'Escalation' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Escalation',
        ],
        'PurchaseProtection' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PurchaseProtection',
        ],
        'OrderLineItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderLineItemID',
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