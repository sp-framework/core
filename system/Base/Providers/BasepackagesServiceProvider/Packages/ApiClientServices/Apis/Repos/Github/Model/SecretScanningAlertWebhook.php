<?php
/**
 * SecretScanningAlertWebhook
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
 * SecretScanningAlertWebhook Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class SecretScanningAlertWebhook implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'secret-scanning-alert-webhook';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'number' => 'int',
        'created_at' => '\DateTime',
        'updated_at' => '\DateTime',
        'url' => 'string',
        'html_url' => 'string',
        'locations_url' => 'string',
        'resolution' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SecretScanningAlertResolutionWebhook',
        'resolved_at' => '\DateTime',
        'resolved_by' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableSimpleUser',
        'resolution_comment' => 'string',
        'secret_type' => 'string',
        'validity' => 'string',
        'push_protection_bypassed' => 'bool',
        'push_protection_bypassed_by' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableSimpleUser',
        'push_protection_bypassed_at' => '\DateTime'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'number' => null,
        'created_at' => 'date-time',
        'updated_at' => 'date-time',
        'url' => 'uri',
        'html_url' => 'uri',
        'locations_url' => 'uri',
        'resolution' => null,
        'resolved_at' => 'date-time',
        'resolved_by' => null,
        'resolution_comment' => null,
        'secret_type' => null,
        'validity' => null,
        'push_protection_bypassed' => null,
        'push_protection_bypassed_by' => null,
        'push_protection_bypassed_at' => 'date-time'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'number' => false,
        'created_at' => false,
        'updated_at' => true,
        'url' => false,
        'html_url' => false,
        'locations_url' => false,
        'resolution' => true,
        'resolved_at' => true,
        'resolved_by' => true,
        'resolution_comment' => true,
        'secret_type' => false,
        'validity' => false,
        'push_protection_bypassed' => true,
        'push_protection_bypassed_by' => true,
        'push_protection_bypassed_at' => true
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
        'number' => 'number',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'url' => 'url',
        'html_url' => 'html_url',
        'locations_url' => 'locations_url',
        'resolution' => 'resolution',
        'resolved_at' => 'resolved_at',
        'resolved_by' => 'resolved_by',
        'resolution_comment' => 'resolution_comment',
        'secret_type' => 'secret_type',
        'validity' => 'validity',
        'push_protection_bypassed' => 'push_protection_bypassed',
        'push_protection_bypassed_by' => 'push_protection_bypassed_by',
        'push_protection_bypassed_at' => 'push_protection_bypassed_at'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'number' => 'setNumber',
        'created_at' => 'setCreatedAt',
        'updated_at' => 'setUpdatedAt',
        'url' => 'setUrl',
        'html_url' => 'setHtmlUrl',
        'locations_url' => 'setLocationsUrl',
        'resolution' => 'setResolution',
        'resolved_at' => 'setResolvedAt',
        'resolved_by' => 'setResolvedBy',
        'resolution_comment' => 'setResolutionComment',
        'secret_type' => 'setSecretType',
        'validity' => 'setValidity',
        'push_protection_bypassed' => 'setPushProtectionBypassed',
        'push_protection_bypassed_by' => 'setPushProtectionBypassedBy',
        'push_protection_bypassed_at' => 'setPushProtectionBypassedAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'number' => 'getNumber',
        'created_at' => 'getCreatedAt',
        'updated_at' => 'getUpdatedAt',
        'url' => 'getUrl',
        'html_url' => 'getHtmlUrl',
        'locations_url' => 'getLocationsUrl',
        'resolution' => 'getResolution',
        'resolved_at' => 'getResolvedAt',
        'resolved_by' => 'getResolvedBy',
        'resolution_comment' => 'getResolutionComment',
        'secret_type' => 'getSecretType',
        'validity' => 'getValidity',
        'push_protection_bypassed' => 'getPushProtectionBypassed',
        'push_protection_bypassed_by' => 'getPushProtectionBypassedBy',
        'push_protection_bypassed_at' => 'getPushProtectionBypassedAt'
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

    public const VALIDITY_ACTIVE = 'active';
    public const VALIDITY_INACTIVE = 'inactive';
    public const VALIDITY_UNKNOWN = 'unknown';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getValidityAllowableValues()
    {
        return [
            self::VALIDITY_ACTIVE,
            self::VALIDITY_INACTIVE,
            self::VALIDITY_UNKNOWN,
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
        $this->setIfExists('number', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('updated_at', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('locations_url', $data ?? [], null);
        $this->setIfExists('resolution', $data ?? [], null);
        $this->setIfExists('resolved_at', $data ?? [], null);
        $this->setIfExists('resolved_by', $data ?? [], null);
        $this->setIfExists('resolution_comment', $data ?? [], null);
        $this->setIfExists('secret_type', $data ?? [], null);
        $this->setIfExists('validity', $data ?? [], null);
        $this->setIfExists('push_protection_bypassed', $data ?? [], null);
        $this->setIfExists('push_protection_bypassed_by', $data ?? [], null);
        $this->setIfExists('push_protection_bypassed_at', $data ?? [], null);
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

        $allowedValues = $this->getValidityAllowableValues();
        if (!is_null($this->container['validity']) && !in_array($this->container['validity'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'validity', must be one of '%s'",
                $this->container['validity'],
                implode("', '", $allowedValues)
            );
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
     * Gets number
     *
     * @return int|null
     */
    public function getNumber()
    {
        return $this->container['number'];
    }

    /**
     * Sets number
     *
     * @param int|null $number The security alert number.
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
     * Gets created_at
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param \DateTime|null $created_at The time that the alert was created in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`.
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
     * Gets updated_at
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->container['updated_at'];
    }

    /**
     * Sets updated_at
     *
     * @param \DateTime|null $updated_at The time that the alert was last updated in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setUpdatedAt($updated_at)
    {
        if (is_null($updated_at)) {
            array_push($this->openAPINullablesSetToNull, 'updated_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('updated_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['updated_at'] = $updated_at;

        return $this;
    }

    /**
     * Gets url
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string|null $url The REST API URL of the alert resource.
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
     * Gets html_url
     *
     * @return string|null
     */
    public function getHtmlUrl()
    {
        return $this->container['html_url'];
    }

    /**
     * Sets html_url
     *
     * @param string|null $html_url The GitHub URL of the alert resource.
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
     * Gets locations_url
     *
     * @return string|null
     */
    public function getLocationsUrl()
    {
        return $this->container['locations_url'];
    }

    /**
     * Sets locations_url
     *
     * @param string|null $locations_url The REST API URL of the code locations for this alert.
     *
     * @return self
     */
    public function setLocationsUrl($locations_url)
    {
        if (is_null($locations_url)) {
            throw new \InvalidArgumentException('non-nullable locations_url cannot be null');
        }
        $this->container['locations_url'] = $locations_url;

        return $this;
    }

    /**
     * Gets resolution
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SecretScanningAlertResolutionWebhook|null
     */
    public function getResolution()
    {
        return $this->container['resolution'];
    }

    /**
     * Sets resolution
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SecretScanningAlertResolutionWebhook|null $resolution resolution
     *
     * @return self
     */
    public function setResolution($resolution)
    {
        if (is_null($resolution)) {
            array_push($this->openAPINullablesSetToNull, 'resolution');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('resolution', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['resolution'] = $resolution;

        return $this;
    }

    /**
     * Gets resolved_at
     *
     * @return \DateTime|null
     */
    public function getResolvedAt()
    {
        return $this->container['resolved_at'];
    }

    /**
     * Sets resolved_at
     *
     * @param \DateTime|null $resolved_at The time that the alert was resolved in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setResolvedAt($resolved_at)
    {
        if (is_null($resolved_at)) {
            array_push($this->openAPINullablesSetToNull, 'resolved_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('resolved_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['resolved_at'] = $resolved_at;

        return $this;
    }

    /**
     * Gets resolved_by
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableSimpleUser|null
     */
    public function getResolvedBy()
    {
        return $this->container['resolved_by'];
    }

    /**
     * Sets resolved_by
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableSimpleUser|null $resolved_by resolved_by
     *
     * @return self
     */
    public function setResolvedBy($resolved_by)
    {
        if (is_null($resolved_by)) {
            array_push($this->openAPINullablesSetToNull, 'resolved_by');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('resolved_by', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['resolved_by'] = $resolved_by;

        return $this;
    }

    /**
     * Gets resolution_comment
     *
     * @return string|null
     */
    public function getResolutionComment()
    {
        return $this->container['resolution_comment'];
    }

    /**
     * Sets resolution_comment
     *
     * @param string|null $resolution_comment An optional comment to resolve an alert.
     *
     * @return self
     */
    public function setResolutionComment($resolution_comment)
    {
        if (is_null($resolution_comment)) {
            array_push($this->openAPINullablesSetToNull, 'resolution_comment');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('resolution_comment', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['resolution_comment'] = $resolution_comment;

        return $this;
    }

    /**
     * Gets secret_type
     *
     * @return string|null
     */
    public function getSecretType()
    {
        return $this->container['secret_type'];
    }

    /**
     * Sets secret_type
     *
     * @param string|null $secret_type The type of secret that secret scanning detected.
     *
     * @return self
     */
    public function setSecretType($secret_type)
    {
        if (is_null($secret_type)) {
            throw new \InvalidArgumentException('non-nullable secret_type cannot be null');
        }
        $this->container['secret_type'] = $secret_type;

        return $this;
    }

    /**
     * Gets validity
     *
     * @return string|null
     */
    public function getValidity()
    {
        return $this->container['validity'];
    }

    /**
     * Sets validity
     *
     * @param string|null $validity The token status as of the latest validity check.
     *
     * @return self
     */
    public function setValidity($validity)
    {
        if (is_null($validity)) {
            throw new \InvalidArgumentException('non-nullable validity cannot be null');
        }
        $allowedValues = $this->getValidityAllowableValues();
        if (!in_array($validity, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'validity', must be one of '%s'",
                    $validity,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['validity'] = $validity;

        return $this;
    }

    /**
     * Gets push_protection_bypassed
     *
     * @return bool|null
     */
    public function getPushProtectionBypassed()
    {
        return $this->container['push_protection_bypassed'];
    }

    /**
     * Sets push_protection_bypassed
     *
     * @param bool|null $push_protection_bypassed Whether push protection was bypassed for the detected secret.
     *
     * @return self
     */
    public function setPushProtectionBypassed($push_protection_bypassed)
    {
        if (is_null($push_protection_bypassed)) {
            array_push($this->openAPINullablesSetToNull, 'push_protection_bypassed');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('push_protection_bypassed', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['push_protection_bypassed'] = $push_protection_bypassed;

        return $this;
    }

    /**
     * Gets push_protection_bypassed_by
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableSimpleUser|null
     */
    public function getPushProtectionBypassedBy()
    {
        return $this->container['push_protection_bypassed_by'];
    }

    /**
     * Sets push_protection_bypassed_by
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableSimpleUser|null $push_protection_bypassed_by push_protection_bypassed_by
     *
     * @return self
     */
    public function setPushProtectionBypassedBy($push_protection_bypassed_by)
    {
        if (is_null($push_protection_bypassed_by)) {
            array_push($this->openAPINullablesSetToNull, 'push_protection_bypassed_by');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('push_protection_bypassed_by', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['push_protection_bypassed_by'] = $push_protection_bypassed_by;

        return $this;
    }

    /**
     * Gets push_protection_bypassed_at
     *
     * @return \DateTime|null
     */
    public function getPushProtectionBypassedAt()
    {
        return $this->container['push_protection_bypassed_at'];
    }

    /**
     * Sets push_protection_bypassed_at
     *
     * @param \DateTime|null $push_protection_bypassed_at The time that push protection was bypassed in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setPushProtectionBypassedAt($push_protection_bypassed_at)
    {
        if (is_null($push_protection_bypassed_at)) {
            array_push($this->openAPINullablesSetToNull, 'push_protection_bypassed_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('push_protection_bypassed_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['push_protection_bypassed_at'] = $push_protection_bypassed_at;

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


