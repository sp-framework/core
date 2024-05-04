<?php
/**
 * ChecksCreateRequest
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
 * ChecksCreateRequest Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class ChecksCreateRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'checks_create_request';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'name' => 'string',
        'head_sha' => 'string',
        'details_url' => 'string',
        'external_id' => 'string',
        'status' => 'string',
        'started_at' => '\DateTime',
        'conclusion' => 'string',
        'completed_at' => '\DateTime',
        'output' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ChecksCreateRequestOutput',
        'actions' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ChecksCreateRequestActionsInner[]'
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
        'head_sha' => null,
        'details_url' => null,
        'external_id' => null,
        'status' => null,
        'started_at' => 'date-time',
        'conclusion' => null,
        'completed_at' => 'date-time',
        'output' => null,
        'actions' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'name' => false,
        'head_sha' => false,
        'details_url' => false,
        'external_id' => false,
        'status' => false,
        'started_at' => false,
        'conclusion' => false,
        'completed_at' => false,
        'output' => false,
        'actions' => false
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
        'head_sha' => 'head_sha',
        'details_url' => 'details_url',
        'external_id' => 'external_id',
        'status' => 'status',
        'started_at' => 'started_at',
        'conclusion' => 'conclusion',
        'completed_at' => 'completed_at',
        'output' => 'output',
        'actions' => 'actions'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'name' => 'setName',
        'head_sha' => 'setHeadSha',
        'details_url' => 'setDetailsUrl',
        'external_id' => 'setExternalId',
        'status' => 'setStatus',
        'started_at' => 'setStartedAt',
        'conclusion' => 'setConclusion',
        'completed_at' => 'setCompletedAt',
        'output' => 'setOutput',
        'actions' => 'setActions'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'name' => 'getName',
        'head_sha' => 'getHeadSha',
        'details_url' => 'getDetailsUrl',
        'external_id' => 'getExternalId',
        'status' => 'getStatus',
        'started_at' => 'getStartedAt',
        'conclusion' => 'getConclusion',
        'completed_at' => 'getCompletedAt',
        'output' => 'getOutput',
        'actions' => 'getActions'
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
    public const CONCLUSION_ACTION_REQUIRED = 'action_required';
    public const CONCLUSION_CANCELLED = 'cancelled';
    public const CONCLUSION_FAILURE = 'failure';
    public const CONCLUSION_NEUTRAL = 'neutral';
    public const CONCLUSION_SUCCESS = 'success';
    public const CONCLUSION_SKIPPED = 'skipped';
    public const CONCLUSION_STALE = 'stale';
    public const CONCLUSION_TIMED_OUT = 'timed_out';

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
            self::CONCLUSION_ACTION_REQUIRED,
            self::CONCLUSION_CANCELLED,
            self::CONCLUSION_FAILURE,
            self::CONCLUSION_NEUTRAL,
            self::CONCLUSION_SUCCESS,
            self::CONCLUSION_SKIPPED,
            self::CONCLUSION_STALE,
            self::CONCLUSION_TIMED_OUT,
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
        $this->setIfExists('head_sha', $data ?? [], null);
        $this->setIfExists('details_url', $data ?? [], null);
        $this->setIfExists('external_id', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], 'queued');
        $this->setIfExists('started_at', $data ?? [], null);
        $this->setIfExists('conclusion', $data ?? [], null);
        $this->setIfExists('completed_at', $data ?? [], null);
        $this->setIfExists('output', $data ?? [], null);
        $this->setIfExists('actions', $data ?? [], null);
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
        if ($this->container['head_sha'] === null) {
            $invalidProperties[] = "'head_sha' can't be null";
        }
        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container['status'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getConclusionAllowableValues();
        if (!is_null($this->container['conclusion']) && !in_array($this->container['conclusion'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'conclusion', must be one of '%s'",
                $this->container['conclusion'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['actions']) && (count($this->container['actions']) > 3)) {
            $invalidProperties[] = "invalid value for 'actions', number of items must be less than or equal to 3.";
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
     * @param string $name The name of the check. For example, \"code-coverage\".
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
     * @param string $head_sha The SHA of the commit.
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
     * Gets details_url
     *
     * @return string|null
     */
    public function getDetailsUrl()
    {
        return $this->container['details_url'];
    }

    /**
     * Sets details_url
     *
     * @param string|null $details_url The URL of the integrator's site that has the full details of the check. If the integrator does not provide this, then the homepage of the GitHub app is used.
     *
     * @return self
     */
    public function setDetailsUrl($details_url)
    {
        if (is_null($details_url)) {
            throw new \InvalidArgumentException('non-nullable details_url cannot be null');
        }
        $this->container['details_url'] = $details_url;

        return $this;
    }

    /**
     * Gets external_id
     *
     * @return string|null
     */
    public function getExternalId()
    {
        return $this->container['external_id'];
    }

    /**
     * Sets external_id
     *
     * @param string|null $external_id A reference for the run on the integrator's system.
     *
     * @return self
     */
    public function setExternalId($external_id)
    {
        if (is_null($external_id)) {
            throw new \InvalidArgumentException('non-nullable external_id cannot be null');
        }
        $this->container['external_id'] = $external_id;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string|null $status The current status of the check run. Only GitHub Actions can set a status of `waiting`, `pending`, or `requested`.
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
     * Gets started_at
     *
     * @return \DateTime|null
     */
    public function getStartedAt()
    {
        return $this->container['started_at'];
    }

    /**
     * Sets started_at
     *
     * @param \DateTime|null $started_at The time that the check run began. This is a timestamp in [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setStartedAt($started_at)
    {
        if (is_null($started_at)) {
            throw new \InvalidArgumentException('non-nullable started_at cannot be null');
        }
        $this->container['started_at'] = $started_at;

        return $this;
    }

    /**
     * Gets conclusion
     *
     * @return string|null
     */
    public function getConclusion()
    {
        return $this->container['conclusion'];
    }

    /**
     * Sets conclusion
     *
     * @param string|null $conclusion **Required if you provide `completed_at` or a `status` of `completed`**. The final conclusion of the check.  **Note:** Providing `conclusion` will automatically set the `status` parameter to `completed`. You cannot change a check run conclusion to `stale`, only GitHub can set this.
     *
     * @return self
     */
    public function setConclusion($conclusion)
    {
        if (is_null($conclusion)) {
            throw new \InvalidArgumentException('non-nullable conclusion cannot be null');
        }
        $allowedValues = $this->getConclusionAllowableValues();
        if (!in_array($conclusion, $allowedValues, true)) {
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
     * Gets completed_at
     *
     * @return \DateTime|null
     */
    public function getCompletedAt()
    {
        return $this->container['completed_at'];
    }

    /**
     * Sets completed_at
     *
     * @param \DateTime|null $completed_at The time the check completed. This is a timestamp in [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setCompletedAt($completed_at)
    {
        if (is_null($completed_at)) {
            throw new \InvalidArgumentException('non-nullable completed_at cannot be null');
        }
        $this->container['completed_at'] = $completed_at;

        return $this;
    }

    /**
     * Gets output
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ChecksCreateRequestOutput|null
     */
    public function getOutput()
    {
        return $this->container['output'];
    }

    /**
     * Sets output
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ChecksCreateRequestOutput|null $output output
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
     * Gets actions
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ChecksCreateRequestActionsInner[]|null
     */
    public function getActions()
    {
        return $this->container['actions'];
    }

    /**
     * Sets actions
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ChecksCreateRequestActionsInner[]|null $actions Displays a button on GitHub that can be clicked to alert your app to do additional tasks. For example, a code linting app can display a button that automatically fixes detected errors. The button created in this object is displayed after the check run completes. When a user clicks the button, GitHub sends the [`check_run.requested_action` webhook](https://docs.github.com/enterprise-server@3.12/webhooks/event-payloads/#check_run) to your app. Each action includes a `label`, `identifier` and `description`. A maximum of three actions are accepted. To learn more about check runs and requested actions, see \"[Check runs and requested actions](https://docs.github.com/enterprise-server@3.12/rest/guides/using-the-rest-api-to-interact-with-checks#check-runs-and-requested-actions).\"
     *
     * @return self
     */
    public function setActions($actions)
    {
        if (is_null($actions)) {
            throw new \InvalidArgumentException('non-nullable actions cannot be null');
        }

        if ((count($actions) > 3)) {
            throw new \InvalidArgumentException('invalid value for $actions when calling ChecksCreateRequest., number of items must be less than or equal to 3.');
        }
        $this->container['actions'] = $actions;

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


