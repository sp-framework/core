<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class PaymentTransactionCodeType extends BaseType
{
    private static $propertyTypes = [
        'PaymentStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentTransactionStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentStatus',
        ],
        'Payer' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\UserIdentityType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Payer',
        ],
        'Payee' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\UserIdentityType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Payee',
        ],
        'PaymentTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentTime',
        ],
        'PaymentAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentAmount',
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
        'PaymentReferenceID' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TransactionReferenceType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'PaymentReferenceID',
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