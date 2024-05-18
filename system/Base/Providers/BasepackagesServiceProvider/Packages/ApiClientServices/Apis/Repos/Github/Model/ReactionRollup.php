<?php
/**
 * ReactionRollup
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
 * ReactionRollup Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class ReactionRollup implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'reaction-rollup';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'url' => 'string',
        'total_count' => 'int',
        '_1' => 'int',
        '_1' => 'int',
        'laugh' => 'int',
        'confused' => 'int',
        'heart' => 'int',
        'hooray' => 'int',
        'eyes' => 'int',
        'rocket' => 'int'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'url' => 'uri',
        'total_count' => null,
        '_1' => null,
        '_1' => null,
        'laugh' => null,
        'confused' => null,
        'heart' => null,
        'hooray' => null,
        'eyes' => null,
        'rocket' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'url' => false,
        'total_count' => false,
        '_1' => false,
        '_1' => false,
        'laugh' => false,
        'confused' => false,
        'heart' => false,
        'hooray' => false,
        'eyes' => false,
        'rocket' => false
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
        'url' => 'url',
        'total_count' => 'total_count',
        '_1' => '+1',
        '_1' => '-1',
        'laugh' => 'laugh',
        'confused' => 'confused',
        'heart' => 'heart',
        'hooray' => 'hooray',
        'eyes' => 'eyes',
        'rocket' => 'rocket'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'url' => 'setUrl',
        'total_count' => 'setTotalCount',
        '_1' => 'set1',
        '_1' => 'set1',
        'laugh' => 'setLaugh',
        'confused' => 'setConfused',
        'heart' => 'setHeart',
        'hooray' => 'setHooray',
        'eyes' => 'setEyes',
        'rocket' => 'setRocket'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'url' => 'getUrl',
        'total_count' => 'getTotalCount',
        '_1' => 'get1',
        '_1' => 'get1',
        'laugh' => 'getLaugh',
        'confused' => 'getConfused',
        'heart' => 'getHeart',
        'hooray' => 'getHooray',
        'eyes' => 'getEyes',
        'rocket' => 'getRocket'
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
        $this->setIfExists('url', $data ?? [], null);
        $this->setIfExists('total_count', $data ?? [], null);
        $this->setIfExists('_1', $data ?? [], null);
        $this->setIfExists('_1', $data ?? [], null);
        $this->setIfExists('laugh', $data ?? [], null);
        $this->setIfExists('confused', $data ?? [], null);
        $this->setIfExists('heart', $data ?? [], null);
        $this->setIfExists('hooray', $data ?? [], null);
        $this->setIfExists('eyes', $data ?? [], null);
        $this->setIfExists('rocket', $data ?? [], null);
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

        if ($this->container['url'] === null) {
            $invalidProperties[] = "'url' can't be null";
        }
        if ($this->container['total_count'] === null) {
            $invalidProperties[] = "'total_count' can't be null";
        }
        if ($this->container['_1'] === null) {
            $invalidProperties[] = "'_1' can't be null";
        }
        if ($this->container['_1'] === null) {
            $invalidProperties[] = "'_1' can't be null";
        }
        if ($this->container['laugh'] === null) {
            $invalidProperties[] = "'laugh' can't be null";
        }
        if ($this->container['confused'] === null) {
            $invalidProperties[] = "'confused' can't be null";
        }
        if ($this->container['heart'] === null) {
            $invalidProperties[] = "'heart' can't be null";
        }
        if ($this->container['hooray'] === null) {
            $invalidProperties[] = "'hooray' can't be null";
        }
        if ($this->container['eyes'] === null) {
            $invalidProperties[] = "'eyes' can't be null";
        }
        if ($this->container['rocket'] === null) {
            $invalidProperties[] = "'rocket' can't be null";
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
     * Gets total_count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->container['total_count'];
    }

    /**
     * Sets total_count
     *
     * @param int $total_count total_count
     *
     * @return self
     */
    public function setTotalCount($total_count)
    {
        if (is_null($total_count)) {
            throw new \InvalidArgumentException('non-nullable total_count cannot be null');
        }
        $this->container['total_count'] = $total_count;

        return $this;
    }

    /**
     * Gets _1
     *
     * @return int
     */
    public function get1()
    {
        return $this->container['_1'];
    }

    /**
     * Sets _1
     *
     * @param int $_1 _1
     *
     * @return self
     */
    public function set1($_1)
    {
        if (is_null($_1)) {
            throw new \InvalidArgumentException('non-nullable _1 cannot be null');
        }
        $this->container['_1'] = $_1;

        return $this;
    }

    /**
     * Gets _1
     *
     * @return int
     */
    public function get1()
    {
        return $this->container['_1'];
    }

    /**
     * Sets _1
     *
     * @param int $_1 _1
     *
     * @return self
     */
    public function set1($_1)
    {
        if (is_null($_1)) {
            throw new \InvalidArgumentException('non-nullable _1 cannot be null');
        }
        $this->container['_1'] = $_1;

        return $this;
    }

    /**
     * Gets laugh
     *
     * @return int
     */
    public function getLaugh()
    {
        return $this->container['laugh'];
    }

    /**
     * Sets laugh
     *
     * @param int $laugh laugh
     *
     * @return self
     */
    public function setLaugh($laugh)
    {
        if (is_null($laugh)) {
            throw new \InvalidArgumentException('non-nullable laugh cannot be null');
        }
        $this->container['laugh'] = $laugh;

        return $this;
    }

    /**
     * Gets confused
     *
     * @return int
     */
    public function getConfused()
    {
        return $this->container['confused'];
    }

    /**
     * Sets confused
     *
     * @param int $confused confused
     *
     * @return self
     */
    public function setConfused($confused)
    {
        if (is_null($confused)) {
            throw new \InvalidArgumentException('non-nullable confused cannot be null');
        }
        $this->container['confused'] = $confused;

        return $this;
    }

    /**
     * Gets heart
     *
     * @return int
     */
    public function getHeart()
    {
        return $this->container['heart'];
    }

    /**
     * Sets heart
     *
     * @param int $heart heart
     *
     * @return self
     */
    public function setHeart($heart)
    {
        if (is_null($heart)) {
            throw new \InvalidArgumentException('non-nullable heart cannot be null');
        }
        $this->container['heart'] = $heart;

        return $this;
    }

    /**
     * Gets hooray
     *
     * @return int
     */
    public function getHooray()
    {
        return $this->container['hooray'];
    }

    /**
     * Sets hooray
     *
     * @param int $hooray hooray
     *
     * @return self
     */
    public function setHooray($hooray)
    {
        if (is_null($hooray)) {
            throw new \InvalidArgumentException('non-nullable hooray cannot be null');
        }
        $this->container['hooray'] = $hooray;

        return $this;
    }

    /**
     * Gets eyes
     *
     * @return int
     */
    public function getEyes()
    {
        return $this->container['eyes'];
    }

    /**
     * Sets eyes
     *
     * @param int $eyes eyes
     *
     * @return self
     */
    public function setEyes($eyes)
    {
        if (is_null($eyes)) {
            throw new \InvalidArgumentException('non-nullable eyes cannot be null');
        }
        $this->container['eyes'] = $eyes;

        return $this;
    }

    /**
     * Gets rocket
     *
     * @return int
     */
    public function getRocket()
    {
        return $this->container['rocket'];
    }

    /**
     * Sets rocket
     *
     * @param int $rocket rocket
     *
     * @return self
     */
    public function setRocket($rocket)
    {
        if (is_null($rocket)) {
            throw new \InvalidArgumentException('non-nullable rocket cannot be null');
        }
        $this->container['rocket'] = $rocket;

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


