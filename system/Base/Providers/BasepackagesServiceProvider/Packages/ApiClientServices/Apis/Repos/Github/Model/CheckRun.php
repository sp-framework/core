<?php
/**
 * CheckRun
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
 * CheckRun Class Doc Comment
 *
 * @category Class
 * @description A check performed on the code of a given code change
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class CheckRun implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'check-run';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'head_sha' => 'string',
        'node_id' => 'string',
        'external_id' => 'string',
        'url' => 'string',
        'html_url' => 'string',
        'details_url' => 'string',
        'status' => 'string',
        'conclusion' => 'string',
        'started_at' => '\DateTime',
        'completed_at' => '\DateTime',
        'output' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunOutput',
        'name' => 'string',
        'check_suite' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunCheckSuite',
        'app' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableIntegration',
        'pull_requests' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\PullRequestMinimal[]',
        'deployment' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DeploymentSimple'
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
        'head_sha' => null,
        'node_id' => null,
        'external_id' => null,
        'url' => null,
        'html_url' => null,
        'details_url' => null,
        'status' => null,
        'conclusion' => null,
        'started_at' => 'date-time',
        'completed_at' => 'date-time',
        'output' => null,
        'name' => null,
        'check_suite' => null,
        'app' => null,
        'pull_requests' => null,
        'deployment' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'head_sha' => false,
        'node_id' => false,
        'external_id' => true,
        'url' => false,
        'html_url' => true,
        'details_url' => true,
        'status' => false,
        'conclusion' => true,
        'started_at' => true,
        'completed_at' => true,
        'output' => false,
        'name' => false,
        'check_suite' => true,
        'app' => true,
        'pull_requests' => false,
        'deployment' => false
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
        'head_sha' => 'head_sha',
        'node_id' => 'node_id',
        'external_id' => 'external_id',
        'url' => 'url',
        'html_url' => 'html_url',
        'details_url' => 'details_url',
        'status' => 'status',
        'conclusion' => 'conclusion',
        'started_at' => 'started_at',
        'completed_at' => 'completed_at',
        'output' => 'output',
        'name' => 'name',
        'check_suite' => 'check_suite',
        'app' => 'app',
        'pull_requests' => 'pull_requests',
        'deployment' => 'deployment'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'head_sha' => 'setHeadSha',
        'node_id' => 'setNodeId',
        'external_id' => 'setExternalId',
        'url' => 'setUrl',
        'html_url' => 'setHtmlUrl',
        'details_url' => 'setDetailsUrl',
        'status' => 'setStatus',
        'conclusion' => 'setConclusion',
        'started_at' => 'setStartedAt',
        'completed_at' => 'setCompletedAt',
        'output' => 'setOutput',
        'name' => 'setName',
        'check_suite' => 'setCheckSuite',
        'app' => 'setApp',
        'pull_requests' => 'setPullRequests',
        'deployment' => 'setDeployment'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'head_sha' => 'getHeadSha',
        'node_id' => 'getNodeId',
        'external_id' => 'getExternalId',
        'url' => 'getUrl',
        'html_url' => 'getHtmlUrl',
        'details_url' => 'getDetailsUrl',
        'status' => 'getStatus',
        'conclusion' => 'getConclusion',
        'started_at' => 'getStartedAt',
        'completed_at' => 'getCompletedAt',
        'output' => 'getOutput',
        'name' => 'getName',
        'check_suite' => 'getCheckSuite',
        'app' => 'getApp',
        'pull_requests' => 'getPullRequests',
        'deployment' => 'getDeployment'
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

    public const STATUS_QUEUED = 'queued';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_REQUESTED = 'requested';
    public const STATUS_PENDING = 'pending';
    public const CONCLUSION_SUCCESS = 'success';
    public const CONCLUSION_FAILURE = 'failure';
    public const CONCLUSION_NEUTRAL = 'neutral';
    public const CONCLUSION_CANCELLED = 'cancelled';
    public const CONCLUSION_SKIPPED = 'skipped';
    public const CONCLUSION_TIMED_OUT = 'timed_out';
    public const CONCLUSION_ACTION_REQUIRED = 'action_required';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getStatusAllowableValues()
    {
        return [
            self::STATUS_QUEUED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_WAITING,
            self::STATUS_REQUESTED,
            self::STATUS_PENDING,
        ];
    }

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
            self::CONCLUSION_SKIPPED,
            self::CONCLUSION_TIMED_OUT,
            self::CONCLUSION_ACTION_REQUIRED,
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
        $this->setIfExists('head_sha', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('external_id', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('details_url', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('conclusion', $data ?? [], null);
        $this->setIfExists('started_at', $data ?? [], null);
        $this->setIfExists('completed_at', $data ?? [], null);
        $this->setIfExists('output', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('check_suite', $data ?? [], null);
        $this->setIfExists('app', $data ?? [], null);
        $this->setIfExists('pull_requests', $data ?? [], null);
        $this->setIfExists('deployment', $data ?? [], null);
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
        if ($this->container['head_sha'] === null) {
            $invalidProperties[] = "'head_sha' can't be null";
        }
        if ($this->container['node_id'] === null) {
            $invalidProperties[] = "'node_id' can't be null";
        }
        if ($this->container['external_id'] === null) {
            $invalidProperties[] = "'external_id' can't be null";
        }
        if ($this->container['url'] === null) {
            $invalidProperties[] = "'url' can't be null";
        }
        if ($this->container['html_url'] === null) {
            $invalidProperties[] = "'html_url' can't be null";
        }
        if ($this->container['details_url'] === null) {
            $invalidProperties[] = "'details_url' can't be null";
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

        if ($this->container['started_at'] === null) {
            $invalidProperties[] = "'started_at' can't be null";
        }
        if ($this->container['completed_at'] === null) {
            $invalidProperties[] = "'completed_at' can't be null";
        }
        if ($this->container['output'] === null) {
            $invalidProperties[] = "'output' can't be null";
        }
        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        if ($this->container['check_suite'] === null) {
            $invalidProperties[] = "'check_suite' can't be null";
        }
        if ($this->container['app'] === null) {
            $invalidProperties[] = "'app' can't be null";
        }
        if ($this->container['pull_requests'] === null) {
            $invalidProperties[] = "'pull_requests' can't be null";
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
     * @param int $id The id of the check.
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
     * @param string $head_sha The SHA of the commit that is being checked.
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
     * Gets external_id
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->container['external_id'];
    }

    /**
     * Sets external_id
     *
     * @param string $external_id external_id
     *
     * @return self
     */
    public function setExternalId($external_id)
    {
        if (is_null($external_id)) {
            array_push($this->openAPINullablesSetToNull, 'external_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('external_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['external_id'] = $external_id;

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
     * Gets html_url
     *
     * @return string
     */
    public function getHtmlUrl()
    {
        return $this->container['html_url'];
    }

    /**
     * Sets html_url
     *
     * @param string $html_url html_url
     *
     * @return self
     */
    public function setHtmlUrl($html_url)
    {
        if (is_null($html_url)) {
            array_push($this->openAPINullablesSetToNull, 'html_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('html_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['html_url'] = $html_url;

        return $this;
    }

    /**
     * Gets details_url
     *
     * @return string
     */
    public function getDetailsUrl()
    {
        return $this->container['details_url'];
    }

    /**
     * Sets details_url
     *
     * @param string $details_url details_url
     *
     * @return self
     */
    public function setDetailsUrl($details_url)
    {
        if (is_null($details_url)) {
            array_push($this->openAPINullablesSetToNull, 'details_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('details_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['details_url'] = $details_url;

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
     * @param string $status The phase of the lifecycle that the check is currently in. Statuses of waiting, requested, and pending are reserved for GitHub Actions check runs.
     *
     * @return self
     */
    public function setStatus($status)
    {
        if (is_null($status)) {
            throw new \InvalidArgumentException('non-nullable status cannot be null');
        }
        $allowedValues = $this->getStatusAllowableValues();
        if (!in_array($status, $allowedValues, true)) {
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
     * @param string $conclusion conclusion
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
     * Gets started_at
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->container['started_at'];
    }

    /**
     * Sets started_at
     *
     * @param \DateTime $started_at started_at
     *
     * @return self
     */
    public function setStartedAt($started_at)
    {
        if (is_null($started_at)) {
            array_push($this->openAPINullablesSetToNull, 'started_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('started_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['started_at'] = $started_at;

        return $this;
    }

    /**
     * Gets completed_at
     *
     * @return \DateTime
     */
    public function getCompletedAt()
    {
        return $this->container['completed_at'];
    }

    /**
     * Sets completed_at
     *
     * @param \DateTime $completed_at completed_at
     *
     * @return self
     */
    public function setCompletedAt($completed_at)
    {
        if (is_null($completed_at)) {
            array_push($this->openAPINullablesSetToNull, 'completed_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('completed_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['completed_at'] = $completed_at;

        return $this;
    }

    /**
     * Gets output
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunOutput
     */
    public function getOutput()
    {
        return $this->container['output'];
    }

    /**
     * Sets output
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunOutput $output output
     *
     * @return self
     */
    public function setOutput($output)
    {
        if (is_null($output)) {
            throw new \InvalidArgumentException('non-nullable output cannot be null');
        }
        $this->container['output'] = $output;

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
     * @param string $name The name of the check.
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
     * Gets check_suite
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunCheckSuite
     */
    public function getCheckSuite()
    {
        return $this->container['check_suite'];
    }

    /**
     * Sets check_suite
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CheckRunCheckSuite $check_suite check_suite
     *
     * @return self
     */
    public function setCheckSuite($check_suite)
    {
        if (is_null($check_suite)) {
            array_push($this->openAPINullablesSetToNull, 'check_suite');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('check_suite', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['check_suite'] = $check_suite;

        return $this;
    }

    /**
     * Gets app
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableIntegration
     */
    public function getApp()
    {
        return $this->container['app'];
    }

    /**
     * Sets app
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\NullableIntegration $app app
     *
     * @return self
     */
    public function setApp($app)
    {
        if (is_null($app)) {
            array_push($this->openAPINullablesSetToNull, 'app');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('app', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['app'] = $app;

        return $this;
    }

    /**
     * Gets pull_requests
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\PullRequestMinimal[]
     */
    public function getPullRequests()
    {
        return $this->container['pull_requests'];
    }

    /**
     * Sets pull_requests
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\PullRequestMinimal[] $pull_requests Pull requests that are open with a `head_sha` or `head_branch` that matches the check. The returned pull requests do not necessarily indicate pull requests that triggered the check.
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
     * Gets deployment
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DeploymentSimple|null
     */
    public function getDeployment()
    {
        return $this->container['deployment'];
    }

    /**
     * Sets deployment
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DeploymentSimple|null $deployment deployment
     *
     * @return self
     */
    public function setDeployment($deployment)
    {
        if (is_null($deployment)) {
            throw new \InvalidArgumentException('non-nullable deployment cannot be null');
        }
        $this->container['deployment'] = $deployment;

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


