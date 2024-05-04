<?php
/**
 * EnterpriseAdminCreateSelfHostedRunnerGroupForEnterpriseRequest
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
 * EnterpriseAdminCreateSelfHostedRunnerGroupForEnterpriseRequest Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class EnterpriseAdminCreateSelfHostedRunnerGroupForEnterpriseRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'enterprise_admin_create_self_hosted_runner_group_for_enterprise_request';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'name' => 'string',
        'visibility' => 'string',
        'selected_organization_ids' => 'int[]',
        'runners' => 'int[]',
        'allows_public_repositories' => 'bool',
        'restricted_to_workflows' => 'bool',
        'selected_workflows' => 'string[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'name' => null,
        'visibility' => null,
        'selected_organization_ids' => null,
        'runners' => null,
        'allows_public_repositories' => null,
        'restricted_to_workflows' => null,
        'selected_workflows' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'name' => false,
        'visibility' => false,
        'selected_organization_ids' => false,
        'runners' => false,
        'allows_public_repositories' => false,
        'restricted_to_workflows' => false,
        'selected_workflows' => false
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
        'name' => 'name',
        'visibility' => 'visibility',
        'selected_organization_ids' => 'selected_organization_ids',
        'runners' => 'runners',
        'allows_public_repositories' => 'allows_public_repositories',
        'restricted_to_workflows' => 'restricted_to_workflows',
        'selected_workflows' => 'selected_workflows'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'name' => 'setName',
        'visibility' => 'setVisibility',
        'selected_organization_ids' => 'setSelectedOrganizationIds',
        'runners' => 'setRunners',
        'allows_public_repositories' => 'setAllowsPublicRepositories',
        'restricted_to_workflows' => 'setRestrictedToWorkflows',
        'selected_workflows' => 'setSelectedWorkflows'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'name' => 'getName',
        'visibility' => 'getVisibility',
        'selected_organization_ids' => 'getSelectedOrganizationIds',
        'runners' => 'getRunners',
        'allows_public_repositories' => 'getAllowsPublicRepositories',
        'restricted_to_workflows' => 'getRestrictedToWorkflows',
        'selected_workflows' => 'getSelectedWorkflows'
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

    public const VISIBILITY_SELECTED = 'selected';
    public const VISIBILITY_ALL = 'all';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getVisibilityAllowableValues()
    {
        return [
            self::VISIBILITY_SELECTED,
            self::VISIBILITY_ALL,
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
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('visibility', $data ?? [], null);
        $this->setIfExists('selected_organization_ids', $data ?? [], null);
        $this->setIfExists('runners', $data ?? [], null);
        $this->setIfExists('allows_public_repositories', $data ?? [], false);
        $this->setIfExists('restricted_to_workflows', $data ?? [], false);
        $this->setIfExists('selected_workflows', $data ?? [], null);
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

        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        $allowedValues = $this->getVisibilityAllowableValues();
        if (!is_null($this->container['visibility']) && !in_array($this->container['visibility'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'visibility', must be one of '%s'",
                $this->container['visibility'],
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
     * @param string $name Name of the runner group.
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
     * Gets visibility
     *
     * @return string|null
     */
    public function getVisibility()
    {
        return $this->container['visibility'];
    }

    /**
     * Sets visibility
     *
     * @param string|null $visibility Visibility of a runner group. You can select all organizations or select individual organization.
     *
     * @return self
     */
    public function setVisibility($visibility)
    {
        if (is_null($visibility)) {
            throw new \InvalidArgumentException('non-nullable visibility cannot be null');
        }
        $allowedValues = $this->getVisibilityAllowableValues();
        if (!in_array($visibility, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'visibility', must be one of '%s'",
                    $visibility,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['visibility'] = $visibility;

        return $this;
    }

    /**
     * Gets selected_organization_ids
     *
     * @return int[]|null
     */
    public function getSelectedOrganizationIds()
    {
        return $this->container['selected_organization_ids'];
    }

    /**
     * Sets selected_organization_ids
     *
     * @param int[]|null $selected_organization_ids List of organization IDs that can access the runner group.
     *
     * @return self
     */
    public function setSelectedOrganizationIds($selected_organization_ids)
    {
        if (is_null($selected_organization_ids)) {
            throw new \InvalidArgumentException('non-nullable selected_organization_ids cannot be null');
        }
        $this->container['selected_organization_ids'] = $selected_organization_ids;

        return $this;
    }

    /**
     * Gets runners
     *
     * @return int[]|null
     */
    public function getRunners()
    {
        return $this->container['runners'];
    }

    /**
     * Sets runners
     *
     * @param int[]|null $runners List of runner IDs to add to the runner group.
     *
     * @return self
     */
    public function setRunners($runners)
    {
        if (is_null($runners)) {
            throw new \InvalidArgumentException('non-nullable runners cannot be null');
        }
        $this->container['runners'] = $runners;

        return $this;
    }

    /**
     * Gets allows_public_repositories
     *
     * @return bool|null
     */
    public function getAllowsPublicRepositories()
    {
        return $this->container['allows_public_repositories'];
    }

    /**
     * Sets allows_public_repositories
     *
     * @param bool|null $allows_public_repositories Whether the runner group can be used by `public` repositories.
     *
     * @return self
     */
    public function setAllowsPublicRepositories($allows_public_repositories)
    {
        if (is_null($allows_public_repositories)) {
            throw new \InvalidArgumentException('non-nullable allows_public_repositories cannot be null');
        }
        $this->container['allows_public_repositories'] = $allows_public_repositories;

        return $this;
    }

    /**
     * Gets restricted_to_workflows
     *
     * @return bool|null
     */
    public function getRestrictedToWorkflows()
    {
        return $this->container['restricted_to_workflows'];
    }

    /**
     * Sets restricted_to_workflows
     *
     * @param bool|null $restricted_to_workflows If `true`, the runner group will be restricted to running only the workflows specified in the `selected_workflows` array.
     *
     * @return self
     */
    public function setRestrictedToWorkflows($restricted_to_workflows)
    {
        if (is_null($restricted_to_workflows)) {
            throw new \InvalidArgumentException('non-nullable restricted_to_workflows cannot be null');
        }
        $this->container['restricted_to_workflows'] = $restricted_to_workflows;

        return $this;
    }

    /**
     * Gets selected_workflows
     *
     * @return string[]|null
     */
    public function getSelectedWorkflows()
    {
        return $this->container['selected_workflows'];
    }

    /**
     * Sets selected_workflows
     *
     * @param string[]|null $selected_workflows List of workflows the runner group should be allowed to run. This setting will be ignored unless `restricted_to_workflows` is set to `true`.
     *
     * @return self
     */
    public function setSelectedWorkflows($selected_workflows)
    {
        if (is_null($selected_workflows)) {
            throw new \InvalidArgumentException('non-nullable selected_workflows cannot be null');
        }
        $this->container['selected_workflows'] = $selected_workflows;

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


