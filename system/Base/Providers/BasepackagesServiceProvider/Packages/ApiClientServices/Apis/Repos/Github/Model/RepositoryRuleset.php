<?php
/**
 * RepositoryRuleset
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
 * RepositoryRuleset Class Doc Comment
 *
 * @category Class
 * @description A set of rules to apply when specified conditions are met.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class RepositoryRuleset implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'repository-ruleset';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'name' => 'string',
        'target' => 'string',
        'source_type' => 'string',
        'source' => 'string',
        'enforcement' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRuleEnforcement',
        'bypass_actors' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetBypassActor[]',
        'current_user_can_bypass' => 'string',
        'node_id' => 'string',
        '_links' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetLinks',
        'conditions' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetConditions',
        'rules' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRule[]',
        'created_at' => '\DateTime',
        'updated_at' => '\DateTime'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'id' => null,
        'name' => null,
        'target' => null,
        'source_type' => null,
        'source' => null,
        'enforcement' => null,
        'bypass_actors' => null,
        'current_user_can_bypass' => null,
        'node_id' => null,
        '_links' => null,
        'conditions' => null,
        'rules' => null,
        'created_at' => 'date-time',
        'updated_at' => 'date-time'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'name' => false,
        'target' => false,
        'source_type' => false,
        'source' => false,
        'enforcement' => false,
        'bypass_actors' => false,
        'current_user_can_bypass' => false,
        'node_id' => false,
        '_links' => false,
        'conditions' => true,
        'rules' => false,
        'created_at' => false,
        'updated_at' => false
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
        'id' => 'id',
        'name' => 'name',
        'target' => 'target',
        'source_type' => 'source_type',
        'source' => 'source',
        'enforcement' => 'enforcement',
        'bypass_actors' => 'bypass_actors',
        'current_user_can_bypass' => 'current_user_can_bypass',
        'node_id' => 'node_id',
        '_links' => '_links',
        'conditions' => 'conditions',
        'rules' => 'rules',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'name' => 'setName',
        'target' => 'setTarget',
        'source_type' => 'setSourceType',
        'source' => 'setSource',
        'enforcement' => 'setEnforcement',
        'bypass_actors' => 'setBypassActors',
        'current_user_can_bypass' => 'setCurrentUserCanBypass',
        'node_id' => 'setNodeId',
        '_links' => 'setLinks',
        'conditions' => 'setConditions',
        'rules' => 'setRules',
        'created_at' => 'setCreatedAt',
        'updated_at' => 'setUpdatedAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'name' => 'getName',
        'target' => 'getTarget',
        'source_type' => 'getSourceType',
        'source' => 'getSource',
        'enforcement' => 'getEnforcement',
        'bypass_actors' => 'getBypassActors',
        'current_user_can_bypass' => 'getCurrentUserCanBypass',
        'node_id' => 'getNodeId',
        '_links' => 'getLinks',
        'conditions' => 'getConditions',
        'rules' => 'getRules',
        'created_at' => 'getCreatedAt',
        'updated_at' => 'getUpdatedAt'
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

    public const TARGET_BRANCH = 'branch';
    public const TARGET_TAG = 'tag';
    public const TARGET_PUSH = 'push';
    public const SOURCE_TYPE_REPOSITORY = 'Repository';
    public const SOURCE_TYPE_ORGANIZATION = 'Organization';
    public const CURRENT_USER_CAN_BYPASS_ALWAYS = 'always';
    public const CURRENT_USER_CAN_BYPASS_PULL_REQUESTS_ONLY = 'pull_requests_only';
    public const CURRENT_USER_CAN_BYPASS_NEVER = 'never';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTargetAllowableValues()
    {
        return [
            self::TARGET_BRANCH,
            self::TARGET_TAG,
            self::TARGET_PUSH,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getSourceTypeAllowableValues()
    {
        return [
            self::SOURCE_TYPE_REPOSITORY,
            self::SOURCE_TYPE_ORGANIZATION,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getCurrentUserCanBypassAllowableValues()
    {
        return [
            self::CURRENT_USER_CAN_BYPASS_ALWAYS,
            self::CURRENT_USER_CAN_BYPASS_PULL_REQUESTS_ONLY,
            self::CURRENT_USER_CAN_BYPASS_NEVER,
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
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('target', $data ?? [], null);
        $this->setIfExists('source_type', $data ?? [], null);
        $this->setIfExists('source', $data ?? [], null);
        $this->setIfExists('enforcement', $data ?? [], null);
        $this->setIfExists('bypass_actors', $data ?? [], null);
        $this->setIfExists('current_user_can_bypass', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('_links', $data ?? [], null);
        $this->setIfExists('conditions', $data ?? [], null);
        $this->setIfExists('rules', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('updated_at', $data ?? [], null);
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

        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        $allowedValues = $this->getTargetAllowableValues();
        if (!is_null($this->container['target']) && !in_array($this->container['target'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'target', must be one of '%s'",
                $this->container['target'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getSourceTypeAllowableValues();
        if (!is_null($this->container['source_type']) && !in_array($this->container['source_type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'source_type', must be one of '%s'",
                $this->container['source_type'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['source'] === null) {
            $invalidProperties[] = "'source' can't be null";
        }
        if ($this->container['enforcement'] === null) {
            $invalidProperties[] = "'enforcement' can't be null";
        }
        $allowedValues = $this->getCurrentUserCanBypassAllowableValues();
        if (!is_null($this->container['current_user_can_bypass']) && !in_array($this->container['current_user_can_bypass'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'current_user_can_bypass', must be one of '%s'",
                $this->container['current_user_can_bypass'],
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
     * @param int $id The ID of the ruleset
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
     * @param string $name The name of the ruleset
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
     * Gets target
     *
     * @return string|null
     */
    public function getTarget()
    {
        return $this->container['target'];
    }

    /**
     * Sets target
     *
     * @param string|null $target The target of the ruleset  **Note**: The `push` target is in beta and is subject to change.
     *
     * @return self
     */
    public function setTarget($target)
    {
        if (is_null($target)) {
            throw new \InvalidArgumentException('non-nullable target cannot be null');
        }
        $allowedValues = $this->getTargetAllowableValues();
        if (!in_array($target, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'target', must be one of '%s'",
                    $target,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['target'] = $target;

        return $this;
    }

    /**
     * Gets source_type
     *
     * @return string|null
     */
    public function getSourceType()
    {
        return $this->container['source_type'];
    }

    /**
     * Sets source_type
     *
     * @param string|null $source_type The type of the source of the ruleset
     *
     * @return self
     */
    public function setSourceType($source_type)
    {
        if (is_null($source_type)) {
            throw new \InvalidArgumentException('non-nullable source_type cannot be null');
        }
        $allowedValues = $this->getSourceTypeAllowableValues();
        if (!in_array($source_type, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'source_type', must be one of '%s'",
                    $source_type,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['source_type'] = $source_type;

        return $this;
    }

    /**
     * Gets source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->container['source'];
    }

    /**
     * Sets source
     *
     * @param string $source The name of the source
     *
     * @return self
     */
    public function setSource($source)
    {
        if (is_null($source)) {
            throw new \InvalidArgumentException('non-nullable source cannot be null');
        }
        $this->container['source'] = $source;

        return $this;
    }

    /**
     * Gets enforcement
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRuleEnforcement
     */
    public function getEnforcement()
    {
        return $this->container['enforcement'];
    }

    /**
     * Sets enforcement
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRuleEnforcement $enforcement enforcement
     *
     * @return self
     */
    public function setEnforcement($enforcement)
    {
        if (is_null($enforcement)) {
            throw new \InvalidArgumentException('non-nullable enforcement cannot be null');
        }
        $this->container['enforcement'] = $enforcement;

        return $this;
    }

    /**
     * Gets bypass_actors
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetBypassActor[]|null
     */
    public function getBypassActors()
    {
        return $this->container['bypass_actors'];
    }

    /**
     * Sets bypass_actors
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetBypassActor[]|null $bypass_actors The actors that can bypass the rules in this ruleset
     *
     * @return self
     */
    public function setBypassActors($bypass_actors)
    {
        if (is_null($bypass_actors)) {
            throw new \InvalidArgumentException('non-nullable bypass_actors cannot be null');
        }
        $this->container['bypass_actors'] = $bypass_actors;

        return $this;
    }

    /**
     * Gets current_user_can_bypass
     *
     * @return string|null
     */
    public function getCurrentUserCanBypass()
    {
        return $this->container['current_user_can_bypass'];
    }

    /**
     * Sets current_user_can_bypass
     *
     * @param string|null $current_user_can_bypass The bypass type of the user making the API request for this ruleset. This field is only returned when querying the repository-level endpoint.
     *
     * @return self
     */
    public function setCurrentUserCanBypass($current_user_can_bypass)
    {
        if (is_null($current_user_can_bypass)) {
            throw new \InvalidArgumentException('non-nullable current_user_can_bypass cannot be null');
        }
        $allowedValues = $this->getCurrentUserCanBypassAllowableValues();
        if (!in_array($current_user_can_bypass, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'current_user_can_bypass', must be one of '%s'",
                    $current_user_can_bypass,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['current_user_can_bypass'] = $current_user_can_bypass;

        return $this;
    }

    /**
     * Gets node_id
     *
     * @return string|null
     */
    public function getNodeId()
    {
        return $this->container['node_id'];
    }

    /**
     * Sets node_id
     *
     * @param string|null $node_id node_id
     *
     * @return self
     */
    public function setNodeId($node_id)
    {
        if (is_null($node_id)) {
            throw new \InvalidArgumentException('non-nullable node_id cannot be null');
        }
        $this->container['node_id'] = $node_id;

        return $this;
    }

    /**
     * Gets _links
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetLinks|null
     */
    public function getLinks()
    {
        return $this->container['_links'];
    }

    /**
     * Sets _links
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetLinks|null $_links _links
     *
     * @return self
     */
    public function setLinks($_links)
    {
        if (is_null($_links)) {
            throw new \InvalidArgumentException('non-nullable _links cannot be null');
        }
        $this->container['_links'] = $_links;

        return $this;
    }

    /**
     * Gets conditions
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetConditions|null
     */
    public function getConditions()
    {
        return $this->container['conditions'];
    }

    /**
     * Sets conditions
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRulesetConditions|null $conditions conditions
     *
     * @return self
     */
    public function setConditions($conditions)
    {
        if (is_null($conditions)) {
            array_push($this->openAPINullablesSetToNull, 'conditions');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('conditions', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['conditions'] = $conditions;

        return $this;
    }

    /**
     * Gets rules
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRule[]|null
     */
    public function getRules()
    {
        return $this->container['rules'];
    }

    /**
     * Sets rules
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RepositoryRule[]|null $rules rules
     *
     * @return self
     */
    public function setRules($rules)
    {
        if (is_null($rules)) {
            throw new \InvalidArgumentException('non-nullable rules cannot be null');
        }
        $this->container['rules'] = $rules;

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
     * @param \DateTime|null $created_at created_at
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
     * @param \DateTime|null $updated_at updated_at
     *
     * @return self
     */
    public function setUpdatedAt($updated_at)
    {
        if (is_null($updated_at)) {
            throw new \InvalidArgumentException('non-nullable updated_at cannot be null');
        }
        $this->container['updated_at'] = $updated_at;

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


