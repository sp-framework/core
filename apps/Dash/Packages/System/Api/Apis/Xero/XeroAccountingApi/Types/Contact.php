<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Contact extends BaseType
{
    private static $propertyTypes = [
        'ContactID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ContactID',
        ],
        'ContactNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ContactNumber',
        ],
        'AccountNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AccountNumber',
        ],
        'ContactStatus' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ContactStatus',
        ],
        'Name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Name',
        ],
        'FirstName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FirstName',
        ],
        'LastName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LastName',
        ],
        'EmailAddress' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EmailAddress',
        ],
        'SkypeUserName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SkypeUserName',
        ],
        'ContactPersons' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ContactPerson',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ContactPersons',
        ],
        'BankAccountDetails' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BankAccountDetails',
        ],
        'TaxNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TaxNumber',
        ],
        'AccountsReceivableTaxType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AccountsReceivableTaxType',
        ],
        'AccountsPayableTaxType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AccountsPayableTaxType',
        ],
        'Addresses' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Address',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Addresses',
        ],
        'Phones' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Phone',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Phones',
        ],
        'IsSupplier' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IsSupplier',
        ],
        'IsCustomer' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IsCustomer',
        ],
        'DefaultCurrency' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DefaultCurrency',
        ],
        'XeroNetworkKey' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'XeroNetworkKey',
        ],
        'SalesDefaultAccountCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SalesDefaultAccountCode',
        ],
        'PurchasesDefaultAccountCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PurchasesDefaultAccountCode',
        ],
        'SalesTrackingCategories' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\SalesTrackingCategory',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SalesTrackingCategories',
        ],
        'PurchasesTrackingCategories' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\SalesTrackingCategory',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'PurchasesTrackingCategories',
        ],
        'TrackingCategoryName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TrackingCategoryName',
        ],
        'TrackingCategoryOption' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TrackingCategoryOption',
        ],
        'PaymentTerms' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\PaymentTerm',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentTerms',
        ],
        'UpdatedDateUTC' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdatedDateUTC',
        ],
        'ContactGroups' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ContactGroup',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ContactGroups',
        ],
        'Website' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Website',
        ],
        'BrandingTheme' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\BrandingTheme',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BrandingTheme',
        ],
        'BatchPayments' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\BatchPaymentDetails',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BatchPayments',
        ],
        'Discount' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Discount',
        ],
        'Balances' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Balances',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Balances',
        ],
        'Attachments' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Attachment',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Attachments',
        ],
        'HasAttachments' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HasAttachments',
        ],
        'ValidationErrors' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ValidationError',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ValidationErrors',
        ],
        'HasValidationErrors' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HasValidationErrors',
        ],
        'StatusAttributeString' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StatusAttributeString',
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