<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class RefundTransactionInfoType extends BaseType
{
    private static $propertyTypes = [
        'RefundStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentTransactionStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundStatus',
        ],
        'RefundType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RefundSourceTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundType',
        ],
        'RefundTo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\UserIdentityType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundTo',
        ],
        'RefundTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundTime',
        ],
        'RefundAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundAmount',
        ],
        'ReferenceID' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TransactionReferenceType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReferenceID',
        ],
        'FeeOrCreditAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeeOrCreditAmount',
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