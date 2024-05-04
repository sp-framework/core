<?php
/**
 * WebhookBranchProtectionRuleEditedChanges
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
 * WebhookBranchProtectionRuleEditedChanges Class Doc Comment
 *
 * @category Class
 * @description If the action was &#x60;edited&#x60;, the changes to the rule.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class WebhookBranchProtectionRuleEditedChanges implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'webhook_branch_protection_rule_edited_changes';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'admin_enforced' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced',
        'authorized_actor_names' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAuthorizedActorNames',
        'authorized_actors_only' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced',
        'authorized_dismissal_actors_only' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced',
        'linear_history_requirement_enforcement_level' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesLinearHistoryRequirementEnforcementLevel',
        'required_status_checks' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAuthorizedActorNames',
        'required_status_checks_enforcement_level' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesLinearHistoryRequirementEnforcementLevel'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'admin_enforced' => null,
        'authorized_actor_names' => null,
        'authorized_actors_only' => null,
        'authorized_dismissal_actors_only' => null,
        'linear_history_requirement_enforcement_level' => null,
        'required_status_checks' => null,
        'required_status_checks_enforcement_level' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'admin_enforced' => false,
        'authorized_actor_names' => false,
        'authorized_actors_only' => false,
        'authorized_dismissal_actors_only' => false,
        'linear_history_requirement_enforcement_level' => false,
        'required_status_checks' => false,
        'required_status_checks_enforcement_level' => false
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
        'admin_enforced' => 'admin_enforced',
        'authorized_actor_names' => 'authorized_actor_names',
        'authorized_actors_only' => 'authorized_actors_only',
        'authorized_dismissal_actors_only' => 'authorized_dismissal_actors_only',
        'linear_history_requirement_enforcement_level' => 'linear_history_requirement_enforcement_level',
        'required_status_checks' => 'required_status_checks',
        'required_status_checks_enforcement_level' => 'required_status_checks_enforcement_level'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'admin_enforced' => 'setAdminEnforced',
        'authorized_actor_names' => 'setAuthorizedActorNames',
        'authorized_actors_only' => 'setAuthorizedActorsOnly',
        'authorized_dismissal_actors_only' => 'setAuthorizedDismissalActorsOnly',
        'linear_history_requirement_enforcement_level' => 'setLinearHistoryRequirementEnforcementLevel',
        'required_status_checks' => 'setRequiredStatusChecks',
        'required_status_checks_enforcement_level' => 'setRequiredStatusChecksEnforcementLevel'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'admin_enforced' => 'getAdminEnforced',
        'authorized_actor_names' => 'getAuthorizedActorNames',
        'authorized_actors_only' => 'getAuthorizedActorsOnly',
        'authorized_dismissal_actors_only' => 'getAuthorizedDismissalActorsOnly',
        'linear_history_requirement_enforcement_level' => 'getLinearHistoryRequirementEnforcementLevel',
        'required_status_checks' => 'getRequiredStatusChecks',
        'required_status_checks_enforcement_level' => 'getRequiredStatusChecksEnforcementLevel'
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
        $this->setIfExists('admin_enforced', $data ?? [], null);
        $this->setIfExists('authorized_actor_names', $data ?? [], null);
        $this->setIfExists('authorized_actors_only', $data ?? [], null);
        $this->setIfExists('authorized_dismissal_actors_only', $data ?? [], null);
        $this->setIfExists('linear_history_requirement_enforcement_level', $data ?? [], null);
        $this->setIfExists('required_status_checks', $data ?? [], null);
        $this->setIfExists('required_status_checks_enforcement_level', $data ?? [], null);
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
     * Gets admin_enforced
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced|null
     */
    public function getAdminEnforced()
    {
        return $this->container['admin_enforced'];
    }

    /**
     * Sets admin_enforced
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced|null $admin_enforced admin_enforced
     *
     * @return self
     */
    public function setAdminEnforced($admin_enforced)
    {
        if (is_null($admin_enforced)) {
            throw new \InvalidArgumentException('non-nullable admin_enforced cannot be null');
        }
        $this->container['admin_enforced'] = $admin_enforced;

        return $this;
    }

    /**
     * Gets authorized_actor_names
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAuthorizedActorNames|null
     */
    public function getAuthorizedActorNames()
    {
        return $this->container['authorized_actor_names'];
    }

    /**
     * Sets authorized_actor_names
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAuthorizedActorNames|null $authorized_actor_names authorized_actor_names
     *
     * @return self
     */
    public function setAuthorizedActorNames($authorized_actor_names)
    {
        if (is_null($authorized_actor_names)) {
            throw new \InvalidArgumentException('non-nullable authorized_actor_names cannot be null');
        }
        $this->container['authorized_actor_names'] = $authorized_actor_names;

        return $this;
    }

    /**
     * Gets authorized_actors_only
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced|null
     */
    public function getAuthorizedActorsOnly()
    {
        return $this->container['authorized_actors_only'];
    }

    /**
     * Sets authorized_actors_only
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced|null $authorized_actors_only authorized_actors_only
     *
     * @return self
     */
    public function setAuthorizedActorsOnly($authorized_actors_only)
    {
        if (is_null($authorized_actors_only)) {
            throw new \InvalidArgumentException('non-nullable authorized_actors_only cannot be null');
        }
        $this->container['authorized_actors_only'] = $authorized_actors_only;

        return $this;
    }

    /**
     * Gets authorized_dismissal_actors_only
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced|null
     */
    public function getAuthorizedDismissalActorsOnly()
    {
        return $this->container['authorized_dismissal_actors_only'];
    }

    /**
     * Sets authorized_dismissal_actors_only
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAdminEnforced|null $authorized_dismissal_actors_only authorized_dismissal_actors_only
     *
     * @return self
     */
    public function setAuthorizedDismissalActorsOnly($authorized_dismissal_actors_only)
    {
        if (is_null($authorized_dismissal_actors_only)) {
            throw new \InvalidArgumentException('non-nullable authorized_dismissal_actors_only cannot be null');
        }
        $this->container['authorized_dismissal_actors_only'] = $authorized_dismissal_actors_only;

        return $this;
    }

    /**
     * Gets linear_history_requirement_enforcement_level
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesLinearHistoryRequirementEnforcementLevel|null
     */
    public function getLinearHistoryRequirementEnforcementLevel()
    {
        return $this->container['linear_history_requirement_enforcement_level'];
    }

    /**
     * Sets linear_history_requirement_enforcement_level
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesLinearHistoryRequirementEnforcementLevel|null $linear_history_requirement_enforcement_level linear_history_requirement_enforcement_level
     *
     * @return self
     */
    public function setLinearHistoryRequirementEnforcementLevel($linear_history_requirement_enforcement_level)
    {
        if (is_null($linear_history_requirement_enforcement_level)) {
            throw new \InvalidArgumentException('non-nullable linear_history_requirement_enforcement_level cannot be null');
        }
        $this->container['linear_history_requirement_enforcement_level'] = $linear_history_requirement_enforcement_level;

        return $this;
    }

    /**
     * Gets required_status_checks
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAuthorizedActorNames|null
     */
    public function getRequiredStatusChecks()
    {
        return $this->container['required_status_checks'];
    }

    /**
     * Sets required_status_checks
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesAuthorizedActorNames|null $required_status_checks required_status_checks
     *
     * @return self
     */
    public function setRequiredStatusChecks($required_status_checks)
    {
        if (is_null($required_status_checks)) {
            throw new \InvalidArgumentException('non-nullable required_status_checks cannot be null');
        }
        $this->container['required_status_checks'] = $required_status_checks;

        return $this;
    }

    /**
     * Gets required_status_checks_enforcement_level
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesLinearHistoryRequirementEnforcementLevel|null
     */
    public function getRequiredStatusChecksEnforcementLevel()
    {
        return $this->container['required_status_checks_enforcement_level'];
    }

    /**
     * Sets required_status_checks_enforcement_level
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookBranchProtectionRuleEditedChangesLinearHistoryRequirementEnforcementLevel|null $required_status_checks_enforcement_level required_status_checks_enforcement_level
     *
     * @return self
     */
    public function setRequiredStatusChecksEnforcementLevel($required_status_checks_enforcement_level)
    {
        if (is_null($required_status_checks_enforcement_level)) {
            throw new \InvalidArgumentException('non-nullable required_status_checks_enforcement_level cannot be null');
        }
        $this->container['required_status_checks_enforcement_level'] = $required_status_checks_enforcement_level;

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


