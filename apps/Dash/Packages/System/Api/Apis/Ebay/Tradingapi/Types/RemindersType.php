<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class RemindersType extends BaseType
{
    private static $propertyTypes = [
        'PaymentToSendCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentToSendCount',
        ],
        'FeedbackToReceiveCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackToReceiveCount',
        ],
        'FeedbackToSendCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackToSendCount',
        ],
        'OutbidCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OutbidCount',
        ],
        'PaymentToReceiveCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentToReceiveCount',
        ],
        'SecondChanceOfferCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SecondChanceOfferCount',
        ],
        'ShippingNeededCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingNeededCount',
        ],
        'RelistingNeededCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RelistingNeededCount',
        ],
        'TotalNewLeadsCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalNewLeadsCount',
        ],
        'DocsForCCProcessingToSendCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DocsForCCProcessingToSendCount',
        ],
        'RTEToProcessCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RTEToProcessCount',
        ],
        'ItemReceiptToConfirmCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemReceiptToConfirmCount',
        ],
        'RefundOnHoldCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundOnHoldCount',
        ],
        'RefundCancelledCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundCancelledCount',
        ],
        'ShippingDetailsToBeProvidedCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingDetailsToBeProvidedCount',
        ],
        'ItemReceiptConfirmationToReceiveCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemReceiptConfirmationToReceiveCount',
        ],
        'RefundInitiatedCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundInitiatedCount',
        ],
        'PendingRTERequestCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PendingRTERequestCount',
        ],
        'DeclinedRTERequestCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeclinedRTERequestCount',
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