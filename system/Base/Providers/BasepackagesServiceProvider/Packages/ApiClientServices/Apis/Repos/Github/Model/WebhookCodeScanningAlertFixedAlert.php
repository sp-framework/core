<?php
/**
 * WebhookCodeScanningAlertFixedAlert
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * GitHub v3 REST API
 *
 * GitHub's v3 REST API.
 *
 * The version of the OpenAPI document: 1.1.4
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.5.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model;

use \ArrayAccess;
use \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\ObjectSerializer;

/**
 * WebhookCodeScanningAlertFixedAlert Class Doc Comment
 *
 * @category Class
 * @description The code scanning alert involved in the event.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class WebhookCodeScanningAlertFixedAlert implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'webhook_code_scanning_alert_fixed_alert';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'created_at' => '\DateTime',
        'dismissed_at' => '\DateTime',
        'dismissed_by' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User',
        'dismissed_reason' => 'string',
        'html_url' => 'string',
        'instances_url' => 'string',
        'most_recent_instance' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\AlertInstance',
        'number' => 'int',
        'rule' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookCodeScanningAlertClosedByUserAlertRule',
        'state' => 'string',
        'tool' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookCodeScanningAlertClosedByUserAlertTool',
        'url' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'created_at' => 'date-time',
        'dismissed_at' => 'date-time',
        'dismissed_by' => null,
        'dismissed_reason' => null,
        'html_url' => 'uri',
        'instances_url' => 'uri',
        'most_recent_instance' => null,
        'number' => null,
        'rule' => null,
        'state' => null,
        'tool' => null,
        'url' => 'uri'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'created_at' => false,
        'dismissed_at' => true,
        'dismissed_by' => true,
        'dismissed_reason' => true,
        'html_url' => false,
        'instances_url' => false,
        'most_recent_instance' => true,
        'number' => false,
        'rule' => false,
        'state' => false,
        'tool' => false,
        'url' => false
    ];

    /**
      * If a nullable field gets set to null, insert it here
      *
      * @var boolean[]
      */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'created_at' => 'created_at',
        'dismissed_at' => 'dismissed_at',
        'dismissed_by' => 'dismissed_by',
        'dismissed_reason' => 'dismissed_reason',
        'html_url' => 'html_url',
        'instances_url' => 'instances_url',
        'most_recent_instance' => 'most_recent_instance',
        'number' => 'number',
        'rule' => 'rule',
        'state' => 'state',
        'tool' => 'tool',
        'url' => 'url'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'created_at' => 'setCreatedAt',
        'dismissed_at' => 'setDismissedAt',
        'dismissed_by' => 'setDismissedBy',
        'dismissed_reason' => 'setDismissedReason',
        'html_url' => 'setHtmlUrl',
        'instances_url' => 'setInstancesUrl',
        'most_recent_instance' => 'setMostRecentInstance',
        'number' => 'setNumber',
        'rule' => 'setRule',
        'state' => 'setState',
        'tool' => 'setTool',
        'url' => 'setUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'created_at' => 'getCreatedAt',
        'dismissed_at' => 'getDismissedAt',
        'dismissed_by' => 'getDismissedBy',
        'dismissed_reason' => 'getDismissedReason',
        'html_url' => 'getHtmlUrl',
        'instances_url' => 'getInstancesUrl',
        'most_recent_instance' => 'getMostRecentInstance',
        'number' => 'getNumber',
        'rule' => 'getRule',
        'state' => 'getState',
        'tool' => 'getTool',
        'url' => 'getUrl'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    public const DISMISSED_REASON_FALSE_POSITIVE = 'false positive';
    public const DISMISSED_REASON_WONT_FIX = 'won't fix';
    public const DISMISSED_REASON_USED_IN_TESTS = 'used in tests';
    public const DISMISSED_REASON_NULL = 'null';
    public const STATE_FIXED = 'fixed';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getDismissedReasonAllowableValues()
    {
        return [
            self::DISMISSED_REASON_FALSE_POSITIVE,
            self::DISMISSED_REASON_WONT_FIX,
            self::DISMISSED_REASON_USED_IN_TESTS,
            self::DISMISSED_REASON_NULL,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getStateAllowableValues()
    {
        return [
            self::STATE_FIXED,
        ];
    }

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('dismissed_at', $data ?? [], null);
        $this->setIfExists('dismissed_by', $data ?? [], null);
        $this->setIfExists('dismissed_reason', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('instances_url', $data ?? [], null);
        $this->setIfExists('most_recent_instance', $data ?? [], null);
        $this->setIfExists('number', $data ?? [], null);
        $this->setIfExists('rule', $data ?? [], null);
        $this->setIfExists('state', $data ?? [], null);
        $this->setIfExists('tool', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
    }

    /**
    * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
    * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
    * $this->openAPINullablesSetToNull array
    *
    * @param string $variableName
    * @param array  $fields
    * @param mixed  $defaultValue
    */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['created_at'] === null) {
            $invalidProperties[] = "'created_at' can't be null";
        }
        if ($this->container['dismissed_at'] === null) {
            $invalidProperties[] = "'dismissed_at' can't be null";
        }
        if ($this->container['dismissed_by'] === null) {
            $invalidProperties[] = "'dismissed_by' can't be null";
        }
        if ($this->container['dismissed_reason'] === null) {
            $invalidProperties[] = "'dismissed_reason' can't be null";
        }
        $allowedValues = $this->getDismissedReasonAllowableValues();
        if (!is_null($this->container['dismissed_reason']) && !in_array($this->container['dismissed_reason'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'dismissed_reason', must be one of '%s'",
                $this->container['dismissed_reason'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['html_url'] === null) {
            $invalidProperties[] = "'html_url' can't be null";
        }
        if ($this->container['number'] === null) {
            $invalidProperties[] = "'number' can't be null";
        }
        if ($this->container['rule'] === null) {
            $invalidProperties[] = "'rule' can't be null";
        }
        if ($this->container['state'] === null) {
            $invalidProperties[] = "'state' can't be null";
        }
        $allowedValues = $this->getStateAllowableValues();
        if (!is_null($this->container['state']) && !in_array($this->container['state'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'state', must be one of '%s'",
                $this->container['state'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['tool'] === null) {
            $invalidProperties[] = "'tool' can't be null";
        }
        if ($this->container['url'] === null) {
            $invalidProperties[] = "'url' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param \DateTime $created_at The time that the alert was created in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ.`
     *
     * @return self
     */
    public function setCreatedAt($created_at)
    {
        if (is_null($created_at)) {
            throw new \InvalidArgumentException('non-nullable created_at cannot be null');
        }
        $this->container['created_at'] = $created_at;

        return $this;
    }

    /**
     * Gets dismissed_at
     *
     * @return \DateTime
     */
    public function getDismissedAt()
    {
        return $this->container['dismissed_at'];
    }

    /**
     * Sets dismissed_at
     *
     * @param \DateTime $dismissed_at The time that the alert was dismissed in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setDismissedAt($dismissed_at)
    {
        if (is_null($dismissed_at)) {
            array_push($this->openAPINullablesSetToNull, 'dismissed_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('dismissed_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['dismissed_at'] = $dismissed_at;

        return $this;
    }

    /**
     * Gets dismissed_by
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User
     */
    public function getDismissedBy()
    {
        return $this->container['dismissed_by'];
    }

    /**
     * Sets dismissed_by
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User $dismissed_by dismissed_by
     *
     * @return self
     */
    public function setDismissedBy($dismissed_by)
    {
        if (is_null($dismissed_by)) {
            array_push($this->openAPINullablesSetToNull, 'dismissed_by');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('dismissed_by', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['dismissed_by'] = $dismissed_by;

        return $this;
    }

    /**
     * Gets dismissed_reason
     *
     * @return string
     */
    public function getDismissedReason()
    {
        return $this->container['dismissed_reason'];
    }

    /**
     * Sets dismissed_reason
     *
     * @param string $dismissed_reason The reason for dismissing or closing the alert.
     *
     * @return self
     */
    public function setDismissedReason($dismissed_reason)
    {
        if (is_null($dismissed_reason)) {
            array_push($this->openAPINullablesSetToNull, 'dismissed_reason');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('dismissed_reason', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $allowedValues = $this->getDismissedReasonAllowableValues();
        if (!is_null($dismissed_reason) && !in_array($dismissed_reason, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'dismissed_reason', must be one of '%s'",
                    $dismissed_reason,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['dismissed_reason'] = $dismissed_reason;

        return $this;
    }

    /**
     * Gets html_url
     *
     * @return string
     */
    public function getHtmlUrl()
    {
        return $this->container['html_url'];
    }

    /**
     * Sets html_url
     *
     * @param string $html_url The GitHub URL of the alert resource.
     *
     * @return self
     */
    public function setHtmlUrl($html_url)
    {
        if (is_null($html_url)) {
            throw new \InvalidArgumentException('non-nullable html_url cannot be null');
        }
        $this->container['html_url'] = $html_url;

        return $this;
    }

    /**
     * Gets instances_url
     *
     * @return string|null
     */
    public function getInstancesUrl()
    {
        return $this->container['instances_url'];
    }

    /**
     * Sets instances_url
     *
     * @param string|null $instances_url instances_url
     *
     * @return self
     */
    public function setInstancesUrl($instances_url)
    {
        if (is_null($instances_url)) {
            throw new \InvalidArgumentException('non-nullable instances_url cannot be null');
        }
        $this->container['instances_url'] = $instances_url;

        return $this;
    }

    /**
     * Gets most_recent_instance
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\AlertInstance|null
     */
    public function getMostRecentInstance()
    {
        return $this->container['most_recent_instance'];
    }

    /**
     * Sets most_recent_instance
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\AlertInstance|null $most_recent_instance most_recent_instance
     *
     * @return self
     */
    public function setMostRecentInstance($most_recent_instance)
    {
        if (is_null($most_recent_instance)) {
            array_push($this->openAPINullablesSetToNull, 'most_recent_instance');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('most_recent_instance', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['most_recent_instance'] = $most_recent_instance;

        return $this;
    }

    /**
     * Gets number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->container['number'];
    }

    /**
     * Sets number
     *
     * @param int $number The code scanning alert number.
     *
     * @return self
     */
    public function setNumber($number)
    {
        if (is_null($number)) {
            throw new \InvalidArgumentException('non-nullable number cannot be null');
        }
        $this->container['number'] = $number;

        return $this;
    }

    /**
     * Gets rule
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookCodeScanningAlertClosedByUserAlertRule
     */
    public function getRule()
    {
        return $this->container['rule'];
    }

    /**
     * Sets rule
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookCodeScanningAlertClosedByUserAlertRule $rule rule
     *
     * @return self
     */
    public function setRule($rule)
    {
        if (is_null($rule)) {
            throw new \InvalidArgumentException('non-nullable rule cannot be null');
        }
        $this->container['rule'] = $rule;

        return $this;
    }

    /**
     * Gets state
     *
     * @return string
     */
    public function getState()
    {
        return $this->container['state'];
    }

    /**
     * Sets state
     *
     * @param string $state State of a code scanning alert.
     *
     * @return self
     */
    public function setState($state)
    {
        if (is_null($state)) {
            throw new \InvalidArgumentException('non-nullable state cannot be null');
        }
        $allowedValues = $this->getStateAllowableValues();
        if (!in_array($state, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'state', must be one of '%s'",
                    $state,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['state'] = $state;

        return $this;
    }

    /**
     * Gets tool
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookCodeScanningAlertClosedByUserAlertTool
     */
    public function getTool()
    {
        return $this->container['tool'];
    }

    /**
     * Sets tool
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookCodeScanningAlertClosedByUserAlertTool $tool tool
     *
     * @return self
     */
    public function setTool($tool)
    {
        if (is_null($tool)) {
            throw new \InvalidArgumentException('non-nullable tool cannot be null');
        }
        $this->container['tool'] = $tool;

        return $this;
    }

    /**
     * Gets url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string $url url
     *
     * @return self
     */
    public function setUrl($url)
    {
        if (is_null($url)) {
            throw new \InvalidArgumentException('non-nullable url cannot be null');
        }
        $this->container['url'] = $url;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


