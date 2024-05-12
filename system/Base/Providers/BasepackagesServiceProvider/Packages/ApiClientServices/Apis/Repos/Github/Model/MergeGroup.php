<?php
/**
 * MergeGroup
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
 * MergeGroup Class Doc Comment
 *
 * @category Class
 * @description A group of pull requests that the merge queue has grouped together to be merged.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class MergeGroup implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'merge-group';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'head_sha' => 'string',
        'head_ref' => 'string',
        'base_sha' => 'string',
        'base_ref' => 'string',
        'head_commit' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleCommit'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'head_sha' => null,
        'head_ref' => null,
        'base_sha' => null,
        'base_ref' => null,
        'head_commit' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'head_sha' => false,
        'head_ref' => false,
        'base_sha' => false,
        'base_ref' => false,
        'head_commit' => false
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
        'head_sha' => 'head_sha',
        'head_ref' => 'head_ref',
        'base_sha' => 'base_sha',
        'base_ref' => 'base_ref',
        'head_commit' => 'head_commit'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'head_sha' => 'setHeadSha',
        'head_ref' => 'setHeadRef',
        'base_sha' => 'setBaseSha',
        'base_ref' => 'setBaseRef',
        'head_commit' => 'setHeadCommit'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'head_sha' => 'getHeadSha',
        'head_ref' => 'getHeadRef',
        'base_sha' => 'getBaseSha',
        'base_ref' => 'getBaseRef',
        'head_commit' => 'getHeadCommit'
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
        $this->setIfExists('head_sha', $data ?? [], null);
        $this->setIfExists('head_ref', $data ?? [], null);
        $this->setIfExists('base_sha', $data ?? [], null);
        $this->setIfExists('base_ref', $data ?? [], null);
        $this->setIfExists('head_commit', $data ?? [], null);
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

        if ($this->container['head_sha'] === null) {
            $invalidProperties[] = "'head_sha' can't be null";
        }
        if ($this->container['head_ref'] === null) {
            $invalidProperties[] = "'head_ref' can't be null";
        }
        if ($this->container['base_sha'] === null) {
            $invalidProperties[] = "'base_sha' can't be null";
        }
        if ($this->container['base_ref'] === null) {
            $invalidProperties[] = "'base_ref' can't be null";
        }
        if ($this->container['head_commit'] === null) {
            $invalidProperties[] = "'head_commit' can't be null";
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
     * Gets head_sha
     *
     * @return string
     */
    public function getHeadSha()
    {
        return $this->container['head_sha'];
    }

    /**
     * Sets head_sha
     *
     * @param string $head_sha The SHA of the merge group.
     *
     * @return self
     */
    public function setHeadSha($head_sha)
    {
        if (is_null($head_sha)) {
            throw new \InvalidArgumentException('non-nullable head_sha cannot be null');
        }
        $this->container['head_sha'] = $head_sha;

        return $this;
    }

    /**
     * Gets head_ref
     *
     * @return string
     */
    public function getHeadRef()
    {
        return $this->container['head_ref'];
    }

    /**
     * Sets head_ref
     *
     * @param string $head_ref The full ref of the merge group.
     *
     * @return self
     */
    public function setHeadRef($head_ref)
    {
        if (is_null($head_ref)) {
            throw new \InvalidArgumentException('non-nullable head_ref cannot be null');
        }
        $this->container['head_ref'] = $head_ref;

        return $this;
    }

    /**
     * Gets base_sha
     *
     * @return string
     */
    public function getBaseSha()
    {
        return $this->container['base_sha'];
    }

    /**
     * Sets base_sha
     *
     * @param string $base_sha The SHA of the merge group's parent commit.
     *
     * @return self
     */
    public function setBaseSha($base_sha)
    {
        if (is_null($base_sha)) {
            throw new \InvalidArgumentException('non-nullable base_sha cannot be null');
        }
        $this->container['base_sha'] = $base_sha;

        return $this;
    }

    /**
     * Gets base_ref
     *
     * @return string
     */
    public function getBaseRef()
    {
        return $this->container['base_ref'];
    }

    /**
     * Sets base_ref
     *
     * @param string $base_ref The full ref of the branch the merge group will be merged into.
     *
     * @return self
     */
    public function setBaseRef($base_ref)
    {
        if (is_null($base_ref)) {
            throw new \InvalidArgumentException('non-nullable base_ref cannot be null');
        }
        $this->container['base_ref'] = $base_ref;

        return $this;
    }

    /**
     * Gets head_commit
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleCommit
     */
    public function getHeadCommit()
    {
        return $this->container['head_commit'];
    }

    /**
     * Sets head_commit
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleCommit $head_commit head_commit
     *
     * @return self
     */
    public function setHeadCommit($head_commit)
    {
        if (is_null($head_commit)) {
            throw new \InvalidArgumentException('non-nullable head_commit cannot be null');
        }
        $this->container['head_commit'] = $head_commit;

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


