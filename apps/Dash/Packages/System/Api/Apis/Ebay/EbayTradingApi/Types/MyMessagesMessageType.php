<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class MyMessagesMessageType extends BaseType
{
    private static $propertyTypes = [
        'Sender' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Sender',
        ],
        'RecipientUserID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RecipientUserID',
        ],
        'SendToName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SendToName',
        ],
        'Subject' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Subject',
        ],
        'MessageID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MessageID',
        ],
        'ExternalMessageID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExternalMessageID',
        ],
        'ContentType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ContentType',
        ],
        'Text' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Text',
        ],
        'Flagged' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Flagged',
        ],
        'Read' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Read',
        ],
        'CreationDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CreationDate',
        ],
        'ReceiveDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReceiveDate',
        ],
        'ExpirationDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpirationDate',
        ],
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'ResponseDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MyMessagesResponseDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ResponseDetails',
        ],
        'ForwardDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MyMessagesForwardDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ForwardDetails',
        ],
        'Folder' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MyMessagesFolderType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Folder',
        ],
        'Content' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Content',
        ],
        'MessageType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MessageTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MessageType',
        ],
        'ListingStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ListingStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ListingStatus',
        ],
        'QuestionType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\QuestionTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QuestionType',
        ],
        'Replied' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Replied',
        ],
        'HighPriority' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HighPriority',
        ],
        'ItemEndTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemEndTime',
        ],
        'ItemTitle' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemTitle',
        ],
        'MessageMedia' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MessageMediaType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'MessageMedia',
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