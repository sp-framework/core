<?php
/**
 * ReposUpdateBranchProtectionRequest
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
 * ReposUpdateBranchProtectionRequest Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class ReposUpdateBranchProtectionRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'repos_update_branch_protection_request';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'required_status_checks' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRequiredStatusChecks',
        'enforce_admins' => 'bool',
        'required_pull_request_reviews' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRequiredPullRequestReviews',
        'restrictions' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRestrictions',
        'required_linear_history' => 'bool',
        'allow_force_pushes' => 'bool',
        'allow_deletions' => 'bool',
        'block_creations' => 'bool',
        'required_conversation_resolution' => 'bool',
        'lock_branch' => 'bool',
        'allow_fork_syncing' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'required_status_checks' => null,
        'enforce_admins' => null,
        'required_pull_request_reviews' => null,
        'restrictions' => null,
        'required_linear_history' => null,
        'allow_force_pushes' => null,
        'allow_deletions' => null,
        'block_creations' => null,
        'required_conversation_resolution' => null,
        'lock_branch' => null,
        'allow_fork_syncing' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'required_status_checks' => true,
        'enforce_admins' => true,
        'required_pull_request_reviews' => true,
        'restrictions' => true,
        'required_linear_history' => false,
        'allow_force_pushes' => true,
        'allow_deletions' => false,
        'block_creations' => false,
        'required_conversation_resolution' => false,
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
        'required_status_checks' => 'required_status_checks',
        'enforce_admins' => 'enforce_admins',
        'required_pull_request_reviews' => 'required_pull_request_reviews',
        'restrictions' => 'restrictions',
        'required_linear_history' => 'required_linear_history',
        'allow_force_pushes' => 'allow_force_pushes',
        'allow_deletions' => 'allow_deletions',
        'block_creations' => 'block_creations',
        'required_conversation_resolution' => 'required_conversation_resolution',
        'lock_branch' => 'lock_branch',
        'allow_fork_syncing' => 'allow_fork_syncing'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'required_status_checks' => 'setRequiredStatusChecks',
        'enforce_admins' => 'setEnforceAdmins',
        'required_pull_request_reviews' => 'setRequiredPullRequestReviews',
        'restrictions' => 'setRestrictions',
        'required_linear_history' => 'setRequiredLinearHistory',
        'allow_force_pushes' => 'setAllowForcePushes',
        'allow_deletions' => 'setAllowDeletions',
        'block_creations' => 'setBlockCreations',
        'required_conversation_resolution' => 'setRequiredConversationResolution',
        'lock_branch' => 'setLockBranch',
        'allow_fork_syncing' => 'setAllowForkSyncing'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'required_status_checks' => 'getRequiredStatusChecks',
        'enforce_admins' => 'getEnforceAdmins',
        'required_pull_request_reviews' => 'getRequiredPullRequestReviews',
        'restrictions' => 'getRestrictions',
        'required_linear_history' => 'getRequiredLinearHistory',
        'allow_force_pushes' => 'getAllowForcePushes',
        'allow_deletions' => 'getAllowDeletions',
        'block_creations' => 'getBlockCreations',
        'required_conversation_resolution' => 'getRequiredConversationResolution',
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
        $this->setIfExists('required_status_checks', $data ?? [], null);
        $this->setIfExists('enforce_admins', $data ?? [], null);
        $this->setIfExists('required_pull_request_reviews', $data ?? [], null);
        $this->setIfExists('restrictions', $data ?? [], null);
        $this->setIfExists('required_linear_history', $data ?? [], null);
        $this->setIfExists('allow_force_pushes', $data ?? [], null);
        $this->setIfExists('allow_deletions', $data ?? [], null);
        $this->setIfExists('block_creations', $data ?? [], null);
        $this->setIfExists('required_conversation_resolution', $data ?? [], null);
        $this->setIfExists('lock_branch', $data ?? [], false);
        $this->setIfExists('allow_fork_syncing', $data ?? [], false);
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

        if ($this->container['required_status_checks'] === null) {
            $invalidProperties[] = "'required_status_checks' can't be null";
        }
        if ($this->container['enforce_admins'] === null) {
            $invalidProperties[] = "'enforce_admins' can't be null";
        }
        if ($this->container['required_pull_request_reviews'] === null) {
            $invalidProperties[] = "'required_pull_request_reviews' can't be null";
        }
        if ($this->container['restrictions'] === null) {
            $invalidProperties[] = "'restrictions' can't be null";
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
     * Gets required_status_checks
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRequiredStatusChecks
     */
    public function getRequiredStatusChecks()
    {
        return $this->container['required_status_checks'];
    }

    /**
     * Sets required_status_checks
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRequiredStatusChecks $required_status_checks required_status_checks
     *
     * @return self
     */
    public function setRequiredStatusChecks($required_status_checks)
    {
        if (is_null($required_status_checks)) {
            array_push($this->openAPINullablesSetToNull, 'required_status_checks');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('required_status_checks', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['required_status_checks'] = $required_status_checks;

        return $this;
    }

    /**
     * Gets enforce_admins
     *
     * @return bool
     */
    public function getEnforceAdmins()
    {
        return $this->container['enforce_admins'];
    }

    /**
     * Sets enforce_admins
     *
     * @param bool $enforce_admins Enforce all configured restrictions for administrators. Set to `true` to enforce required status checks for repository administrators. Set to `null` to disable.
     *
     * @return self
     */
    public function setEnforceAdmins($enforce_admins)
    {
        if (is_null($enforce_admins)) {
            array_push($this->openAPINullablesSetToNull, 'enforce_admins');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('enforce_admins', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['enforce_admins'] = $enforce_admins;

        return $this;
    }

    /**
     * Gets required_pull_request_reviews
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRequiredPullRequestReviews
     */
    public function getRequiredPullRequestReviews()
    {
        return $this->container['required_pull_request_reviews'];
    }

    /**
     * Sets required_pull_request_reviews
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRequiredPullRequestReviews $required_pull_request_reviews required_pull_request_reviews
     *
     * @return self
     */
    public function setRequiredPullRequestReviews($required_pull_request_reviews)
    {
        if (is_null($required_pull_request_reviews)) {
            array_push($this->openAPINullablesSetToNull, 'required_pull_request_reviews');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('required_pull_request_reviews', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['required_pull_request_reviews'] = $required_pull_request_reviews;

        return $this;
    }

    /**
     * Gets restrictions
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRestrictions
     */
    public function getRestrictions()
    {
        return $this->container['restrictions'];
    }

    /**
     * Sets restrictions
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReposUpdateBranchProtectionRequestRestrictions $restrictions restrictions
     *
     * @return self
     */
    public function setRestrictions($restrictions)
    {
        if (is_null($restrictions)) {
            array_push($this->openAPINullablesSetToNull, 'restrictions');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('restrictions', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['restrictions'] = $restrictions;

        return $this;
    }

    /**
     * Gets required_linear_history
     *
     * @return bool|null
     */
    public function getRequiredLinearHistory()
    {
        return $this->container['required_linear_history'];
    }

    /**
     * Sets required_linear_history
     *
     * @param bool|null $required_linear_history Enforces a linear commit Git history, which prevents anyone from pushing merge commits to a branch. Set to `true` to enforce a linear commit history. Set to `false` to disable a linear commit Git history. Your repository must allow squash merging or rebase merging before you can enable a linear commit history. Default: `false`. For more information, see \"[Requiring a linear commit history](https://docs.github.com/enterprise-server@3.12/github/administering-a-repository/requiring-a-linear-commit-history)\" in the GitHub Help documentation.
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
     * @return bool|null
     */
    public function getAllowForcePushes()
    {
        return $this->container['allow_force_pushes'];
    }

    /**
     * Sets allow_force_pushes
     *
     * @param bool|null $allow_force_pushes Permits force pushes to the protected branch by anyone with write access to the repository. Set to `true` to allow force pushes. Set to `false` or `null` to block force pushes. Default: `false`. For more information, see \"[Enabling force pushes to a protected branch](https://docs.github.com/enterprise-server@3.12/github/administering-a-repository/enabling-force-pushes-to-a-protected-branch)\" in the GitHub Help documentation.\"
     *
     * @return self
     */
    public function setAllowForcePushes($allow_force_pushes)
    {
        if (is_null($allow_force_pushes)) {
            array_push($this->openAPINullablesSetToNull, 'allow_force_pushes');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('allow_force_pushes', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['allow_force_pushes'] = $allow_force_pushes;

        return $this;
    }

    /**
     * Gets allow_deletions
     *
     * @return bool|null
     */
    public function getAllowDeletions()
    {
        return $this->container['allow_deletions'];
    }

    /**
     * Sets allow_deletions
     *
     * @param bool|null $allow_deletions Allows deletion of the protected branch by anyone with write access to the repository. Set to `false` to prevent deletion of the protected branch. Default: `false`. For more information, see \"[Enabling force pushes to a protected branch](https://docs.github.com/enterprise-server@3.12/github/administering-a-repository/enabling-force-pushes-to-a-protected-branch)\" in the GitHub Help documentation.
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
     * Gets block_creations
     *
     * @return bool|null
     */
    public function getBlockCreations()
    {
        return $this->container['block_creations'];
    }

    /**
     * Sets block_creations
     *
     * @param bool|null $block_creations If set to `true`, the `restrictions` branch protection settings which limits who can push will also block pushes which create new branches, unless the push is initiated by a user, team, or app which has the ability to push. Set to `true` to restrict new branch creation. Default: `false`.
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
     * Gets required_conversation_resolution
     *
     * @return bool|null
     */
    public function getRequiredConversationResolution()
    {
        return $this->container['required_conversation_resolution'];
    }

    /**
     * Sets required_conversation_resolution
     *
     * @param bool|null $required_conversation_resolution Requires all conversations on code to be resolved before a pull request can be merged into a branch that matches this rule. Set to `false` to disable. Default: `false`.
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
     * Gets lock_branch
     *
     * @return bool|null
     */
    public function getLockBranch()
    {
        return $this->container['lock_branch'];
    }

    /**
     * Sets lock_branch
     *
     * @param bool|null $lock_branch Whether to set the branch as read-only. If this is true, users will not be able to push to the branch. Default: `false`.
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
     * @return bool|null
     */
    public function getAllowForkSyncing()
    {
        return $this->container['allow_fork_syncing'];
    }

    /**
     * Sets allow_fork_syncing
     *
     * @param bool|null $allow_fork_syncing Whether users can pull changes from upstream when the branch is locked. Set to `true` to allow fork syncing. Set to `false` to prevent fork syncing. Default: `false`.
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


