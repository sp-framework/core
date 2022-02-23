<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Invoice extends BaseType
{
    private static $propertyTypes = [
        'invoice_id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'invoice_id',
        ],
        'reference' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'reference',
        ],
        'amount' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'amount',
        ],
        'tax_code' => [
          'attribute' => false,
          'elementName' => 'tax_code',
        ],
        'tax' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'tax',
        ],
        'created' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'created',
        ],
        'date_due' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'date_due',
        ],
        'date_overdue' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'date_overdue',
        ],
        'paid' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'paid',
        ],
        'refunded' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'refunded',
        ],
        'payment_failure_count' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'payment_failure_count',
        ],
        'invoice_items' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\InvoiceLineItem',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'invoice_items',
        ],
        'invoice_download_url' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'invoice_download_url',
        ],
      ];

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);
    }
}