<?php
/**
 * BranchProtection
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
 * BranchProtection Class Doc Comment
 *
 * @category Class
 * @description BranchProtection represents a branch protection for a repository
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class BranchProtection implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'BranchProtection';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'approvals_whitelist_teams' => 'string[]',
        'approvals_whitelist_username' => 'string[]',
        'block_on_official_review_requests' => 'bool',
        'block_on_outdated_branch' => 'bool',
        'block_on_rejected_reviews' => 'bool',
        'branch_name' => 'string',
        'created_at' => '\DateTime',
        'dismiss_stale_approvals' => 'bool',
        'enable_approvals_whitelist' => 'bool',
        'enable_merge_whitelist' => 'bool',
        'enable_push' => 'bool',
        'enable_push_whitelist' => 'bool',
        'enable_status_check' => 'bool',
        'merge_whitelist_teams' => 'string[]',
        'merge_whitelist_usernames' => 'string[]',
        'protected_file_patterns' => 'string',
        'push_whitelist_deploy_keys' => 'bool',
        'push_whitelist_teams' => 'string[]',
        'push_whitelist_usernames' => 'string[]',
        'require_signed_commits' => 'bool',
        'required_approvals' => 'int',
        'rule_name' => 'string',
        'status_check_contexts' => 'string[]',
        'unprotected_file_patterns' => 'string',
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
        'approvals_whitelist_teams' => null,
        'approvals_whitelist_username' => null,
        'block_on_official_review_requests' => null,
        'block_on_outdated_branch' => null,
        'block_on_rejected_reviews' => null,
        'branch_name' => null,
        'created_at' => 'date-time',
        'dismiss_stale_approvals' => null,
        'enable_approvals_whitelist' => null,
        'enable_merge_whitelist' => null,
        'enable_push' => null,
        'enable_push_whitelist' => null,
        'enable_status_check' => null,
        'merge_whitelist_teams' => null,
        'merge_whitelist_usernames' => null,
        'protected_file_patterns' => null,
        'push_whitelist_deploy_keys' => null,
        'push_whitelist_teams' => null,
        'push_whitelist_usernames' => null,
        'require_signed_commits' => null,
        'required_approvals' => 'int64',
        'rule_name' => null,
        'status_check_contexts' => null,
        'unprotected_file_patterns' => null,
        'updated_at' => 'date-time'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'approvals_whitelist_teams' => false,
        'approvals_whitelist_username' => false,
        'block_on_official_review_requests' => false,
        'block_on_outdated_branch' => false,
        'block_on_rejected_reviews' => false,
        'branch_name' => false,
        'created_at' => false,
        'dismiss_stale_approvals' => false,
        'enable_approvals_whitelist' => false,
        'enable_merge_whitelist' => false,
        'enable_push' => false,
        'enable_push_whitelist' => false,
        'enable_status_check' => false,
        'merge_whitelist_teams' => false,
        'merge_whitelist_usernames' => false,
        'protected_file_patterns' => false,
        'push_whitelist_deploy_keys' => false,
        'push_whitelist_teams' => false,
        'push_whitelist_usernames' => false,
        'require_signed_commits' => false,
        'required_approvals' => false,
        'rule_name' => false,
        'status_check_contexts' => false,
        'unprotected_file_patterns' => false,
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
        'approvals_whitelist_teams' => 'approvals_whitelist_teams',
        'approvals_whitelist_username' => 'approvals_whitelist_username',
        'block_on_official_review_requests' => 'block_on_official_review_requests',
        'block_on_outdated_branch' => 'block_on_outdated_branch',
        'block_on_rejected_reviews' => 'block_on_rejected_reviews',
        'branch_name' => 'branch_name',
        'created_at' => 'created_at',
        'dismiss_stale_approvals' => 'dismiss_stale_approvals',
        'enable_approvals_whitelist' => 'enable_approvals_whitelist',
        'enable_merge_whitelist' => 'enable_merge_whitelist',
        'enable_push' => 'enable_push',
        'enable_push_whitelist' => 'enable_push_whitelist',
        'enable_status_check' => 'enable_status_check',
        'merge_whitelist_teams' => 'merge_whitelist_teams',
        'merge_whitelist_usernames' => 'merge_whitelist_usernames',
        'protected_file_patterns' => 'protected_file_patterns',
        'push_whitelist_deploy_keys' => 'push_whitelist_deploy_keys',
        'push_whitelist_teams' => 'push_whitelist_teams',
        'push_whitelist_usernames' => 'push_whitelist_usernames',
        'require_signed_commits' => 'require_signed_commits',
        'required_approvals' => 'required_approvals',
        'rule_name' => 'rule_name',
        'status_check_contexts' => 'status_check_contexts',
        'unprotected_file_patterns' => 'unprotected_file_patterns',
        'updated_at' => 'updated_at'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'approvals_whitelist_teams' => 'setApprovalsWhitelistTeams',
        'approvals_whitelist_username' => 'setApprovalsWhitelistUsername',
        'block_on_official_review_requests' => 'setBlockOnOfficialReviewRequests',
        'block_on_outdated_branch' => 'setBlockOnOutdatedBranch',
        'block_on_rejected_reviews' => 'setBlockOnRejectedReviews',
        'branch_name' => 'setBranchName',
        'created_at' => 'setCreatedAt',
        'dismiss_stale_approvals' => 'setDismissStaleApprovals',
        'enable_approvals_whitelist' => 'setEnableApprovalsWhitelist',
        'enable_merge_whitelist' => 'setEnableMergeWhitelist',
        'enable_push' => 'setEnablePush',
        'enable_push_whitelist' => 'setEnablePushWhitelist',
        'enable_status_check' => 'setEnableStatusCheck',
        'merge_whitelist_teams' => 'setMergeWhitelistTeams',
        'merge_whitelist_usernames' => 'setMergeWhitelistUsernames',
        'protected_file_patterns' => 'setProtectedFilePatterns',
        'push_whitelist_deploy_keys' => 'setPushWhitelistDeployKeys',
        'push_whitelist_teams' => 'setPushWhitelistTeams',
        'push_whitelist_usernames' => 'setPushWhitelistUsernames',
        'require_signed_commits' => 'setRequireSignedCommits',
        'required_approvals' => 'setRequiredApprovals',
        'rule_name' => 'setRuleName',
        'status_check_contexts' => 'setStatusCheckContexts',
        'unprotected_file_patterns' => 'setUnprotectedFilePatterns',
        'updated_at' => 'setUpdatedAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'approvals_whitelist_teams' => 'getApprovalsWhitelistTeams',
        'approvals_whitelist_username' => 'getApprovalsWhitelistUsername',
        'block_on_official_review_requests' => 'getBlockOnOfficialReviewRequests',
        'block_on_outdated_branch' => 'getBlockOnOutdatedBranch',
        'block_on_rejected_reviews' => 'getBlockOnRejectedReviews',
        'branch_name' => 'getBranchName',
        'created_at' => 'getCreatedAt',
        'dismiss_stale_approvals' => 'getDismissStaleApprovals',
        'enable_approvals_whitelist' => 'getEnableApprovalsWhitelist',
        'enable_merge_whitelist' => 'getEnableMergeWhitelist',
        'enable_push' => 'getEnablePush',
        'enable_push_whitelist' => 'getEnablePushWhitelist',
        'enable_status_check' => 'getEnableStatusCheck',
        'merge_whitelist_teams' => 'getMergeWhitelistTeams',
        'merge_whitelist_usernames' => 'getMergeWhitelistUsernames',
        'protected_file_patterns' => 'getProtectedFilePatterns',
        'push_whitelist_deploy_keys' => 'getPushWhitelistDeployKeys',
        'push_whitelist_teams' => 'getPushWhitelistTeams',
        'push_whitelist_usernames' => 'getPushWhitelistUsernames',
        'require_signed_commits' => 'getRequireSignedCommits',
        'required_approvals' => 'getRequiredApprovals',
        'rule_name' => 'getRuleName',
        'status_check_contexts' => 'getStatusCheckContexts',
        'unprotected_file_patterns' => 'getUnprotectedFilePatterns',
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
        $this->setIfExists('approvals_whitelist_teams', $data ?? [], null);
        $this->setIfExists('approvals_whitelist_username', $data ?? [], null);
        $this->setIfExists('block_on_official_review_requests', $data ?? [], null);
        $this->setIfExists('block_on_outdated_branch', $data ?? [], null);
        $this->setIfExists('block_on_rejected_reviews', $data ?? [], null);
        $this->setIfExists('branch_name', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('dismiss_stale_approvals', $data ?? [], null);
        $this->setIfExists('enable_approvals_whitelist', $data ?? [], null);
        $this->setIfExists('enable_merge_whitelist', $data ?? [], null);
        $this->setIfExists('enable_push', $data ?? [], null);
        $this->setIfExists('enable_push_whitelist', $data ?? [], null);
        $this->setIfExists('enable_status_check', $data ?? [], null);
        $this->setIfExists('merge_whitelist_teams', $data ?? [], null);
        $this->setIfExists('merge_whitelist_usernames', $data ?? [], null);
        $this->setIfExists('protected_file_patterns', $data ?? [], null);
        $this->setIfExists('push_whitelist_deploy_keys', $data ?? [], null);
        $this->setIfExists('push_whitelist_teams', $data ?? [], null);
        $this->setIfExists('push_whitelist_usernames', $data ?? [], null);
        $this->setIfExists('require_signed_commits', $data ?? [], null);
        $this->setIfExists('required_approvals', $data ?? [], null);
        $this->setIfExists('rule_name', $data ?? [], null);
        $this->setIfExists('status_check_contexts', $data ?? [], null);
        $this->setIfExists('unprotected_file_patterns', $data ?? [], null);
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
     * Gets approvals_whitelist_teams
     *
     * @return string[]|null
     */
    public function getApprovalsWhitelistTeams()
    {
        return $this->container['approvals_whitelist_teams'];
    }

    /**
     * Sets approvals_whitelist_teams
     *
     * @param string[]|null $approvals_whitelist_teams approvals_whitelist_teams
     *
     * @return self
     */
    public function setApprovalsWhitelistTeams($approvals_whitelist_teams)
    {
        if (is_null($approvals_whitelist_teams)) {
            throw new \InvalidArgumentException('non-nullable approvals_whitelist_teams cannot be null');
        }
        $this->container['approvals_whitelist_teams'] = $approvals_whitelist_teams;

        return $this;
    }

    /**
     * Gets approvals_whitelist_username
     *
     * @return string[]|null
     */
    public function getApprovalsWhitelistUsername()
    {
        return $this->container['approvals_whitelist_username'];
    }

    /**
     * Sets approvals_whitelist_username
     *
     * @param string[]|null $approvals_whitelist_username approvals_whitelist_username
     *
     * @return self
     */
    public function setApprovalsWhitelistUsername($approvals_whitelist_username)
    {
        if (is_null($approvals_whitelist_username)) {
            throw new \InvalidArgumentException('non-nullable approvals_whitelist_username cannot be null');
        }
        $this->container['approvals_whitelist_username'] = $approvals_whitelist_username;

        return $this;
    }

    /**
     * Gets block_on_official_review_requests
     *
     * @return bool|null
     */
    public function getBlockOnOfficialReviewRequests()
    {
        return $this->container['block_on_official_review_requests'];
    }

    /**
     * Sets block_on_official_review_requests
     *
     * @param bool|null $block_on_official_review_requests block_on_official_review_requests
     *
     * @return self
     */
    public function setBlockOnOfficialReviewRequests($block_on_official_review_requests)
    {
        if (is_null($block_on_official_review_requests)) {
            throw new \InvalidArgumentException('non-nullable block_on_official_review_requests cannot be null');
        }
        $this->container['block_on_official_review_requests'] = $block_on_official_review_requests;

        return $this;
    }

    /**
     * Gets block_on_outdated_branch
     *
     * @return bool|null
     */
    public function getBlockOnOutdatedBranch()
    {
        return $this->container['block_on_outdated_branch'];
    }

    /**
     * Sets block_on_outdated_branch
     *
     * @param bool|null $block_on_outdated_branch block_on_outdated_branch
     *
     * @return self
     */
    public function setBlockOnOutdatedBranch($block_on_outdated_branch)
    {
        if (is_null($block_on_outdated_branch)) {
            throw new \InvalidArgumentException('non-nullable block_on_outdated_branch cannot be null');
        }
        $this->container['block_on_outdated_branch'] = $block_on_outdated_branch;

        return $this;
    }

    /**
     * Gets block_on_rejected_reviews
     *
     * @return bool|null
     */
    public function getBlockOnRejectedReviews()
    {
        return $this->container['block_on_rejected_reviews'];
    }

    /**
     * Sets block_on_rejected_reviews
     *
     * @param bool|null $block_on_rejected_reviews block_on_rejected_reviews
     *
     * @return self
     */
    public function setBlockOnRejectedReviews($block_on_rejected_reviews)
    {
        if (is_null($block_on_rejected_reviews)) {
            throw new \InvalidArgumentException('non-nullable block_on_rejected_reviews cannot be null');
        }
        $this->container['block_on_rejected_reviews'] = $block_on_rejected_reviews;

        return $this;
    }

    /**
     * Gets branch_name
     *
     * @return string|null
     */
    public function getBranchName()
    {
        return $this->container['branch_name'];
    }

    /**
     * Sets branch_name
     *
     * @param string|null $branch_name Deprecated: true
     *
     * @return self
     */
    public function setBranchName($branch_name)
    {
        if (is_null($branch_name)) {
            throw new \InvalidArgumentException('non-nullable branch_name cannot be null');
        }
        $this->container['branch_name'] = $branch_name;

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
     * Gets dismiss_stale_approvals
     *
     * @return bool|null
     */
    public function getDismissStaleApprovals()
    {
        return $this->container['dismiss_stale_approvals'];
    }

    /**
     * Sets dismiss_stale_approvals
     *
     * @param bool|null $dismiss_stale_approvals dismiss_stale_approvals
     *
     * @return self
     */
    public function setDismissStaleApprovals($dismiss_stale_approvals)
    {
        if (is_null($dismiss_stale_approvals)) {
            throw new \InvalidArgumentException('non-nullable dismiss_stale_approvals cannot be null');
        }
        $this->container['dismiss_stale_approvals'] = $dismiss_stale_approvals;

        return $this;
    }

    /**
     * Gets enable_approvals_whitelist
     *
     * @return bool|null
     */
    public function getEnableApprovalsWhitelist()
    {
        return $this->container['enable_approvals_whitelist'];
    }

    /**
     * Sets enable_approvals_whitelist
     *
     * @param bool|null $enable_approvals_whitelist enable_approvals_whitelist
     *
     * @return self
     */
    public function setEnableApprovalsWhitelist($enable_approvals_whitelist)
    {
        if (is_null($enable_approvals_whitelist)) {
            throw new \InvalidArgumentException('non-nullable enable_approvals_whitelist cannot be null');
        }
        $this->container['enable_approvals_whitelist'] = $enable_approvals_whitelist;

        return $this;
    }

    /**
     * Gets enable_merge_whitelist
     *
     * @return bool|null
     */
    public function getEnableMergeWhitelist()
    {
        return $this->container['enable_merge_whitelist'];
    }

    /**
     * Sets enable_merge_whitelist
     *
     * @param bool|null $enable_merge_whitelist enable_merge_whitelist
     *
     * @return self
     */
    public function setEnableMergeWhitelist($enable_merge_whitelist)
    {
        if (is_null($enable_merge_whitelist)) {
            throw new \InvalidArgumentException('non-nullable enable_merge_whitelist cannot be null');
        }
        $this->container['enable_merge_whitelist'] = $enable_merge_whitelist;

        return $this;
    }

    /**
     * Gets enable_push
     *
     * @return bool|null
     */
    public function getEnablePush()
    {
        return $this->container['enable_push'];
    }

    /**
     * Sets enable_push
     *
     * @param bool|null $enable_push enable_push
     *
     * @return self
     */
    public function setEnablePush($enable_push)
    {
        if (is_null($enable_push)) {
            throw new \InvalidArgumentException('non-nullable enable_push cannot be null');
        }
        $this->container['enable_push'] = $enable_push;

        return $this;
    }

    /**
     * Gets enable_push_whitelist
     *
     * @return bool|null
     */
    public function getEnablePushWhitelist()
    {
        return $this->container['enable_push_whitelist'];
    }

    /**
     * Sets enable_push_whitelist
     *
     * @param bool|null $enable_push_whitelist enable_push_whitelist
     *
     * @return self
     */
    public function setEnablePushWhitelist($enable_push_whitelist)
    {
        if (is_null($enable_push_whitelist)) {
            throw new \InvalidArgumentException('non-nullable enable_push_whitelist cannot be null');
        }
        $this->container['enable_push_whitelist'] = $enable_push_whitelist;

        return $this;
    }

    /**
     * Gets enable_status_check
     *
     * @return bool|null
     */
    public function getEnableStatusCheck()
    {
        return $this->container['enable_status_check'];
    }

    /**
     * Sets enable_status_check
     *
     * @param bool|null $enable_status_check enable_status_check
     *
     * @return self
     */
    public function setEnableStatusCheck($enable_status_check)
    {
        if (is_null($enable_status_check)) {
            throw new \InvalidArgumentException('non-nullable enable_status_check cannot be null');
        }
        $this->container['enable_status_check'] = $enable_status_check;

        return $this;
    }

    /**
     * Gets merge_whitelist_teams
     *
     * @return string[]|null
     */
    public function getMergeWhitelistTeams()
    {
        return $this->container['merge_whitelist_teams'];
    }

    /**
     * Sets merge_whitelist_teams
     *
     * @param string[]|null $merge_whitelist_teams merge_whitelist_teams
     *
     * @return self
     */
    public function setMergeWhitelistTeams($merge_whitelist_teams)
    {
        if (is_null($merge_whitelist_teams)) {
            throw new \InvalidArgumentException('non-nullable merge_whitelist_teams cannot be null');
        }
        $this->container['merge_whitelist_teams'] = $merge_whitelist_teams;

        return $this;
    }

    /**
     * Gets merge_whitelist_usernames
     *
     * @return string[]|null
     */
    public function getMergeWhitelistUsernames()
    {
        return $this->container['merge_whitelist_usernames'];
    }

    /**
     * Sets merge_whitelist_usernames
     *
     * @param string[]|null $merge_whitelist_usernames merge_whitelist_usernames
     *
     * @return self
     */
    public function setMergeWhitelistUsernames($merge_whitelist_usernames)
    {
        if (is_null($merge_whitelist_usernames)) {
            throw new \InvalidArgumentException('non-nullable merge_whitelist_usernames cannot be null');
        }
        $this->container['merge_whitelist_usernames'] = $merge_whitelist_usernames;

        return $this;
    }

    /**
     * Gets protected_file_patterns
     *
     * @return string|null
     */
    public function getProtectedFilePatterns()
    {
        return $this->container['protected_file_patterns'];
    }

    /**
     * Sets protected_file_patterns
     *
     * @param string|null $protected_file_patterns protected_file_patterns
     *
     * @return self
     */
    public function setProtectedFilePatterns($protected_file_patterns)
    {
        if (is_null($protected_file_patterns)) {
            throw new \InvalidArgumentException('non-nullable protected_file_patterns cannot be null');
        }
        $this->container['protected_file_patterns'] = $protected_file_patterns;

        return $this;
    }

    /**
     * Gets push_whitelist_deploy_keys
     *
     * @return bool|null
     */
    public function getPushWhitelistDeployKeys()
    {
        return $this->container['push_whitelist_deploy_keys'];
    }

    /**
     * Sets push_whitelist_deploy_keys
     *
     * @param bool|null $push_whitelist_deploy_keys push_whitelist_deploy_keys
     *
     * @return self
     */
    public function setPushWhitelistDeployKeys($push_whitelist_deploy_keys)
    {
        if (is_null($push_whitelist_deploy_keys)) {
            throw new \InvalidArgumentException('non-nullable push_whitelist_deploy_keys cannot be null');
        }
        $this->container['push_whitelist_deploy_keys'] = $push_whitelist_deploy_keys;

        return $this;
    }

    /**
     * Gets push_whitelist_teams
     *
     * @return string[]|null
     */
    public function getPushWhitelistTeams()
    {
        return $this->container['push_whitelist_teams'];
    }

    /**
     * Sets push_whitelist_teams
     *
     * @param string[]|null $push_whitelist_teams push_whitelist_teams
     *
     * @return self
     */
    public function setPushWhitelistTeams($push_whitelist_teams)
    {
        if (is_null($push_whitelist_teams)) {
            throw new \InvalidArgumentException('non-nullable push_whitelist_teams cannot be null');
        }
        $this->container['push_whitelist_teams'] = $push_whitelist_teams;

        return $this;
    }

    /**
     * Gets push_whitelist_usernames
     *
     * @return string[]|null
     */
    public function getPushWhitelistUsernames()
    {
        return $this->container['push_whitelist_usernames'];
    }

    /**
     * Sets push_whitelist_usernames
     *
     * @param string[]|null $push_whitelist_usernames push_whitelist_usernames
     *
     * @return self
     */
    public function setPushWhitelistUsernames($push_whitelist_usernames)
    {
        if (is_null($push_whitelist_usernames)) {
            throw new \InvalidArgumentException('non-nullable push_whitelist_usernames cannot be null');
        }
        $this->container['push_whitelist_usernames'] = $push_whitelist_usernames;

        return $this;
    }

    /**
     * Gets require_signed_commits
     *
     * @return bool|null
     */
    public function getRequireSignedCommits()
    {
        return $this->container['require_signed_commits'];
    }

    /**
     * Sets require_signed_commits
     *
     * @param bool|null $require_signed_commits require_signed_commits
     *
     * @return self
     */
    public function setRequireSignedCommits($require_signed_commits)
    {
        if (is_null($require_signed_commits)) {
            throw new \InvalidArgumentException('non-nullable require_signed_commits cannot be null');
        }
        $this->container['require_signed_commits'] = $require_signed_commits;

        return $this;
    }

    /**
     * Gets required_approvals
     *
     * @return int|null
     */
    public function getRequiredApprovals()
    {
        return $this->container['required_approvals'];
    }

    /**
     * Sets required_approvals
     *
     * @param int|null $required_approvals required_approvals
     *
     * @return self
     */
    public function setRequiredApprovals($required_approvals)
    {
        if (is_null($required_approvals)) {
            throw new \InvalidArgumentException('non-nullable required_approvals cannot be null');
        }
        $this->container['required_approvals'] = $required_approvals;

        return $this;
    }

    /**
     * Gets rule_name
     *
     * @return string|null
     */
    public function getRuleName()
    {
        return $this->container['rule_name'];
    }

    /**
     * Sets rule_name
     *
     * @param string|null $rule_name rule_name
     *
     * @return self
     */
    public function setRuleName($rule_name)
    {
        if (is_null($rule_name)) {
            throw new \InvalidArgumentException('non-nullable rule_name cannot be null');
        }
        $this->container['rule_name'] = $rule_name;

        return $this;
    }

    /**
     * Gets status_check_contexts
     *
     * @return string[]|null
     */
    public function getStatusCheckContexts()
    {
        return $this->container['status_check_contexts'];
    }

    /**
     * Sets status_check_contexts
     *
     * @param string[]|null $status_check_contexts status_check_contexts
     *
     * @return self
     */
    public function setStatusCheckContexts($status_check_contexts)
    {
        if (is_null($status_check_contexts)) {
            throw new \InvalidArgumentException('non-nullable status_check_contexts cannot be null');
        }
        $this->container['status_check_contexts'] = $status_check_contexts;

        return $this;
    }

    /**
     * Gets unprotected_file_patterns
     *
     * @return string|null
     */
    public function getUnprotectedFilePatterns()
    {
        return $this->container['unprotected_file_patterns'];
    }

    /**
     * Sets unprotected_file_patterns
     *
     * @param string|null $unprotected_file_patterns unprotected_file_patterns
     *
     * @return self
     */
    public function setUnprotectedFilePatterns($unprotected_file_patterns)
    {
        if (is_null($unprotected_file_patterns)) {
            throw new \InvalidArgumentException('non-nullable unprotected_file_patterns cannot be null');
        }
        $this->container['unprotected_file_patterns'] = $unprotected_file_patterns;

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


