<?php
/**
 * CreateBranchProtectionOption
 *
 * PHP version 5
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Gitea API.
 *
 * This documentation describes the Gitea API.
 *
 * OpenAPI spec version: 1.21.7
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.32-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model;

use \ArrayAccess;
use \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Base\ObjectSerializer;

/**
 * CreateBranchProtectionOption Class Doc Comment
 *
 * @category Class
 * @description CreateBranchProtectionOption options for creating a branch protection
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreateBranchProtectionOption implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'CreateBranchProtectionOption';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'approvals_whitelist_teams' => 'string[]',
        'approvals_whitelist_username' => 'string[]',
        'block_on_official_review_requests' => 'bool',
        'block_on_outdated_branch' => 'bool',
        'block_on_rejected_reviews' => 'bool',
        'branch_name' => 'string',
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
        'unprotected_file_patterns' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'approvals_whitelist_teams' => null,
        'approvals_whitelist_username' => null,
        'block_on_official_review_requests' => null,
        'block_on_outdated_branch' => null,
        'block_on_rejected_reviews' => null,
        'branch_name' => null,
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
        'unprotected_file_patterns' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
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
        'unprotected_file_patterns' => 'unprotected_file_patterns'
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
        'unprotected_file_patterns' => 'setUnprotectedFilePatterns'
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
        'unprotected_file_patterns' => 'getUnprotectedFilePatterns'
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
        return self::$swaggerModelName;
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
        $this->container['approvals_whitelist_teams'] = isset($data['approvals_whitelist_teams']) ? $data['approvals_whitelist_teams'] : null;
        $this->container['approvals_whitelist_username'] = isset($data['approvals_whitelist_username']) ? $data['approvals_whitelist_username'] : null;
        $this->container['block_on_official_review_requests'] = isset($data['block_on_official_review_requests']) ? $data['block_on_official_review_requests'] : null;
        $this->container['block_on_outdated_branch'] = isset($data['block_on_outdated_branch']) ? $data['block_on_outdated_branch'] : null;
        $this->container['block_on_rejected_reviews'] = isset($data['block_on_rejected_reviews']) ? $data['block_on_rejected_reviews'] : null;
        $this->container['branch_name'] = isset($data['branch_name']) ? $data['branch_name'] : null;
        $this->container['dismiss_stale_approvals'] = isset($data['dismiss_stale_approvals']) ? $data['dismiss_stale_approvals'] : null;
        $this->container['enable_approvals_whitelist'] = isset($data['enable_approvals_whitelist']) ? $data['enable_approvals_whitelist'] : null;
        $this->container['enable_merge_whitelist'] = isset($data['enable_merge_whitelist']) ? $data['enable_merge_whitelist'] : null;
        $this->container['enable_push'] = isset($data['enable_push']) ? $data['enable_push'] : null;
        $this->container['enable_push_whitelist'] = isset($data['enable_push_whitelist']) ? $data['enable_push_whitelist'] : null;
        $this->container['enable_status_check'] = isset($data['enable_status_check']) ? $data['enable_status_check'] : null;
        $this->container['merge_whitelist_teams'] = isset($data['merge_whitelist_teams']) ? $data['merge_whitelist_teams'] : null;
        $this->container['merge_whitelist_usernames'] = isset($data['merge_whitelist_usernames']) ? $data['merge_whitelist_usernames'] : null;
        $this->container['protected_file_patterns'] = isset($data['protected_file_patterns']) ? $data['protected_file_patterns'] : null;
        $this->container['push_whitelist_deploy_keys'] = isset($data['push_whitelist_deploy_keys']) ? $data['push_whitelist_deploy_keys'] : null;
        $this->container['push_whitelist_teams'] = isset($data['push_whitelist_teams']) ? $data['push_whitelist_teams'] : null;
        $this->container['push_whitelist_usernames'] = isset($data['push_whitelist_usernames']) ? $data['push_whitelist_usernames'] : null;
        $this->container['require_signed_commits'] = isset($data['require_signed_commits']) ? $data['require_signed_commits'] : null;
        $this->container['required_approvals'] = isset($data['required_approvals']) ? $data['required_approvals'] : null;
        $this->container['rule_name'] = isset($data['rule_name']) ? $data['rule_name'] : null;
        $this->container['status_check_contexts'] = isset($data['status_check_contexts']) ? $data['status_check_contexts'] : null;
        $this->container['unprotected_file_patterns'] = isset($data['unprotected_file_patterns']) ? $data['unprotected_file_patterns'] : null;
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
     * @return string[]
     */
    public function getApprovalsWhitelistTeams()
    {
        return $this->container['approvals_whitelist_teams'];
    }

    /**
     * Sets approvals_whitelist_teams
     *
     * @param string[] $approvals_whitelist_teams approvals_whitelist_teams
     *
     * @return $this
     */
    public function setApprovalsWhitelistTeams($approvals_whitelist_teams)
    {
        $this->container['approvals_whitelist_teams'] = $approvals_whitelist_teams;

        return $this;
    }

    /**
     * Gets approvals_whitelist_username
     *
     * @return string[]
     */
    public function getApprovalsWhitelistUsername()
    {
        return $this->container['approvals_whitelist_username'];
    }

    /**
     * Sets approvals_whitelist_username
     *
     * @param string[] $approvals_whitelist_username approvals_whitelist_username
     *
     * @return $this
     */
    public function setApprovalsWhitelistUsername($approvals_whitelist_username)
    {
        $this->container['approvals_whitelist_username'] = $approvals_whitelist_username;

        return $this;
    }

    /**
     * Gets block_on_official_review_requests
     *
     * @return bool
     */
    public function getBlockOnOfficialReviewRequests()
    {
        return $this->container['block_on_official_review_requests'];
    }

    /**
     * Sets block_on_official_review_requests
     *
     * @param bool $block_on_official_review_requests block_on_official_review_requests
     *
     * @return $this
     */
    public function setBlockOnOfficialReviewRequests($block_on_official_review_requests)
    {
        $this->container['block_on_official_review_requests'] = $block_on_official_review_requests;

        return $this;
    }

    /**
     * Gets block_on_outdated_branch
     *
     * @return bool
     */
    public function getBlockOnOutdatedBranch()
    {
        return $this->container['block_on_outdated_branch'];
    }

    /**
     * Sets block_on_outdated_branch
     *
     * @param bool $block_on_outdated_branch block_on_outdated_branch
     *
     * @return $this
     */
    public function setBlockOnOutdatedBranch($block_on_outdated_branch)
    {
        $this->container['block_on_outdated_branch'] = $block_on_outdated_branch;

        return $this;
    }

    /**
     * Gets block_on_rejected_reviews
     *
     * @return bool
     */
    public function getBlockOnRejectedReviews()
    {
        return $this->container['block_on_rejected_reviews'];
    }

    /**
     * Sets block_on_rejected_reviews
     *
     * @param bool $block_on_rejected_reviews block_on_rejected_reviews
     *
     * @return $this
     */
    public function setBlockOnRejectedReviews($block_on_rejected_reviews)
    {
        $this->container['block_on_rejected_reviews'] = $block_on_rejected_reviews;

        return $this;
    }

    /**
     * Gets branch_name
     *
     * @return string
     */
    public function getBranchName()
    {
        return $this->container['branch_name'];
    }

    /**
     * Sets branch_name
     *
     * @param string $branch_name Deprecated: true
     *
     * @return $this
     */
    public function setBranchName($branch_name)
    {
        $this->container['branch_name'] = $branch_name;

        return $this;
    }

    /**
     * Gets dismiss_stale_approvals
     *
     * @return bool
     */
    public function getDismissStaleApprovals()
    {
        return $this->container['dismiss_stale_approvals'];
    }

    /**
     * Sets dismiss_stale_approvals
     *
     * @param bool $dismiss_stale_approvals dismiss_stale_approvals
     *
     * @return $this
     */
    public function setDismissStaleApprovals($dismiss_stale_approvals)
    {
        $this->container['dismiss_stale_approvals'] = $dismiss_stale_approvals;

        return $this;
    }

    /**
     * Gets enable_approvals_whitelist
     *
     * @return bool
     */
    public function getEnableApprovalsWhitelist()
    {
        return $this->container['enable_approvals_whitelist'];
    }

    /**
     * Sets enable_approvals_whitelist
     *
     * @param bool $enable_approvals_whitelist enable_approvals_whitelist
     *
     * @return $this
     */
    public function setEnableApprovalsWhitelist($enable_approvals_whitelist)
    {
        $this->container['enable_approvals_whitelist'] = $enable_approvals_whitelist;

        return $this;
    }

    /**
     * Gets enable_merge_whitelist
     *
     * @return bool
     */
    public function getEnableMergeWhitelist()
    {
        return $this->container['enable_merge_whitelist'];
    }

    /**
     * Sets enable_merge_whitelist
     *
     * @param bool $enable_merge_whitelist enable_merge_whitelist
     *
     * @return $this
     */
    public function setEnableMergeWhitelist($enable_merge_whitelist)
    {
        $this->container['enable_merge_whitelist'] = $enable_merge_whitelist;

        return $this;
    }

    /**
     * Gets enable_push
     *
     * @return bool
     */
    public function getEnablePush()
    {
        return $this->container['enable_push'];
    }

    /**
     * Sets enable_push
     *
     * @param bool $enable_push enable_push
     *
     * @return $this
     */
    public function setEnablePush($enable_push)
    {
        $this->container['enable_push'] = $enable_push;

        return $this;
    }

    /**
     * Gets enable_push_whitelist
     *
     * @return bool
     */
    public function getEnablePushWhitelist()
    {
        return $this->container['enable_push_whitelist'];
    }

    /**
     * Sets enable_push_whitelist
     *
     * @param bool $enable_push_whitelist enable_push_whitelist
     *
     * @return $this
     */
    public function setEnablePushWhitelist($enable_push_whitelist)
    {
        $this->container['enable_push_whitelist'] = $enable_push_whitelist;

        return $this;
    }

    /**
     * Gets enable_status_check
     *
     * @return bool
     */
    public function getEnableStatusCheck()
    {
        return $this->container['enable_status_check'];
    }

    /**
     * Sets enable_status_check
     *
     * @param bool $enable_status_check enable_status_check
     *
     * @return $this
     */
    public function setEnableStatusCheck($enable_status_check)
    {
        $this->container['enable_status_check'] = $enable_status_check;

        return $this;
    }

    /**
     * Gets merge_whitelist_teams
     *
     * @return string[]
     */
    public function getMergeWhitelistTeams()
    {
        return $this->container['merge_whitelist_teams'];
    }

    /**
     * Sets merge_whitelist_teams
     *
     * @param string[] $merge_whitelist_teams merge_whitelist_teams
     *
     * @return $this
     */
    public function setMergeWhitelistTeams($merge_whitelist_teams)
    {
        $this->container['merge_whitelist_teams'] = $merge_whitelist_teams;

        return $this;
    }

    /**
     * Gets merge_whitelist_usernames
     *
     * @return string[]
     */
    public function getMergeWhitelistUsernames()
    {
        return $this->container['merge_whitelist_usernames'];
    }

    /**
     * Sets merge_whitelist_usernames
     *
     * @param string[] $merge_whitelist_usernames merge_whitelist_usernames
     *
     * @return $this
     */
    public function setMergeWhitelistUsernames($merge_whitelist_usernames)
    {
        $this->container['merge_whitelist_usernames'] = $merge_whitelist_usernames;

        return $this;
    }

    /**
     * Gets protected_file_patterns
     *
     * @return string
     */
    public function getProtectedFilePatterns()
    {
        return $this->container['protected_file_patterns'];
    }

    /**
     * Sets protected_file_patterns
     *
     * @param string $protected_file_patterns protected_file_patterns
     *
     * @return $this
     */
    public function setProtectedFilePatterns($protected_file_patterns)
    {
        $this->container['protected_file_patterns'] = $protected_file_patterns;

        return $this;
    }

    /**
     * Gets push_whitelist_deploy_keys
     *
     * @return bool
     */
    public function getPushWhitelistDeployKeys()
    {
        return $this->container['push_whitelist_deploy_keys'];
    }

    /**
     * Sets push_whitelist_deploy_keys
     *
     * @param bool $push_whitelist_deploy_keys push_whitelist_deploy_keys
     *
     * @return $this
     */
    public function setPushWhitelistDeployKeys($push_whitelist_deploy_keys)
    {
        $this->container['push_whitelist_deploy_keys'] = $push_whitelist_deploy_keys;

        return $this;
    }

    /**
     * Gets push_whitelist_teams
     *
     * @return string[]
     */
    public function getPushWhitelistTeams()
    {
        return $this->container['push_whitelist_teams'];
    }

    /**
     * Sets push_whitelist_teams
     *
     * @param string[] $push_whitelist_teams push_whitelist_teams
     *
     * @return $this
     */
    public function setPushWhitelistTeams($push_whitelist_teams)
    {
        $this->container['push_whitelist_teams'] = $push_whitelist_teams;

        return $this;
    }

    /**
     * Gets push_whitelist_usernames
     *
     * @return string[]
     */
    public function getPushWhitelistUsernames()
    {
        return $this->container['push_whitelist_usernames'];
    }

    /**
     * Sets push_whitelist_usernames
     *
     * @param string[] $push_whitelist_usernames push_whitelist_usernames
     *
     * @return $this
     */
    public function setPushWhitelistUsernames($push_whitelist_usernames)
    {
        $this->container['push_whitelist_usernames'] = $push_whitelist_usernames;

        return $this;
    }

    /**
     * Gets require_signed_commits
     *
     * @return bool
     */
    public function getRequireSignedCommits()
    {
        return $this->container['require_signed_commits'];
    }

    /**
     * Sets require_signed_commits
     *
     * @param bool $require_signed_commits require_signed_commits
     *
     * @return $this
     */
    public function setRequireSignedCommits($require_signed_commits)
    {
        $this->container['require_signed_commits'] = $require_signed_commits;

        return $this;
    }

    /**
     * Gets required_approvals
     *
     * @return int
     */
    public function getRequiredApprovals()
    {
        return $this->container['required_approvals'];
    }

    /**
     * Sets required_approvals
     *
     * @param int $required_approvals required_approvals
     *
     * @return $this
     */
    public function setRequiredApprovals($required_approvals)
    {
        $this->container['required_approvals'] = $required_approvals;

        return $this;
    }

    /**
     * Gets rule_name
     *
     * @return string
     */
    public function getRuleName()
    {
        return $this->container['rule_name'];
    }

    /**
     * Sets rule_name
     *
     * @param string $rule_name rule_name
     *
     * @return $this
     */
    public function setRuleName($rule_name)
    {
        $this->container['rule_name'] = $rule_name;

        return $this;
    }

    /**
     * Gets status_check_contexts
     *
     * @return string[]
     */
    public function getStatusCheckContexts()
    {
        return $this->container['status_check_contexts'];
    }

    /**
     * Sets status_check_contexts
     *
     * @param string[] $status_check_contexts status_check_contexts
     *
     * @return $this
     */
    public function setStatusCheckContexts($status_check_contexts)
    {
        $this->container['status_check_contexts'] = $status_check_contexts;

        return $this;
    }

    /**
     * Gets unprotected_file_patterns
     *
     * @return string
     */
    public function getUnprotectedFilePatterns()
    {
        return $this->container['unprotected_file_patterns'];
    }

    /**
     * Sets unprotected_file_patterns
     *
     * @param string $unprotected_file_patterns unprotected_file_patterns
     *
     * @return $this
     */
    public function setUnprotectedFilePatterns($unprotected_file_patterns)
    {
        $this->container['unprotected_file_patterns'] = $unprotected_file_patterns;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
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
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


