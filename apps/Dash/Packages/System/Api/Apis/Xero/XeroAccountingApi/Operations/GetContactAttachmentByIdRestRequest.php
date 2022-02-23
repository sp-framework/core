<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class GetContactAttachmentByIdRestRequest extends BaseType
{
    private static $propertyTypes = [
        'ContactID' => [
          'type' =>       'string',
          'attribute' => false,
          'repeatable' => false,
          'elementName' => 'ContactID',
        ],
        'AttachmentID' => [
          'type' =>       'string',
          'attribute' => false,
          'repeatable' => false,
          'elementName' => 'AttachmentID',
        ],
        'ContentType' => [
          'type' =>       'string',
          'attribute' => false,
          'repeatable' => false,
          'elementName' => 'ContentType',
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