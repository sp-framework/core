<?php
/**
 * WebhooksMarketplacePurchasePlan
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
 * WebhooksMarketplacePurchasePlan Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class WebhooksMarketplacePurchasePlan implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'webhooks_marketplace_purchase_plan';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'bullets' => 'string[]',
        'description' => 'string',
        'has_free_trial' => 'bool',
        'id' => 'int',
        'monthly_price_in_cents' => 'int',
        'name' => 'string',
        'price_model' => 'string',
        'unit_name' => 'string',
        'yearly_price_in_cents' => 'int'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'bullets' => null,
        'description' => null,
        'has_free_trial' => null,
        'id' => null,
        'monthly_price_in_cents' => null,
        'name' => null,
        'price_model' => null,
        'unit_name' => null,
        'yearly_price_in_cents' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'bullets' => false,
        'description' => false,
        'has_free_trial' => false,
        'id' => false,
        'monthly_price_in_cents' => false,
        'name' => false,
        'price_model' => false,
        'unit_name' => true,
        'yearly_price_in_cents' => false
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
        'bullets' => 'bullets',
        'description' => 'description',
        'has_free_trial' => 'has_free_trial',
        'id' => 'id',
        'monthly_price_in_cents' => 'monthly_price_in_cents',
        'name' => 'name',
        'price_model' => 'price_model',
        'unit_name' => 'unit_name',
        'yearly_price_in_cents' => 'yearly_price_in_cents'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'bullets' => 'setBullets',
        'description' => 'setDescription',
        'has_free_trial' => 'setHasFreeTrial',
        'id' => 'setId',
        'monthly_price_in_cents' => 'setMonthlyPriceInCents',
        'name' => 'setName',
        'price_model' => 'setPriceModel',
        'unit_name' => 'setUnitName',
        'yearly_price_in_cents' => 'setYearlyPriceInCents'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'bullets' => 'getBullets',
        'description' => 'getDescription',
        'has_free_trial' => 'getHasFreeTrial',
        'id' => 'getId',
        'monthly_price_in_cents' => 'getMonthlyPriceInCents',
        'name' => 'getName',
        'price_model' => 'getPriceModel',
        'unit_name' => 'getUnitName',
        'yearly_price_in_cents' => 'getYearlyPriceInCents'
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

    public const PRICE_MODEL_FREE = 'FREE';
    public const PRICE_MODEL_FLAT_RATE = 'FLAT_RATE';
    public const PRICE_MODEL_PER_UNIT = 'PER_UNIT';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPriceModelAllowableValues()
    {
        return [
            self::PRICE_MODEL_FREE,
            self::PRICE_MODEL_FLAT_RATE,
            self::PRICE_MODEL_PER_UNIT,
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
        $this->setIfExists('bullets', $data ?? [], null);
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('has_free_trial', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('monthly_price_in_cents', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('price_model', $data ?? [], null);
        $this->setIfExists('unit_name', $data ?? [], null);
        $this->setIfExists('yearly_price_in_cents', $data ?? [], null);
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

        if ($this->container['bullets'] === null) {
            $invalidProperties[] = "'bullets' can't be null";
        }
        if ($this->container['description'] === null) {
            $invalidProperties[] = "'description' can't be null";
        }
        if ($this->container['has_free_trial'] === null) {
            $invalidProperties[] = "'has_free_trial' can't be null";
        }
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['monthly_price_in_cents'] === null) {
            $invalidProperties[] = "'monthly_price_in_cents' can't be null";
        }
        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        if ($this->container['price_model'] === null) {
            $invalidProperties[] = "'price_model' can't be null";
        }
        $allowedValues = $this->getPriceModelAllowableValues();
        if (!is_null($this->container['price_model']) && !in_array($this->container['price_model'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'price_model', must be one of '%s'",
                $this->container['price_model'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['unit_name'] === null) {
            $invalidProperties[] = "'unit_name' can't be null";
        }
        if ($this->container['yearly_price_in_cents'] === null) {
            $invalidProperties[] = "'yearly_price_in_cents' can't be null";
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
     * Gets bullets
     *
     * @return string[]
     */
    public function getBullets()
    {
        return $this->container['bullets'];
    }

    /**
     * Sets bullets
     *
     * @param string[] $bullets bullets
     *
     * @return self
     */
    public function setBullets($bullets)
    {
        if (is_null($bullets)) {
            throw new \InvalidArgumentException('non-nullable bullets cannot be null');
        }
        $this->container['bullets'] = $bullets;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     *
     * @param string $description description
     *
     * @return self
     */
    public function setDescription($description)
    {
        if (is_null($description)) {
            throw new \InvalidArgumentException('non-nullable description cannot be null');
        }
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets has_free_trial
     *
     * @return bool
     */
    public function getHasFreeTrial()
    {
        return $this->container['has_free_trial'];
    }

    /**
     * Sets has_free_trial
     *
     * @param bool $has_free_trial has_free_trial
     *
     * @return self
     */
    public function setHasFreeTrial($has_free_trial)
    {
        if (is_null($has_free_trial)) {
            throw new \InvalidArgumentException('non-nullable has_free_trial cannot be null');
        }
        $this->container['has_free_trial'] = $has_free_trial;

        return $this;
    }

    /**
     * Gets id
     *
     * @return int
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param int $id id
     *
     * @return self
     */
    public function setId($id)
    {
        if (is_null($id)) {
            throw new \InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets monthly_price_in_cents
     *
     * @return int
     */
    public function getMonthlyPriceInCents()
    {
        return $this->container['monthly_price_in_cents'];
    }

    /**
     * Sets monthly_price_in_cents
     *
     * @param int $monthly_price_in_cents monthly_price_in_cents
     *
     * @return self
     */
    public function setMonthlyPriceInCents($monthly_price_in_cents)
    {
        if (is_null($monthly_price_in_cents)) {
            throw new \InvalidArgumentException('non-nullable monthly_price_in_cents cannot be null');
        }
        $this->container['monthly_price_in_cents'] = $monthly_price_in_cents;

        return $this;
    }

    /**
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string $name name
     *
     * @return self
     */
    public function setName($name)
    {
        if (is_null($name)) {
            throw new \InvalidArgumentException('non-nullable name cannot be null');
        }
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets price_model
     *
     * @return string
     */
    public function getPriceModel()
    {
        return $this->container['price_model'];
    }

    /**
     * Sets price_model
     *
     * @param string $price_model price_model
     *
     * @return self
     */
    public function setPriceModel($price_model)
    {
        if (is_null($price_model)) {
            throw new \InvalidArgumentException('non-nullable price_model cannot be null');
        }
        $allowedValues = $this->getPriceModelAllowableValues();
        if (!in_array($price_model, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'price_model', must be one of '%s'",
                    $price_model,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['price_model'] = $price_model;

        return $this;
    }

    /**
     * Gets unit_name
     *
     * @return string
     */
    public function getUnitName()
    {
        return $this->container['unit_name'];
    }

    /**
     * Sets unit_name
     *
     * @param string $unit_name unit_name
     *
     * @return self
     */
    public function setUnitName($unit_name)
    {
        if (is_null($unit_name)) {
            array_push($this->openAPINullablesSetToNull, 'unit_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('unit_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['unit_name'] = $unit_name;

        return $this;
    }

    /**
     * Gets yearly_price_in_cents
     *
     * @return int
     */
    public function getYearlyPriceInCents()
    {
        return $this->container['yearly_price_in_cents'];
    }

    /**
     * Sets yearly_price_in_cents
     *
     * @param int $yearly_price_in_cents yearly_price_in_cents
     *
     * @return self
     */
    public function setYearlyPriceInCents($yearly_price_in_cents)
    {
        if (is_null($yearly_price_in_cents)) {
            throw new \InvalidArgumentException('non-nullable yearly_price_in_cents cannot be null');
        }
        $this->container['yearly_price_in_cents'] = $yearly_price_in_cents;

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


