<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CreateLoadBalancerRequest extends BaseType
{
    private static $propertyTypes = [
        'region' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'region',
        ],
        'vpc_id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'vpc_id',
        ],
        'name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'name',
        ],
        'algorithm' => [
          'attribute' => false,
          'elementName' => 'algorithm',
        ],
        'forwarding_rules' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\ForwardingRule',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'forwarding_rules',
        ],
        'health_check' => [
          'attribute' => false,
          'elementName' => 'health_check',
        ],
        'sticky_sessions' => [
          'attribute' => false,
          'elementName' => 'sticky_sessions',
        ],
        'redirect_http_to_https' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'redirect_http_to_https',
        ],
        'enable_proxy_protocol' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enable_proxy_protocol',
        ],
        'enable_backend_keepalive' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enable_backend_keepalive',
        ],
        'server_ids' => [
          'type' => 'integer',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'server_ids',
        ],
        'tag' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'tag',
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