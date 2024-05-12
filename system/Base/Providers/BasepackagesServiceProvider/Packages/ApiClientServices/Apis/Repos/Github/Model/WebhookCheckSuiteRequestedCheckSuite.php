<?php
/**
 * WebhookCheckSuiteRequestedCheckSuite
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
 * WebhookCheckSuiteRequestedCheckSuite Class Doc Comment
 *
 * @category Class
 * @description The [check_suite](https://docs.github.com/rest/checks/suites#get-a-check-suite).
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class WebhookCheckSuiteRequestedCheckSuite implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'webhook_check_suite_requested_check_suite';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'after' => 'string',
        'app' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App3',
        'before' => 'string',
        'check_runs_url' => 'string',
        'conclusion' => 'string',
        'created_at' => '\DateTime',
        'head_branch' => 'string',
        'head_commit' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleCommit',
        'head_sha' => 'string',
        'id' => 'int',
        'latest_check_runs_count' => 'int',
        'node_id' => 'string',
        'pull_requests' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunPullRequest[]',
        'rerequestable' => 'bool',
        'runs_rerequestable' => 'bool',
        'status' => 'string',
        'updated_at' => '\DateTime',
        'url' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'after' => null,
        'app' => null,
        'before' => null,
        'check_runs_url' => 'uri',
        'conclusion' => null,
        'created_at' => 'date-time',
        'head_branch' => null,
        'head_commit' => null,
        'head_sha' => null,
        'id' => null,
        'latest_check_runs_count' => null,
        'node_id' => null,
        'pull_requests' => null,
        'rerequestable' => null,
        'runs_rerequestable' => null,
        'status' => null,
        'updated_at' => 'date-time',
        'url' => 'uri'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'after' => true,
        'app' => false,
        'before' => true,
        'check_runs_url' => false,
        'conclusion' => true,
        'created_at' => false,
        'head_branch' => true,
        'head_commit' => false,
        'head_sha' => false,
        'id' => false,
        'latest_check_runs_count' => false,
        'node_id' => false,
        'pull_requests' => false,
        'rerequestable' => false,
        'runs_rerequestable' => false,
        'status' => true,
        'updated_at' => false,
        'url' => false
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
        'after' => 'after',
        'app' => 'app',
        'before' => 'before',
        'check_runs_url' => 'check_runs_url',
        'conclusion' => 'conclusion',
        'created_at' => 'created_at',
        'head_branch' => 'head_branch',
        'head_commit' => 'head_commit',
        'head_sha' => 'head_sha',
        'id' => 'id',
        'latest_check_runs_count' => 'latest_check_runs_count',
        'node_id' => 'node_id',
        'pull_requests' => 'pull_requests',
        'rerequestable' => 'rerequestable',
        'runs_rerequestable' => 'runs_rerequestable',
        'status' => 'status',
        'updated_at' => 'updated_at',
        'url' => 'url'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'after' => 'setAfter',
        'app' => 'setApp',
        'before' => 'setBefore',
        'check_runs_url' => 'setCheckRunsUrl',
        'conclusion' => 'setConclusion',
        'created_at' => 'setCreatedAt',
        'head_branch' => 'setHeadBranch',
        'head_commit' => 'setHeadCommit',
        'head_sha' => 'setHeadSha',
        'id' => 'setId',
        'latest_check_runs_count' => 'setLatestCheckRunsCount',
        'node_id' => 'setNodeId',
        'pull_requests' => 'setPullRequests',
        'rerequestable' => 'setRerequestable',
        'runs_rerequestable' => 'setRunsRerequestable',
        'status' => 'setStatus',
        'updated_at' => 'setUpdatedAt',
        'url' => 'setUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'after' => 'getAfter',
        'app' => 'getApp',
        'before' => 'getBefore',
        'check_runs_url' => 'getCheckRunsUrl',
        'conclusion' => 'getConclusion',
        'created_at' => 'getCreatedAt',
        'head_branch' => 'getHeadBranch',
        'head_commit' => 'getHeadCommit',
        'head_sha' => 'getHeadSha',
        'id' => 'getId',
        'latest_check_runs_count' => 'getLatestCheckRunsCount',
        'node_id' => 'getNodeId',
        'pull_requests' => 'getPullRequests',
        'rerequestable' => 'getRerequestable',
        'runs_rerequestable' => 'getRunsRerequestable',
        'status' => 'getStatus',
        'updated_at' => 'getUpdatedAt',
        'url' => 'getUrl'
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

    public const CONCLUSION_SUCCESS = 'success';
    public const CONCLUSION_FAILURE = 'failure';
    public const CONCLUSION_NEUTRAL = 'neutral';
    public const CONCLUSION_CANCELLED = 'cancelled';
    public const CONCLUSION_TIMED_OUT = 'timed_out';
    public const CONCLUSION_ACTION_REQUIRED = 'action_required';
    public const CONCLUSION_STALE = 'stale';
    public const CONCLUSION_NULL = 'null';
    public const CONCLUSION_SKIPPED = 'skipped';
    public const STATUS_REQUESTED = 'requested';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_NULL = 'null';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getConclusionAllowableValues()
    {
        return [
            self::CONCLUSION_SUCCESS,
            self::CONCLUSION_FAILURE,
            self::CONCLUSION_NEUTRAL,
            self::CONCLUSION_CANCELLED,
            self::CONCLUSION_TIMED_OUT,
            self::CONCLUSION_ACTION_REQUIRED,
            self::CONCLUSION_STALE,
            self::CONCLUSION_NULL,
            self::CONCLUSION_SKIPPED,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getStatusAllowableValues()
    {
        return [
            self::STATUS_REQUESTED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_QUEUED,
            self::STATUS_NULL,
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
        $this->setIfExists('after', $data ?? [], null);
        $this->setIfExists('app', $data ?? [], null);
        $this->setIfExists('before', $data ?? [], null);
        $this->setIfExists('check_runs_url', $data ?? [], null);
        $this->setIfExists('conclusion', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('head_branch', $data ?? [], null);
        $this->setIfExists('head_commit', $data ?? [], null);
        $this->setIfExists('head_sha', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('latest_check_runs_count', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('pull_requests', $data ?? [], null);
        $this->setIfExists('rerequestable', $data ?? [], null);
        $this->setIfExists('runs_rerequestable', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('updated_at', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
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

        if ($this->container['after'] === null) {
            $invalidProperties[] = "'after' can't be null";
        }
        if ($this->container['app'] === null) {
            $invalidProperties[] = "'app' can't be null";
        }
        if ($this->container['before'] === null) {
            $invalidProperties[] = "'before' can't be null";
        }
        if ($this->container['check_runs_url'] === null) {
            $invalidProperties[] = "'check_runs_url' can't be null";
        }
        if ($this->container['conclusion'] === null) {
            $invalidProperties[] = "'conclusion' can't be null";
        }
        $allowedValues = $this->getConclusionAllowableValues();
        if (!is_null($this->container['conclusion']) && !in_array($this->container['conclusion'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'conclusion', must be one of '%s'",
                $this->container['conclusion'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['created_at'] === null) {
            $invalidProperties[] = "'created_at' can't be null";
        }
        if ($this->container['head_branch'] === null) {
            $invalidProperties[] = "'head_branch' can't be null";
        }
        if ($this->container['head_commit'] === null) {
            $invalidProperties[] = "'head_commit' can't be null";
        }
        if ($this->container['head_sha'] === null) {
            $invalidProperties[] = "'head_sha' can't be null";
        }
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['latest_check_runs_count'] === null) {
            $invalidProperties[] = "'latest_check_runs_count' can't be null";
        }
        if ($this->container['node_id'] === null) {
            $invalidProperties[] = "'node_id' can't be null";
        }
        if ($this->container['pull_requests'] === null) {
            $invalidProperties[] = "'pull_requests' can't be null";
        }
        if ($this->container['status'] === null) {
            $invalidProperties[] = "'status' can't be null";
        }
        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container['status'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['updated_at'] === null) {
            $invalidProperties[] = "'updated_at' can't be null";
        }
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
     * Gets after
     *
     * @return string
     */
    public function getAfter()
    {
        return $this->container['after'];
    }

    /**
     * Sets after
     *
     * @param string $after after
     *
     * @return self
     */
    public function setAfter($after)
    {
        if (is_null($after)) {
            array_push($this->openAPINullablesSetToNull, 'after');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('after', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['after'] = $after;

        return $this;
    }

    /**
     * Gets app
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App3
     */
    public function getApp()
    {
        return $this->container['app'];
    }

    /**
     * Sets app
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App3 $app app
     *
     * @return self
     */
    public function setApp($app)
    {
        if (is_null($app)) {
            throw new \InvalidArgumentException('non-nullable app cannot be null');
        }
        $this->container['app'] = $app;

        return $this;
    }

    /**
     * Gets before
     *
     * @return string
     */
    public function getBefore()
    {
        return $this->container['before'];
    }

    /**
     * Sets before
     *
     * @param string $before before
     *
     * @return self
     */
    public function setBefore($before)
    {
        if (is_null($before)) {
            array_push($this->openAPINullablesSetToNull, 'before');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('before', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['before'] = $before;

        return $this;
    }

    /**
     * Gets check_runs_url
     *
     * @return string
     */
    public function getCheckRunsUrl()
    {
        return $this->container['check_runs_url'];
    }

    /**
     * Sets check_runs_url
     *
     * @param string $check_runs_url check_runs_url
     *
     * @return self
     */
    public function setCheckRunsUrl($check_runs_url)
    {
        if (is_null($check_runs_url)) {
            throw new \InvalidArgumentException('non-nullable check_runs_url cannot be null');
        }
        $this->container['check_runs_url'] = $check_runs_url;

        return $this;
    }

    /**
     * Gets conclusion
     *
     * @return string
     */
    public function getConclusion()
    {
        return $this->container['conclusion'];
    }

    /**
     * Sets conclusion
     *
     * @param string $conclusion The summary conclusion for all check runs that are part of the check suite. This value will be `null` until the check run has completed.
     *
     * @return self
     */
    public function setConclusion($conclusion)
    {
        if (is_null($conclusion)) {
            array_push($this->openAPINullablesSetToNull, 'conclusion');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('conclusion', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $allowedValues = $this->getConclusionAllowableValues();
        if (!is_null($conclusion) && !in_array($conclusion, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'conclusion', must be one of '%s'",
                    $conclusion,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['conclusion'] = $conclusion;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param \DateTime $created_at created_at
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
     * Gets head_branch
     *
     * @return string
     */
    public function getHeadBranch()
    {
        return $this->container['head_branch'];
    }

    /**
     * Sets head_branch
     *
     * @param string $head_branch The head branch name the changes are on.
     *
     * @return self
     */
    public function setHeadBranch($head_branch)
    {
        if (is_null($head_branch)) {
            array_push($this->openAPINullablesSetToNull, 'head_branch');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('head_branch', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['head_branch'] = $head_branch;

        return $this;
    }

    /**
     * Gets head_commit
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleCommit
     */
    public function getHeadCommit()
    {
        return $this->container['head_commit'];
    }

    /**
     * Sets head_commit
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleCommit $head_commit head_commit
     *
     * @return self
     */
    public function setHeadCommit($head_commit)
    {
        if (is_null($head_commit)) {
            throw new \InvalidArgumentException('non-nullable head_commit cannot be null');
        }
        $this->container['head_commit'] = $head_commit;

        return $this;
    }

    /**
     * Gets head_sha
     *
     * @return string
     */
    public function getHeadSha()
    {
        return $this->container['head_sha'];
    }

    /**
     * Sets head_sha
     *
     * @param string $head_sha The SHA of the head commit that is being checked.
     *
     * @return self
     */
    public function setHeadSha($head_sha)
    {
        if (is_null($head_sha)) {
            throw new \InvalidArgumentException('non-nullable head_sha cannot be null');
        }
        $this->container['head_sha'] = $head_sha;

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
     * Gets latest_check_runs_count
     *
     * @return int
     */
    public function getLatestCheckRunsCount()
    {
        return $this->container['latest_check_runs_count'];
    }

    /**
     * Sets latest_check_runs_count
     *
     * @param int $latest_check_runs_count latest_check_runs_count
     *
     * @return self
     */
    public function setLatestCheckRunsCount($latest_check_runs_count)
    {
        if (is_null($latest_check_runs_count)) {
            throw new \InvalidArgumentException('non-nullable latest_check_runs_count cannot be null');
        }
        $this->container['latest_check_runs_count'] = $latest_check_runs_count;

        return $this;
    }

    /**
     * Gets node_id
     *
     * @return string
     */
    public function getNodeId()
    {
        return $this->container['node_id'];
    }

    /**
     * Sets node_id
     *
     * @param string $node_id node_id
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
     * Gets pull_requests
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunPullRequest[]
     */
    public function getPullRequests()
    {
        return $this->container['pull_requests'];
    }

    /**
     * Sets pull_requests
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunPullRequest[] $pull_requests An array of pull requests that match this check suite. A pull request matches a check suite if they have the same `head_sha` and `head_branch`. When the check suite's `head_branch` is in a forked repository it will be `null` and the `pull_requests` array will be empty.
     *
     * @return self
     */
    public function setPullRequests($pull_requests)
    {
        if (is_null($pull_requests)) {
            throw new \InvalidArgumentException('non-nullable pull_requests cannot be null');
        }
        $this->container['pull_requests'] = $pull_requests;

        return $this;
    }

    /**
     * Gets rerequestable
     *
     * @return bool|null
     */
    public function getRerequestable()
    {
        return $this->container['rerequestable'];
    }

    /**
     * Sets rerequestable
     *
     * @param bool|null $rerequestable rerequestable
     *
     * @return self
     */
    public function setRerequestable($rerequestable)
    {
        if (is_null($rerequestable)) {
            throw new \InvalidArgumentException('non-nullable rerequestable cannot be null');
        }
        $this->container['rerequestable'] = $rerequestable;

        return $this;
    }

    /**
     * Gets runs_rerequestable
     *
     * @return bool|null
     */
    public function getRunsRerequestable()
    {
        return $this->container['runs_rerequestable'];
    }

    /**
     * Sets runs_rerequestable
     *
     * @param bool|null $runs_rerequestable runs_rerequestable
     *
     * @return self
     */
    public function setRunsRerequestable($runs_rerequestable)
    {
        if (is_null($runs_rerequestable)) {
            throw new \InvalidArgumentException('non-nullable runs_rerequestable cannot be null');
        }
        $this->container['runs_rerequestable'] = $runs_rerequestable;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string $status The summary status for all check runs that are part of the check suite. Can be `requested`, `in_progress`, or `completed`.
     *
     * @return self
     */
    public function setStatus($status)
    {
        if (is_null($status)) {
            array_push($this->openAPINullablesSetToNull, 'status');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('status', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($status) && !in_array($status, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'status', must be one of '%s'",
                    $status,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->container['updated_at'];
    }

    /**
     * Sets updated_at
     *
     * @param \DateTime $updated_at updated_at
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
     * @param string $url URL that points to the check suite API resource.
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


