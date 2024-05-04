<?php
/**
 * EnterpriseRepositoryOverview
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
 * EnterpriseRepositoryOverview Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class EnterpriseRepositoryOverview implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'enterprise-repository-overview';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'total_repos' => 'int',
        'root_repos' => 'int',
        'fork_repos' => 'int',
        'org_repos' => 'int',
        'total_pushes' => 'int',
        'total_wikis' => 'int'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'total_repos' => null,
        'root_repos' => null,
        'fork_repos' => null,
        'org_repos' => null,
        'total_pushes' => null,
        'total_wikis' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'total_repos' => false,
        'root_repos' => false,
        'fork_repos' => false,
        'org_repos' => false,
        'total_pushes' => false,
        'total_wikis' => false
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
        'total_repos' => 'total_repos',
        'root_repos' => 'root_repos',
        'fork_repos' => 'fork_repos',
        'org_repos' => 'org_repos',
        'total_pushes' => 'total_pushes',
        'total_wikis' => 'total_wikis'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'total_repos' => 'setTotalRepos',
        'root_repos' => 'setRootRepos',
        'fork_repos' => 'setForkRepos',
        'org_repos' => 'setOrgRepos',
        'total_pushes' => 'setTotalPushes',
        'total_wikis' => 'setTotalWikis'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'total_repos' => 'getTotalRepos',
        'root_repos' => 'getRootRepos',
        'fork_repos' => 'getForkRepos',
        'org_repos' => 'getOrgRepos',
        'total_pushes' => 'getTotalPushes',
        'total_wikis' => 'getTotalWikis'
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
        $this->setIfExists('total_repos', $data ?? [], null);
        $this->setIfExists('root_repos', $data ?? [], null);
        $this->setIfExists('fork_repos', $data ?? [], null);
        $this->setIfExists('org_repos', $data ?? [], null);
        $this->setIfExists('total_pushes', $data ?? [], null);
        $this->setIfExists('total_wikis', $data ?? [], null);
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

        if ($this->container['total_repos'] === null) {
            $invalidProperties[] = "'total_repos' can't be null";
        }
        if ($this->container['root_repos'] === null) {
            $invalidProperties[] = "'root_repos' can't be null";
        }
        if ($this->container['fork_repos'] === null) {
            $invalidProperties[] = "'fork_repos' can't be null";
        }
        if ($this->container['org_repos'] === null) {
            $invalidProperties[] = "'org_repos' can't be null";
        }
        if ($this->container['total_pushes'] === null) {
            $invalidProperties[] = "'total_pushes' can't be null";
        }
        if ($this->container['total_wikis'] === null) {
            $invalidProperties[] = "'total_wikis' can't be null";
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
     * Gets total_repos
     *
     * @return int
     */
    public function getTotalRepos()
    {
        return $this->container['total_repos'];
    }

    /**
     * Sets total_repos
     *
     * @param int $total_repos total_repos
     *
     * @return self
     */
    public function setTotalRepos($total_repos)
    {
        if (is_null($total_repos)) {
            throw new \InvalidArgumentException('non-nullable total_repos cannot be null');
        }
        $this->container['total_repos'] = $total_repos;

        return $this;
    }

    /**
     * Gets root_repos
     *
     * @return int
     */
    public function getRootRepos()
    {
        return $this->container['root_repos'];
    }

    /**
     * Sets root_repos
     *
     * @param int $root_repos root_repos
     *
     * @return self
     */
    public function setRootRepos($root_repos)
    {
        if (is_null($root_repos)) {
            throw new \InvalidArgumentException('non-nullable root_repos cannot be null');
        }
        $this->container['root_repos'] = $root_repos;

        return $this;
    }

    /**
     * Gets fork_repos
     *
     * @return int
     */
    public function getForkRepos()
    {
        return $this->container['fork_repos'];
    }

    /**
     * Sets fork_repos
     *
     * @param int $fork_repos fork_repos
     *
     * @return self
     */
    public function setForkRepos($fork_repos)
    {
        if (is_null($fork_repos)) {
            throw new \InvalidArgumentException('non-nullable fork_repos cannot be null');
        }
        $this->container['fork_repos'] = $fork_repos;

        return $this;
    }

    /**
     * Gets org_repos
     *
     * @return int
     */
    public function getOrgRepos()
    {
        return $this->container['org_repos'];
    }

    /**
     * Sets org_repos
     *
     * @param int $org_repos org_repos
     *
     * @return self
     */
    public function setOrgRepos($org_repos)
    {
        if (is_null($org_repos)) {
            throw new \InvalidArgumentException('non-nullable org_repos cannot be null');
        }
        $this->container['org_repos'] = $org_repos;

        return $this;
    }

    /**
     * Gets total_pushes
     *
     * @return int
     */
    public function getTotalPushes()
    {
        return $this->container['total_pushes'];
    }

    /**
     * Sets total_pushes
     *
     * @param int $total_pushes total_pushes
     *
     * @return self
     */
    public function setTotalPushes($total_pushes)
    {
        if (is_null($total_pushes)) {
            throw new \InvalidArgumentException('non-nullable total_pushes cannot be null');
        }
        $this->container['total_pushes'] = $total_pushes;

        return $this;
    }

    /**
     * Gets total_wikis
     *
     * @return int
     */
    public function getTotalWikis()
    {
        return $this->container['total_wikis'];
    }

    /**
     * Sets total_wikis
     *
     * @param int $total_wikis total_wikis
     *
     * @return self
     */
    public function setTotalWikis($total_wikis)
    {
        if (is_null($total_wikis)) {
            throw new \InvalidArgumentException('non-nullable total_wikis cannot be null');
        }
        $this->container['total_wikis'] = $total_wikis;

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


