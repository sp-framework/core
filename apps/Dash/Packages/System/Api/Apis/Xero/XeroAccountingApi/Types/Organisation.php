<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class Organisation extends XeroType
{
    private static $propertyTypes = [
        'OrganisationID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrganisationID',
        ],
        'APIKey' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'APIKey',
        ],
        'Name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Name',
        ],
        'LegalName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LegalName',
        ],
        'PaysTax' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaysTax',
        ],
        'Version' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Version',
        ],
        'OrganisationType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrganisationType',
        ],
        'BaseCurrency' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BaseCurrency',
        ],
        'CountryCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CountryCode',
        ],
        'IsDemoCompany' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IsDemoCompany',
        ],
        'OrganisationStatus' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrganisationStatus',
        ],
        'RegistrationNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RegistrationNumber',
        ],
        'EmployerIdentificationNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EmployerIdentificationNumber',
        ],
        'TaxNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TaxNumber',
        ],
        'FinancialYearEndDay' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FinancialYearEndDay',
        ],
        'FinancialYearEndMonth' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FinancialYearEndMonth',
        ],
        'SalesTaxBasis' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SalesTaxBasis',
        ],
        'SalesTaxPeriod' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SalesTaxPeriod',
        ],
        'DefaultSalesTax' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DefaultSalesTax',
        ],
        'DefaultPurchasesTax' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DefaultPurchasesTax',
        ],
        'PeriodLockDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PeriodLockDate',
        ],
        'EndOfYearLockDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EndOfYearLockDate',
        ],
        'CreatedDateUTC' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CreatedDateUTC',
        ],
        'Timezone' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Timezone',
        ],
        'OrganisationEntityType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrganisationEntityType',
        ],
        'ShortCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShortCode',
        ],
        'Class' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Class',
        ],
        'Edition' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Edition',
        ],
        'LineOfBusiness' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LineOfBusiness',
        ],
        'Addresses' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\AddressForOrganisation',
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
        'ExternalLinks' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ExternalLink',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ExternalLinks',
        ],
        'PaymentTerms' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\PaymentTerm',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentTerms',
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