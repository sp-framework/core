<?php
/**
 * ProtectedBranch
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
 * ProtectedBranch Class Doc Comment
 *
 * @category Class
 * @description Branch protections protect branches
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class ProtectedBranch implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'protected-branch';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'url' => 'string',
        'required_status_checks' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\StatusCheckPolicy',
        'required_pull_request_reviews' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredPullRequestReviews',
        'required_signatures' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BranchProtectionRequiredSignatures',
        'enforce_admins' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchEnforceAdmins',
        'required_linear_history' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory',
        'allow_force_pushes' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory',
        'allow_deletions' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory',
        'restrictions' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BranchRestrictionPolicy',
        'required_conversation_resolution' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredConversationResolution',
        'block_creations' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory',
        'lock_branch' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchLockBranch',
        'allow_fork_syncing' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchAllowForkSyncing'
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
        'required_status_checks' => null,
        'required_pull_request_reviews' => null,
        'required_signatures' => null,
        'enforce_admins' => null,
        'required_linear_history' => null,
        'allow_force_pushes' => null,
        'allow_deletions' => null,
        'restrictions' => null,
        'required_conversation_resolution' => null,
        'block_creations' => null,
        'lock_branch' => null,
        'allow_fork_syncing' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'url' => false,
        'required_status_checks' => false,
        'required_pull_request_reviews' => false,
        'required_signatures' => false,
        'enforce_admins' => false,
        'required_linear_history' => false,
        'allow_force_pushes' => false,
        'allow_deletions' => false,
        'restrictions' => false,
        'required_conversation_resolution' => false,
        'block_creations' => false,
        'lock_branch' => false,
        'allow_fork_syncing' => false
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
        'required_status_checks' => 'required_status_checks',
        'required_pull_request_reviews' => 'required_pull_request_reviews',
        'required_signatures' => 'required_signatures',
        'enforce_admins' => 'enforce_admins',
        'required_linear_history' => 'required_linear_history',
        'allow_force_pushes' => 'allow_force_pushes',
        'allow_deletions' => 'allow_deletions',
        'restrictions' => 'restrictions',
        'required_conversation_resolution' => 'required_conversation_resolution',
        'block_creations' => 'block_creations',
        'lock_branch' => 'lock_branch',
        'allow_fork_syncing' => 'allow_fork_syncing'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'url' => 'setUrl',
        'required_status_checks' => 'setRequiredStatusChecks',
        'required_pull_request_reviews' => 'setRequiredPullRequestReviews',
        'required_signatures' => 'setRequiredSignatures',
        'enforce_admins' => 'setEnforceAdmins',
        'required_linear_history' => 'setRequiredLinearHistory',
        'allow_force_pushes' => 'setAllowForcePushes',
        'allow_deletions' => 'setAllowDeletions',
        'restrictions' => 'setRestrictions',
        'required_conversation_resolution' => 'setRequiredConversationResolution',
        'block_creations' => 'setBlockCreations',
        'lock_branch' => 'setLockBranch',
        'allow_fork_syncing' => 'setAllowForkSyncing'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'url' => 'getUrl',
        'required_status_checks' => 'getRequiredStatusChecks',
        'required_pull_request_reviews' => 'getRequiredPullRequestReviews',
        'required_signatures' => 'getRequiredSignatures',
        'enforce_admins' => 'getEnforceAdmins',
        'required_linear_history' => 'getRequiredLinearHistory',
        'allow_force_pushes' => 'getAllowForcePushes',
        'allow_deletions' => 'getAllowDeletions',
        'restrictions' => 'getRestrictions',
        'required_conversation_resolution' => 'getRequiredConversationResolution',
        'block_creations' => 'getBlockCreations',
        'lock_branch' => 'getLockBranch',
        'allow_fork_syncing' => 'getAllowForkSyncing'
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
        $this->setIfExists('required_status_checks', $data ?? [], null);
        $this->setIfExists('required_pull_request_reviews', $data ?? [], null);
        $this->setIfExists('required_signatures', $data ?? [], null);
        $this->setIfExists('enforce_admins', $data ?? [], null);
        $this->setIfExists('required_linear_history', $data ?? [], null);
        $this->setIfExists('allow_force_pushes', $data ?? [], null);
        $this->setIfExists('allow_deletions', $data ?? [], null);
        $this->setIfExists('restrictions', $data ?? [], null);
        $this->setIfExists('required_conversation_resolution', $data ?? [], null);
        $this->setIfExists('block_creations', $data ?? [], null);
        $this->setIfExists('lock_branch', $data ?? [], null);
        $this->setIfExists('allow_fork_syncing', $data ?? [], null);
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
     * Gets required_status_checks
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\StatusCheckPolicy|null
     */
    public function getRequiredStatusChecks()
    {
        return $this->container['required_status_checks'];
    }

    /**
     * Sets required_status_checks
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\StatusCheckPolicy|null $required_status_checks required_status_checks
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
     * Gets required_pull_request_reviews
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredPullRequestReviews|null
     */
    public function getRequiredPullRequestReviews()
    {
        return $this->container['required_pull_request_reviews'];
    }

    /**
     * Sets required_pull_request_reviews
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredPullRequestReviews|null $required_pull_request_reviews required_pull_request_reviews
     *
     * @return self
     */
    public function setRequiredPullRequestReviews($required_pull_request_reviews)
    {
        if (is_null($required_pull_request_reviews)) {
            throw new \InvalidArgumentException('non-nullable required_pull_request_reviews cannot be null');
        }
        $this->container['required_pull_request_reviews'] = $required_pull_request_reviews;

        return $this;
    }

    /**
     * Gets required_signatures
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BranchProtectionRequiredSignatures|null
     */
    public function getRequiredSignatures()
    {
        return $this->container['required_signatures'];
    }

    /**
     * Sets required_signatures
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BranchProtectionRequiredSignatures|null $required_signatures required_signatures
     *
     * @return self
     */
    public function setRequiredSignatures($required_signatures)
    {
        if (is_null($required_signatures)) {
            throw new \InvalidArgumentException('non-nullable required_signatures cannot be null');
        }
        $this->container['required_signatures'] = $required_signatures;

        return $this;
    }

    /**
     * Gets enforce_admins
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchEnforceAdmins|null
     */
    public function getEnforceAdmins()
    {
        return $this->container['enforce_admins'];
    }

    /**
     * Sets enforce_admins
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchEnforceAdmins|null $enforce_admins enforce_admins
     *
     * @return self
     */
    public function setEnforceAdmins($enforce_admins)
    {
        if (is_null($enforce_admins)) {
            throw new \InvalidArgumentException('non-nullable enforce_admins cannot be null');
        }
        $this->container['enforce_admins'] = $enforce_admins;

        return $this;
    }

    /**
     * Gets required_linear_history
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory|null
     */
    public function getRequiredLinearHistory()
    {
        return $this->container['required_linear_history'];
    }

    /**
     * Sets required_linear_history
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory|null $required_linear_history required_linear_history
     *
     * @return self
     */
    public function setRequiredLinearHistory($required_linear_history)
    {
        if (is_null($required_linear_history)) {
            throw new \InvalidArgumentException('non-nullable required_linear_history cannot be null');
        }
        $this->container['required_linear_history'] = $required_linear_history;

        return $this;
    }

    /**
     * Gets allow_force_pushes
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory|null
     */
    public function getAllowForcePushes()
    {
        return $this->container['allow_force_pushes'];
    }

    /**
     * Sets allow_force_pushes
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory|null $allow_force_pushes allow_force_pushes
     *
     * @return self
     */
    public function setAllowForcePushes($allow_force_pushes)
    {
        if (is_null($allow_force_pushes)) {
            throw new \InvalidArgumentException('non-nullable allow_force_pushes cannot be null');
        }
        $this->container['allow_force_pushes'] = $allow_force_pushes;

        return $this;
    }

    /**
     * Gets allow_deletions
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory|null
     */
    public function getAllowDeletions()
    {
        return $this->container['allow_deletions'];
    }

    /**
     * Sets allow_deletions
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory|null $allow_deletions allow_deletions
     *
     * @return self
     */
    public function setAllowDeletions($allow_deletions)
    {
        if (is_null($allow_deletions)) {
            throw new \InvalidArgumentException('non-nullable allow_deletions cannot be null');
        }
        $this->container['allow_deletions'] = $allow_deletions;

        return $this;
    }

    /**
     * Gets restrictions
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BranchRestrictionPolicy|null
     */
    public function getRestrictions()
    {
        return $this->container['restrictions'];
    }

    /**
     * Sets restrictions
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BranchRestrictionPolicy|null $restrictions restrictions
     *
     * @return self
     */
    public function setRestrictions($restrictions)
    {
        if (is_null($restrictions)) {
            throw new \InvalidArgumentException('non-nullable restrictions cannot be null');
        }
        $this->container['restrictions'] = $restrictions;

        return $this;
    }

    /**
     * Gets required_conversation_resolution
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredConversationResolution|null
     */
    public function getRequiredConversationResolution()
    {
        return $this->container['required_conversation_resolution'];
    }

    /**
     * Sets required_conversation_resolution
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredConversationResolution|null $required_conversation_resolution required_conversation_resolution
     *
     * @return self
     */
    public function setRequiredConversationResolution($required_conversation_resolution)
    {
        if (is_null($required_conversation_resolution)) {
            throw new \InvalidArgumentException('non-nullable required_conversation_resolution cannot be null');
        }
        $this->container['required_conversation_resolution'] = $required_conversation_resolution;

        return $this;
    }

    /**
     * Gets block_creations
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory|null
     */
    public function getBlockCreations()
    {
        return $this->container['block_creations'];
    }

    /**
     * Sets block_creations
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchRequiredLinearHistory|null $block_creations block_creations
     *
     * @return self
     */
    public function setBlockCreations($block_creations)
    {
        if (is_null($block_creations)) {
            throw new \InvalidArgumentException('non-nullable block_creations cannot be null');
        }
        $this->container['block_creations'] = $block_creations;

        return $this;
    }

    /**
     * Gets lock_branch
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchLockBranch|null
     */
    public function getLockBranch()
    {
        return $this->container['lock_branch'];
    }

    /**
     * Sets lock_branch
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchLockBranch|null $lock_branch lock_branch
     *
     * @return self
     */
    public function setLockBranch($lock_branch)
    {
        if (is_null($lock_branch)) {
            throw new \InvalidArgumentException('non-nullable lock_branch cannot be null');
        }
        $this->container['lock_branch'] = $lock_branch;

        return $this;
    }

    /**
     * Gets allow_fork_syncing
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchAllowForkSyncing|null
     */
    public function getAllowForkSyncing()
    {
        return $this->container['allow_fork_syncing'];
    }

    /**
     * Sets allow_fork_syncing
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ProtectedBranchAllowForkSyncing|null $allow_fork_syncing allow_fork_syncing
     *
     * @return self
     */
    public function setAllowForkSyncing($allow_fork_syncing)
    {
        if (is_null($allow_fork_syncing)) {
            throw new \InvalidArgumentException('non-nullable allow_fork_syncing cannot be null');
        }
        $this->container['allow_fork_syncing'] = $allow_fork_syncing;

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


