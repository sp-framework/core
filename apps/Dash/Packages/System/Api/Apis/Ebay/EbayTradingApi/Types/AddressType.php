<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class AddressType extends BaseType
{
    private static $propertyTypes = [
        'Name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Name',
        ],
        'Street' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Street',
        ],
        'Street1' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Street1',
        ],
        'Street2' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Street2',
        ],
        'CityName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CityName',
        ],
        'County' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'County',
        ],
        'StateOrProvince' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StateOrProvince',
        ],
        'Country' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CountryCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Country',
        ],
        'CountryName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CountryName',
        ],
        'Phone' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Phone',
        ],
        'PhoneCountryCode' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CountryCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PhoneCountryCode',
        ],
        'PhoneCountryPrefix' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PhoneCountryPrefix',
        ],
        'PhoneAreaOrCityCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PhoneAreaOrCityCode',
        ],
        'PhoneLocalNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PhoneLocalNumber',
        ],
        'PostalCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PostalCode',
        ],
        'AddressID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AddressID',
        ],
        'AddressOwner' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressOwnerCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AddressOwner',
        ],
        'AddressStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AddressStatus',
        ],
        'ExternalAddressID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExternalAddressID',
        ],
        'InternationalName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalName',
        ],
        'InternationalStateAndCity' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalStateAndCity',
        ],
        'InternationalStreet' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalStreet',
        ],
        'CompanyName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CompanyName',
        ],
        'AddressRecordType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressRecordTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AddressRecordType',
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
        'Phone2' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Phone2',
        ],
        'AddressUsage' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressUsageCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AddressUsage',
        ],
        'ReferenceID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReferenceID',
        ],
        'AddressAttribute' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressAttributeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'AddressAttribute',
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