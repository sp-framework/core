<?php
/**
 * EditRepoOption
 *
 * PHP version 5
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Gitea API.
 *
 * This documentation describes the Gitea API.
 *
 * OpenAPI spec version: 1.19.1
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.32-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model;

use \ArrayAccess;
use \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\ObjectSerializer;

/**
 * EditRepoOption Class Doc Comment
 *
 * @category Class
 * @description EditRepoOption options when editing a repository&#39;s properties
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class EditRepoOption implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'EditRepoOption';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'allow_manual_merge' => 'bool',
        'allow_merge_commits' => 'bool',
        'allow_rebase' => 'bool',
        'allow_rebase_explicit' => 'bool',
        'allow_rebase_update' => 'bool',
        'allow_squash_merge' => 'bool',
        'archived' => 'bool',
        'autodetect_manual_merge' => 'bool',
        'default_allow_maintainer_edit' => 'bool',
        'default_branch' => 'string',
        'default_delete_branch_after_merge' => 'bool',
        'default_merge_style' => 'string',
        'description' => 'string',
        'enable_prune' => 'bool',
        'external_tracker' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\ExternalTracker',
        'external_wiki' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\ExternalWiki',
        'has_issues' => 'bool',
        'has_projects' => 'bool',
        'has_pull_requests' => 'bool',
        'has_wiki' => 'bool',
        'ignore_whitespace_conflicts' => 'bool',
        'internal_tracker' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\InternalTracker',
        'mirror_interval' => 'string',
        'name' => 'string',
        'private' => 'bool',
        'template' => 'bool',
        'website' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'allow_manual_merge' => null,
        'allow_merge_commits' => null,
        'allow_rebase' => null,
        'allow_rebase_explicit' => null,
        'allow_rebase_update' => null,
        'allow_squash_merge' => null,
        'archived' => null,
        'autodetect_manual_merge' => null,
        'default_allow_maintainer_edit' => null,
        'default_branch' => null,
        'default_delete_branch_after_merge' => null,
        'default_merge_style' => null,
        'description' => null,
        'enable_prune' => null,
        'external_tracker' => null,
        'external_wiki' => null,
        'has_issues' => null,
        'has_projects' => null,
        'has_pull_requests' => null,
        'has_wiki' => null,
        'ignore_whitespace_conflicts' => null,
        'internal_tracker' => null,
        'mirror_interval' => null,
        'name' => null,
        'private' => null,
        'template' => null,
        'website' => null
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
        'allow_manual_merge' => 'allow_manual_merge',
        'allow_merge_commits' => 'allow_merge_commits',
        'allow_rebase' => 'allow_rebase',
        'allow_rebase_explicit' => 'allow_rebase_explicit',
        'allow_rebase_update' => 'allow_rebase_update',
        'allow_squash_merge' => 'allow_squash_merge',
        'archived' => 'archived',
        'autodetect_manual_merge' => 'autodetect_manual_merge',
        'default_allow_maintainer_edit' => 'default_allow_maintainer_edit',
        'default_branch' => 'default_branch',
        'default_delete_branch_after_merge' => 'default_delete_branch_after_merge',
        'default_merge_style' => 'default_merge_style',
        'description' => 'description',
        'enable_prune' => 'enable_prune',
        'external_tracker' => 'external_tracker',
        'external_wiki' => 'external_wiki',
        'has_issues' => 'has_issues',
        'has_projects' => 'has_projects',
        'has_pull_requests' => 'has_pull_requests',
        'has_wiki' => 'has_wiki',
        'ignore_whitespace_conflicts' => 'ignore_whitespace_conflicts',
        'internal_tracker' => 'internal_tracker',
        'mirror_interval' => 'mirror_interval',
        'name' => 'name',
        'private' => 'private',
        'template' => 'template',
        'website' => 'website'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'allow_manual_merge' => 'setAllowManualMerge',
        'allow_merge_commits' => 'setAllowMergeCommits',
        'allow_rebase' => 'setAllowRebase',
        'allow_rebase_explicit' => 'setAllowRebaseExplicit',
        'allow_rebase_update' => 'setAllowRebaseUpdate',
        'allow_squash_merge' => 'setAllowSquashMerge',
        'archived' => 'setArchived',
        'autodetect_manual_merge' => 'setAutodetectManualMerge',
        'default_allow_maintainer_edit' => 'setDefaultAllowMaintainerEdit',
        'default_branch' => 'setDefaultBranch',
        'default_delete_branch_after_merge' => 'setDefaultDeleteBranchAfterMerge',
        'default_merge_style' => 'setDefaultMergeStyle',
        'description' => 'setDescription',
        'enable_prune' => 'setEnablePrune',
        'external_tracker' => 'setExternalTracker',
        'external_wiki' => 'setExternalWiki',
        'has_issues' => 'setHasIssues',
        'has_projects' => 'setHasProjects',
        'has_pull_requests' => 'setHasPullRequests',
        'has_wiki' => 'setHasWiki',
        'ignore_whitespace_conflicts' => 'setIgnoreWhitespaceConflicts',
        'internal_tracker' => 'setInternalTracker',
        'mirror_interval' => 'setMirrorInterval',
        'name' => 'setName',
        'private' => 'setPrivate',
        'template' => 'setTemplate',
        'website' => 'setWebsite'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'allow_manual_merge' => 'getAllowManualMerge',
        'allow_merge_commits' => 'getAllowMergeCommits',
        'allow_rebase' => 'getAllowRebase',
        'allow_rebase_explicit' => 'getAllowRebaseExplicit',
        'allow_rebase_update' => 'getAllowRebaseUpdate',
        'allow_squash_merge' => 'getAllowSquashMerge',
        'archived' => 'getArchived',
        'autodetect_manual_merge' => 'getAutodetectManualMerge',
        'default_allow_maintainer_edit' => 'getDefaultAllowMaintainerEdit',
        'default_branch' => 'getDefaultBranch',
        'default_delete_branch_after_merge' => 'getDefaultDeleteBranchAfterMerge',
        'default_merge_style' => 'getDefaultMergeStyle',
        'description' => 'getDescription',
        'enable_prune' => 'getEnablePrune',
        'external_tracker' => 'getExternalTracker',
        'external_wiki' => 'getExternalWiki',
        'has_issues' => 'getHasIssues',
        'has_projects' => 'getHasProjects',
        'has_pull_requests' => 'getHasPullRequests',
        'has_wiki' => 'getHasWiki',
        'ignore_whitespace_conflicts' => 'getIgnoreWhitespaceConflicts',
        'internal_tracker' => 'getInternalTracker',
        'mirror_interval' => 'getMirrorInterval',
        'name' => 'getName',
        'private' => 'getPrivate',
        'template' => 'getTemplate',
        'website' => 'getWebsite'
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
        $this->container['allow_manual_merge'] = isset($data['allow_manual_merge']) ? $data['allow_manual_merge'] : null;
        $this->container['allow_merge_commits'] = isset($data['allow_merge_commits']) ? $data['allow_merge_commits'] : null;
        $this->container['allow_rebase'] = isset($data['allow_rebase']) ? $data['allow_rebase'] : null;
        $this->container['allow_rebase_explicit'] = isset($data['allow_rebase_explicit']) ? $data['allow_rebase_explicit'] : null;
        $this->container['allow_rebase_update'] = isset($data['allow_rebase_update']) ? $data['allow_rebase_update'] : null;
        $this->container['allow_squash_merge'] = isset($data['allow_squash_merge']) ? $data['allow_squash_merge'] : null;
        $this->container['archived'] = isset($data['archived']) ? $data['archived'] : null;
        $this->container['autodetect_manual_merge'] = isset($data['autodetect_manual_merge']) ? $data['autodetect_manual_merge'] : null;
        $this->container['default_allow_maintainer_edit'] = isset($data['default_allow_maintainer_edit']) ? $data['default_allow_maintainer_edit'] : null;
        $this->container['default_branch'] = isset($data['default_branch']) ? $data['default_branch'] : null;
        $this->container['default_delete_branch_after_merge'] = isset($data['default_delete_branch_after_merge']) ? $data['default_delete_branch_after_merge'] : null;
        $this->container['default_merge_style'] = isset($data['default_merge_style']) ? $data['default_merge_style'] : null;
        $this->container['description'] = isset($data['description']) ? $data['description'] : null;
        $this->container['enable_prune'] = isset($data['enable_prune']) ? $data['enable_prune'] : null;
        $this->container['external_tracker'] = isset($data['external_tracker']) ? $data['external_tracker'] : null;
        $this->container['external_wiki'] = isset($data['external_wiki']) ? $data['external_wiki'] : null;
        $this->container['has_issues'] = isset($data['has_issues']) ? $data['has_issues'] : null;
        $this->container['has_projects'] = isset($data['has_projects']) ? $data['has_projects'] : null;
        $this->container['has_pull_requests'] = isset($data['has_pull_requests']) ? $data['has_pull_requests'] : null;
        $this->container['has_wiki'] = isset($data['has_wiki']) ? $data['has_wiki'] : null;
        $this->container['ignore_whitespace_conflicts'] = isset($data['ignore_whitespace_conflicts']) ? $data['ignore_whitespace_conflicts'] : null;
        $this->container['internal_tracker'] = isset($data['internal_tracker']) ? $data['internal_tracker'] : null;
        $this->container['mirror_interval'] = isset($data['mirror_interval']) ? $data['mirror_interval'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['private'] = isset($data['private']) ? $data['private'] : null;
        $this->container['template'] = isset($data['template']) ? $data['template'] : null;
        $this->container['website'] = isset($data['website']) ? $data['website'] : null;
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
     * Gets allow_manual_merge
     *
     * @return bool
     */
    public function getAllowManualMerge()
    {
        return $this->container['allow_manual_merge'];
    }

    /**
     * Sets allow_manual_merge
     *
     * @param bool $allow_manual_merge either `true` to allow mark pr as merged manually, or `false` to prevent it.
     *
     * @return $this
     */
    public function setAllowManualMerge($allow_manual_merge)
    {
        $this->container['allow_manual_merge'] = $allow_manual_merge;

        return $this;
    }

    /**
     * Gets allow_merge_commits
     *
     * @return bool
     */
    public function getAllowMergeCommits()
    {
        return $this->container['allow_merge_commits'];
    }

    /**
     * Sets allow_merge_commits
     *
     * @param bool $allow_merge_commits either `true` to allow merging pull requests with a merge commit, or `false` to prevent merging pull requests with merge commits.
     *
     * @return $this
     */
    public function setAllowMergeCommits($allow_merge_commits)
    {
        $this->container['allow_merge_commits'] = $allow_merge_commits;

        return $this;
    }

    /**
     * Gets allow_rebase
     *
     * @return bool
     */
    public function getAllowRebase()
    {
        return $this->container['allow_rebase'];
    }

    /**
     * Sets allow_rebase
     *
     * @param bool $allow_rebase either `true` to allow rebase-merging pull requests, or `false` to prevent rebase-merging.
     *
     * @return $this
     */
    public function setAllowRebase($allow_rebase)
    {
        $this->container['allow_rebase'] = $allow_rebase;

        return $this;
    }

    /**
     * Gets allow_rebase_explicit
     *
     * @return bool
     */
    public function getAllowRebaseExplicit()
    {
        return $this->container['allow_rebase_explicit'];
    }

    /**
     * Sets allow_rebase_explicit
     *
     * @param bool $allow_rebase_explicit either `true` to allow rebase with explicit merge commits (--no-ff), or `false` to prevent rebase with explicit merge commits.
     *
     * @return $this
     */
    public function setAllowRebaseExplicit($allow_rebase_explicit)
    {
        $this->container['allow_rebase_explicit'] = $allow_rebase_explicit;

        return $this;
    }

    /**
     * Gets allow_rebase_update
     *
     * @return bool
     */
    public function getAllowRebaseUpdate()
    {
        return $this->container['allow_rebase_update'];
    }

    /**
     * Sets allow_rebase_update
     *
     * @param bool $allow_rebase_update either `true` to allow updating pull request branch by rebase, or `false` to prevent it.
     *
     * @return $this
     */
    public function setAllowRebaseUpdate($allow_rebase_update)
    {
        $this->container['allow_rebase_update'] = $allow_rebase_update;

        return $this;
    }

    /**
     * Gets allow_squash_merge
     *
     * @return bool
     */
    public function getAllowSquashMerge()
    {
        return $this->container['allow_squash_merge'];
    }

    /**
     * Sets allow_squash_merge
     *
     * @param bool $allow_squash_merge either `true` to allow squash-merging pull requests, or `false` to prevent squash-merging.
     *
     * @return $this
     */
    public function setAllowSquashMerge($allow_squash_merge)
    {
        $this->container['allow_squash_merge'] = $allow_squash_merge;

        return $this;
    }

    /**
     * Gets archived
     *
     * @return bool
     */
    public function getArchived()
    {
        return $this->container['archived'];
    }

    /**
     * Sets archived
     *
     * @param bool $archived set to `true` to archive this repository.
     *
     * @return $this
     */
    public function setArchived($archived)
    {
        $this->container['archived'] = $archived;

        return $this;
    }

    /**
     * Gets autodetect_manual_merge
     *
     * @return bool
     */
    public function getAutodetectManualMerge()
    {
        return $this->container['autodetect_manual_merge'];
    }

    /**
     * Sets autodetect_manual_merge
     *
     * @param bool $autodetect_manual_merge either `true` to enable AutodetectManualMerge, or `false` to prevent it. Note: In some special cases, misjudgments can occur.
     *
     * @return $this
     */
    public function setAutodetectManualMerge($autodetect_manual_merge)
    {
        $this->container['autodetect_manual_merge'] = $autodetect_manual_merge;

        return $this;
    }

    /**
     * Gets default_allow_maintainer_edit
     *
     * @return bool
     */
    public function getDefaultAllowMaintainerEdit()
    {
        return $this->container['default_allow_maintainer_edit'];
    }

    /**
     * Sets default_allow_maintainer_edit
     *
     * @param bool $default_allow_maintainer_edit set to `true` to allow edits from maintainers by default
     *
     * @return $this
     */
    public function setDefaultAllowMaintainerEdit($default_allow_maintainer_edit)
    {
        $this->container['default_allow_maintainer_edit'] = $default_allow_maintainer_edit;

        return $this;
    }

    /**
     * Gets default_branch
     *
     * @return string
     */
    public function getDefaultBranch()
    {
        return $this->container['default_branch'];
    }

    /**
     * Sets default_branch
     *
     * @param string $default_branch sets the default branch for this repository.
     *
     * @return $this
     */
    public function setDefaultBranch($default_branch)
    {
        $this->container['default_branch'] = $default_branch;

        return $this;
    }

    /**
     * Gets default_delete_branch_after_merge
     *
     * @return bool
     */
    public function getDefaultDeleteBranchAfterMerge()
    {
        return $this->container['default_delete_branch_after_merge'];
    }

    /**
     * Sets default_delete_branch_after_merge
     *
     * @param bool $default_delete_branch_after_merge set to `true` to delete pr branch after merge by default
     *
     * @return $this
     */
    public function setDefaultDeleteBranchAfterMerge($default_delete_branch_after_merge)
    {
        $this->container['default_delete_branch_after_merge'] = $default_delete_branch_after_merge;

        return $this;
    }

    /**
     * Gets default_merge_style
     *
     * @return string
     */
    public function getDefaultMergeStyle()
    {
        return $this->container['default_merge_style'];
    }

    /**
     * Sets default_merge_style
     *
     * @param string $default_merge_style set to a merge style to be used by this repository: \"merge\", \"rebase\", \"rebase-merge\", or \"squash\".
     *
     * @return $this
     */
    public function setDefaultMergeStyle($default_merge_style)
    {
        $this->container['default_merge_style'] = $default_merge_style;

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
     * @param string $description a short description of the repository.
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets enable_prune
     *
     * @return bool
     */
    public function getEnablePrune()
    {
        return $this->container['enable_prune'];
    }

    /**
     * Sets enable_prune
     *
     * @param bool $enable_prune enable prune - remove obsolete remote-tracking references
     *
     * @return $this
     */
    public function setEnablePrune($enable_prune)
    {
        $this->container['enable_prune'] = $enable_prune;

        return $this;
    }

    /**
     * Gets external_tracker
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\ExternalTracker
     */
    public function getExternalTracker()
    {
        return $this->container['external_tracker'];
    }

    /**
     * Sets external_tracker
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\ExternalTracker $external_tracker external_tracker
     *
     * @return $this
     */
    public function setExternalTracker($external_tracker)
    {
        $this->container['external_tracker'] = $external_tracker;

        return $this;
    }

    /**
     * Gets external_wiki
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\ExternalWiki
     */
    public function getExternalWiki()
    {
        return $this->container['external_wiki'];
    }

    /**
     * Sets external_wiki
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\ExternalWiki $external_wiki external_wiki
     *
     * @return $this
     */
    public function setExternalWiki($external_wiki)
    {
        $this->container['external_wiki'] = $external_wiki;

        return $this;
    }

    /**
     * Gets has_issues
     *
     * @return bool
     */
    public function getHasIssues()
    {
        return $this->container['has_issues'];
    }

    /**
     * Sets has_issues
     *
     * @param bool $has_issues either `true` to enable issues for this repository or `false` to disable them.
     *
     * @return $this
     */
    public function setHasIssues($has_issues)
    {
        $this->container['has_issues'] = $has_issues;

        return $this;
    }

    /**
     * Gets has_projects
     *
     * @return bool
     */
    public function getHasProjects()
    {
        return $this->container['has_projects'];
    }

    /**
     * Sets has_projects
     *
     * @param bool $has_projects either `true` to enable project unit, or `false` to disable them.
     *
     * @return $this
     */
    public function setHasProjects($has_projects)
    {
        $this->container['has_projects'] = $has_projects;

        return $this;
    }

    /**
     * Gets has_pull_requests
     *
     * @return bool
     */
    public function getHasPullRequests()
    {
        return $this->container['has_pull_requests'];
    }

    /**
     * Sets has_pull_requests
     *
     * @param bool $has_pull_requests either `true` to allow pull requests, or `false` to prevent pull request.
     *
     * @return $this
     */
    public function setHasPullRequests($has_pull_requests)
    {
        $this->container['has_pull_requests'] = $has_pull_requests;

        return $this;
    }

    /**
     * Gets has_wiki
     *
     * @return bool
     */
    public function getHasWiki()
    {
        return $this->container['has_wiki'];
    }

    /**
     * Sets has_wiki
     *
     * @param bool $has_wiki either `true` to enable the wiki for this repository or `false` to disable it.
     *
     * @return $this
     */
    public function setHasWiki($has_wiki)
    {
        $this->container['has_wiki'] = $has_wiki;

        return $this;
    }

    /**
     * Gets ignore_whitespace_conflicts
     *
     * @return bool
     */
    public function getIgnoreWhitespaceConflicts()
    {
        return $this->container['ignore_whitespace_conflicts'];
    }

    /**
     * Sets ignore_whitespace_conflicts
     *
     * @param bool $ignore_whitespace_conflicts either `true` to ignore whitespace for conflicts, or `false` to not ignore whitespace.
     *
     * @return $this
     */
    public function setIgnoreWhitespaceConflicts($ignore_whitespace_conflicts)
    {
        $this->container['ignore_whitespace_conflicts'] = $ignore_whitespace_conflicts;

        return $this;
    }

    /**
     * Gets internal_tracker
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\InternalTracker
     */
    public function getInternalTracker()
    {
        return $this->container['internal_tracker'];
    }

    /**
     * Sets internal_tracker
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\InternalTracker $internal_tracker internal_tracker
     *
     * @return $this
     */
    public function setInternalTracker($internal_tracker)
    {
        $this->container['internal_tracker'] = $internal_tracker;

        return $this;
    }

    /**
     * Gets mirror_interval
     *
     * @return string
     */
    public function getMirrorInterval()
    {
        return $this->container['mirror_interval'];
    }

    /**
     * Sets mirror_interval
     *
     * @param string $mirror_interval set to a string like `8h30m0s` to set the mirror interval time
     *
     * @return $this
     */
    public function setMirrorInterval($mirror_interval)
    {
        $this->container['mirror_interval'] = $mirror_interval;

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
     * @param string $name name of the repository
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets private
     *
     * @return bool
     */
    public function getPrivate()
    {
        return $this->container['private'];
    }

    /**
     * Sets private
     *
     * @param bool $private either `true` to make the repository private or `false` to make it public. Note: you will get a 422 error if the organization restricts changing repository visibility to organization owners and a non-owner tries to change the value of private.
     *
     * @return $this
     */
    public function setPrivate($private)
    {
        $this->container['private'] = $private;

        return $this;
    }

    /**
     * Gets template
     *
     * @return bool
     */
    public function getTemplate()
    {
        return $this->container['template'];
    }

    /**
     * Sets template
     *
     * @param bool $template either `true` to make this repository a template or `false` to make it a normal repository
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->container['template'] = $template;

        return $this;
    }

    /**
     * Gets website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->container['website'];
    }

    /**
     * Sets website
     *
     * @param string $website a URL with more information about the repository.
     *
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->container['website'] = $website;

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


