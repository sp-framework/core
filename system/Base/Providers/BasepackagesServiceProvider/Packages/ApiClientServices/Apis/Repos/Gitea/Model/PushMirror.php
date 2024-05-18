<?php
/**
 * PushMirror
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Gitea API
 *
 * This documentation describes the Gitea API.
 *
 * The version of the OpenAPI document: 1.21.7
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.5.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model;

use \ArrayAccess;
use \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\ObjectSerializer;

/**
 * PushMirror Class Doc Comment
 *
 * @category Class
 * @description PushMirror represents information of a push mirror
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class PushMirror implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'PushMirror';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'created' => 'string',
        'interval' => 'string',
        'last_error' => 'string',
        'last_update' => 'string',
        'remote_address' => 'string',
        'remote_name' => 'string',
        'repo_name' => 'string',
        'sync_on_commit' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'created' => null,
        'interval' => null,
        'last_error' => null,
        'last_update' => null,
        'remote_address' => null,
        'remote_name' => null,
        'repo_name' => null,
        'sync_on_commit' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'created' => false,
        'interval' => false,
        'last_error' => false,
        'last_update' => false,
        'remote_address' => false,
        'remote_name' => false,
        'repo_name' => false,
        'sync_on_commit' => false
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
        'created' => 'created',
        'interval' => 'interval',
        'last_error' => 'last_error',
        'last_update' => 'last_update',
        'remote_address' => 'remote_address',
        'remote_name' => 'remote_name',
        'repo_name' => 'repo_name',
        'sync_on_commit' => 'sync_on_commit'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'created' => 'setCreated',
        'interval' => 'setInterval',
        'last_error' => 'setLastError',
        'last_update' => 'setLastUpdate',
        'remote_address' => 'setRemoteAddress',
        'remote_name' => 'setRemoteName',
        'repo_name' => 'setRepoName',
        'sync_on_commit' => 'setSyncOnCommit'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'created' => 'getCreated',
        'interval' => 'getInterval',
        'last_error' => 'getLastError',
        'last_update' => 'getLastUpdate',
        'remote_address' => 'getRemoteAddress',
        'remote_name' => 'getRemoteName',
        'repo_name' => 'getRepoName',
        'sync_on_commit' => 'getSyncOnCommit'
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
        $this->setIfExists('created', $data ?? [], null);
        $this->setIfExists('interval', $data ?? [], null);
        $this->setIfExists('last_error', $data ?? [], null);
        $this->setIfExists('last_update', $data ?? [], null);
        $this->setIfExists('remote_address', $data ?? [], null);
        $this->setIfExists('remote_name', $data ?? [], null);
        $this->setIfExists('repo_name', $data ?? [], null);
        $this->setIfExists('sync_on_commit', $data ?? [], null);
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
     * Gets created
     *
     * @return string|null
     */
    public function getCreated()
    {
        return $this->container['created'];
    }

    /**
     * Sets created
     *
     * @param string|null $created created
     *
     * @return self
     */
    public function setCreated($created)
    {
        if (is_null($created)) {
            throw new \InvalidArgumentException('non-nullable created cannot be null');
        }
        $this->container['created'] = $created;

        return $this;
    }

    /**
     * Gets interval
     *
     * @return string|null
     */
    public function getInterval()
    {
        return $this->container['interval'];
    }

    /**
     * Sets interval
     *
     * @param string|null $interval interval
     *
     * @return self
     */
    public function setInterval($interval)
    {
        if (is_null($interval)) {
            throw new \InvalidArgumentException('non-nullable interval cannot be null');
        }
        $this->container['interval'] = $interval;

        return $this;
    }

    /**
     * Gets last_error
     *
     * @return string|null
     */
    public function getLastError()
    {
        return $this->container['last_error'];
    }

    /**
     * Sets last_error
     *
     * @param string|null $last_error last_error
     *
     * @return self
     */
    public function setLastError($last_error)
    {
        if (is_null($last_error)) {
            throw new \InvalidArgumentException('non-nullable last_error cannot be null');
        }
        $this->container['last_error'] = $last_error;

        return $this;
    }

    /**
     * Gets last_update
     *
     * @return string|null
     */
    public function getLastUpdate()
    {
        return $this->container['last_update'];
    }

    /**
     * Sets last_update
     *
     * @param string|null $last_update last_update
     *
     * @return self
     */
    public function setLastUpdate($last_update)
    {
        if (is_null($last_update)) {
            throw new \InvalidArgumentException('non-nullable last_update cannot be null');
        }
        $this->container['last_update'] = $last_update;

        return $this;
    }

    /**
     * Gets remote_address
     *
     * @return string|null
     */
    public function getRemoteAddress()
    {
        return $this->container['remote_address'];
    }

    /**
     * Sets remote_address
     *
     * @param string|null $remote_address remote_address
     *
     * @return self
     */
    public function setRemoteAddress($remote_address)
    {
        if (is_null($remote_address)) {
            throw new \InvalidArgumentException('non-nullable remote_address cannot be null');
        }
        $this->container['remote_address'] = $remote_address;

        return $this;
    }

    /**
     * Gets remote_name
     *
     * @return string|null
     */
    public function getRemoteName()
    {
        return $this->container['remote_name'];
    }

    /**
     * Sets remote_name
     *
     * @param string|null $remote_name remote_name
     *
     * @return self
     */
    public function setRemoteName($remote_name)
    {
        if (is_null($remote_name)) {
            throw new \InvalidArgumentException('non-nullable remote_name cannot be null');
        }
        $this->container['remote_name'] = $remote_name;

        return $this;
    }

    /**
     * Gets repo_name
     *
     * @return string|null
     */
    public function getRepoName()
    {
        return $this->container['repo_name'];
    }

    /**
     * Sets repo_name
     *
     * @param string|null $repo_name repo_name
     *
     * @return self
     */
    public function setRepoName($repo_name)
    {
        if (is_null($repo_name)) {
            throw new \InvalidArgumentException('non-nullable repo_name cannot be null');
        }
        $this->container['repo_name'] = $repo_name;

        return $this;
    }

    /**
     * Gets sync_on_commit
     *
     * @return bool|null
     */
    public function getSyncOnCommit()
    {
        return $this->container['sync_on_commit'];
    }

    /**
     * Sets sync_on_commit
     *
     * @param bool|null $sync_on_commit sync_on_commit
     *
     * @return self
     */
    public function setSyncOnCommit($sync_on_commit)
    {
        if (is_null($sync_on_commit)) {
            throw new \InvalidArgumentException('non-nullable sync_on_commit cannot be null');
        }
        $this->container['sync_on_commit'] = $sync_on_commit;

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


