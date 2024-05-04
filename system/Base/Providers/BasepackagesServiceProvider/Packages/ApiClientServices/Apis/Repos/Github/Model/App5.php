<?php
/**
 * App5
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
 * App5 Class Doc Comment
 *
 * @category Class
 * @description GitHub apps are a new way to extend GitHub. They can be installed directly on organizations and user accounts and granted access to specific repositories. They come with granular permissions and built-in webhooks. GitHub apps are first class actors within GitHub.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class App5 implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'App_5';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'created_at' => '\DateTime',
        'description' => 'string',
        'events' => 'string[]',
        'external_url' => 'string',
        'html_url' => 'string',
        'id' => 'int',
        'name' => 'string',
        'node_id' => 'string',
        'owner' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User',
        'permissions' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App1Permissions',
        'slug' => 'string',
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
        'created_at' => 'date-time',
        'description' => null,
        'events' => null,
        'external_url' => 'uri',
        'html_url' => 'uri',
        'id' => null,
        'name' => null,
        'node_id' => null,
        'owner' => null,
        'permissions' => null,
        'slug' => null,
        'updated_at' => 'date-time'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'created_at' => true,
        'description' => true,
        'events' => false,
        'external_url' => true,
        'html_url' => false,
        'id' => true,
        'name' => false,
        'node_id' => false,
        'owner' => true,
        'permissions' => false,
        'slug' => false,
        'updated_at' => true
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
        'created_at' => 'created_at',
        'description' => 'description',
        'events' => 'events',
        'external_url' => 'external_url',
        'html_url' => 'html_url',
        'id' => 'id',
        'name' => 'name',
        'node_id' => 'node_id',
        'owner' => 'owner',
        'permissions' => 'permissions',
        'slug' => 'slug',
        'updated_at' => 'updated_at'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'created_at' => 'setCreatedAt',
        'description' => 'setDescription',
        'events' => 'setEvents',
        'external_url' => 'setExternalUrl',
        'html_url' => 'setHtmlUrl',
        'id' => 'setId',
        'name' => 'setName',
        'node_id' => 'setNodeId',
        'owner' => 'setOwner',
        'permissions' => 'setPermissions',
        'slug' => 'setSlug',
        'updated_at' => 'setUpdatedAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'created_at' => 'getCreatedAt',
        'description' => 'getDescription',
        'events' => 'getEvents',
        'external_url' => 'getExternalUrl',
        'html_url' => 'getHtmlUrl',
        'id' => 'getId',
        'name' => 'getName',
        'node_id' => 'getNodeId',
        'owner' => 'getOwner',
        'permissions' => 'getPermissions',
        'slug' => 'getSlug',
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

    public const EVENTS_BRANCH_PROTECTION_RULE = 'branch_protection_rule';
    public const EVENTS_CHECK_RUN = 'check_run';
    public const EVENTS_CHECK_SUITE = 'check_suite';
    public const EVENTS_CODE_SCANNING_ALERT = 'code_scanning_alert';
    public const EVENTS_COMMIT_COMMENT = 'commit_comment';
    public const EVENTS_CONTENT_REFERENCE = 'content_reference';
    public const EVENTS_CREATE = 'create';
    public const EVENTS_DELETE = 'delete';
    public const EVENTS_DEPLOYMENT = 'deployment';
    public const EVENTS_DEPLOYMENT_REVIEW = 'deployment_review';
    public const EVENTS_DEPLOYMENT_STATUS = 'deployment_status';
    public const EVENTS_DEPLOY_KEY = 'deploy_key';
    public const EVENTS_DISCUSSION = 'discussion';
    public const EVENTS_DISCUSSION_COMMENT = 'discussion_comment';
    public const EVENTS_FORK = 'fork';
    public const EVENTS_GOLLUM = 'gollum';
    public const EVENTS_ISSUES = 'issues';
    public const EVENTS_ISSUE_COMMENT = 'issue_comment';
    public const EVENTS_LABEL = 'label';
    public const EVENTS_MEMBER = 'member';
    public const EVENTS_MEMBERSHIP = 'membership';
    public const EVENTS_MILESTONE = 'milestone';
    public const EVENTS_ORGANIZATION = 'organization';
    public const EVENTS_ORG_BLOCK = 'org_block';
    public const EVENTS_PAGE_BUILD = 'page_build';
    public const EVENTS_PROJECT = 'project';
    public const EVENTS_PROJECT_CARD = 'project_card';
    public const EVENTS_PROJECT_COLUMN = 'project_column';
    public const EVENTS__PUBLIC = 'public';
    public const EVENTS_PULL_REQUEST = 'pull_request';
    public const EVENTS_PULL_REQUEST_REVIEW = 'pull_request_review';
    public const EVENTS_PULL_REQUEST_REVIEW_COMMENT = 'pull_request_review_comment';
    public const EVENTS_PUSH = 'push';
    public const EVENTS_REGISTRY_PACKAGE = 'registry_package';
    public const EVENTS_RELEASE = 'release';
    public const EVENTS_REPOSITORY = 'repository';
    public const EVENTS_REPOSITORY_DISPATCH = 'repository_dispatch';
    public const EVENTS_SECRET_SCANNING_ALERT = 'secret_scanning_alert';
    public const EVENTS_STAR = 'star';
    public const EVENTS_STATUS = 'status';
    public const EVENTS_TEAM = 'team';
    public const EVENTS_TEAM_ADD = 'team_add';
    public const EVENTS_WATCH = 'watch';
    public const EVENTS_WORKFLOW_DISPATCH = 'workflow_dispatch';
    public const EVENTS_WORKFLOW_RUN = 'workflow_run';
    public const EVENTS_WORKFLOW_JOB = 'workflow_job';
    public const EVENTS_PULL_REQUEST_REVIEW_THREAD = 'pull_request_review_thread';
    public const EVENTS_MERGE_QUEUE_ENTRY = 'merge_queue_entry';
    public const EVENTS_SECRET_SCANNING_ALERT_LOCATION = 'secret_scanning_alert_location';
    public const EVENTS_MERGE_GROUP = 'merge_group';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getEventsAllowableValues()
    {
        return [
            self::EVENTS_BRANCH_PROTECTION_RULE,
            self::EVENTS_CHECK_RUN,
            self::EVENTS_CHECK_SUITE,
            self::EVENTS_CODE_SCANNING_ALERT,
            self::EVENTS_COMMIT_COMMENT,
            self::EVENTS_CONTENT_REFERENCE,
            self::EVENTS_CREATE,
            self::EVENTS_DELETE,
            self::EVENTS_DEPLOYMENT,
            self::EVENTS_DEPLOYMENT_REVIEW,
            self::EVENTS_DEPLOYMENT_STATUS,
            self::EVENTS_DEPLOY_KEY,
            self::EVENTS_DISCUSSION,
            self::EVENTS_DISCUSSION_COMMENT,
            self::EVENTS_FORK,
            self::EVENTS_GOLLUM,
            self::EVENTS_ISSUES,
            self::EVENTS_ISSUE_COMMENT,
            self::EVENTS_LABEL,
            self::EVENTS_MEMBER,
            self::EVENTS_MEMBERSHIP,
            self::EVENTS_MILESTONE,
            self::EVENTS_ORGANIZATION,
            self::EVENTS_ORG_BLOCK,
            self::EVENTS_PAGE_BUILD,
            self::EVENTS_PROJECT,
            self::EVENTS_PROJECT_CARD,
            self::EVENTS_PROJECT_COLUMN,
            self::EVENTS__PUBLIC,
            self::EVENTS_PULL_REQUEST,
            self::EVENTS_PULL_REQUEST_REVIEW,
            self::EVENTS_PULL_REQUEST_REVIEW_COMMENT,
            self::EVENTS_PUSH,
            self::EVENTS_REGISTRY_PACKAGE,
            self::EVENTS_RELEASE,
            self::EVENTS_REPOSITORY,
            self::EVENTS_REPOSITORY_DISPATCH,
            self::EVENTS_SECRET_SCANNING_ALERT,
            self::EVENTS_STAR,
            self::EVENTS_STATUS,
            self::EVENTS_TEAM,
            self::EVENTS_TEAM_ADD,
            self::EVENTS_WATCH,
            self::EVENTS_WORKFLOW_DISPATCH,
            self::EVENTS_WORKFLOW_RUN,
            self::EVENTS_WORKFLOW_JOB,
            self::EVENTS_PULL_REQUEST_REVIEW_THREAD,
            self::EVENTS_MERGE_QUEUE_ENTRY,
            self::EVENTS_SECRET_SCANNING_ALERT_LOCATION,
            self::EVENTS_MERGE_GROUP,
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
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('events', $data ?? [], null);
        $this->setIfExists('external_url', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('owner', $data ?? [], null);
        $this->setIfExists('permissions', $data ?? [], null);
        $this->setIfExists('slug', $data ?? [], null);
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

        if ($this->container['created_at'] === null) {
            $invalidProperties[] = "'created_at' can't be null";
        }
        if ($this->container['description'] === null) {
            $invalidProperties[] = "'description' can't be null";
        }
        if ($this->container['external_url'] === null) {
            $invalidProperties[] = "'external_url' can't be null";
        }
        if ($this->container['html_url'] === null) {
            $invalidProperties[] = "'html_url' can't be null";
        }
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        if ($this->container['node_id'] === null) {
            $invalidProperties[] = "'node_id' can't be null";
        }
        if ($this->container['owner'] === null) {
            $invalidProperties[] = "'owner' can't be null";
        }
        if ($this->container['updated_at'] === null) {
            $invalidProperties[] = "'updated_at' can't be null";
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
            array_push($this->openAPINullablesSetToNull, 'created_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('created_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['created_at'] = $created_at;

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
     * @param string $description description
     *
     * @return self
     */
    public function setDescription($description)
    {
        if (is_null($description)) {
            array_push($this->openAPINullablesSetToNull, 'description');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('description', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets events
     *
     * @return string[]|null
     */
    public function getEvents()
    {
        return $this->container['events'];
    }

    /**
     * Sets events
     *
     * @param string[]|null $events The list of events for the GitHub app
     *
     * @return self
     */
    public function setEvents($events)
    {
        if (is_null($events)) {
            throw new \InvalidArgumentException('non-nullable events cannot be null');
        }
        $allowedValues = $this->getEventsAllowableValues();
        if (array_diff($events, $allowedValues)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'events', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['events'] = $events;

        return $this;
    }

    /**
     * Gets external_url
     *
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->container['external_url'];
    }

    /**
     * Sets external_url
     *
     * @param string $external_url external_url
     *
     * @return self
     */
    public function setExternalUrl($external_url)
    {
        if (is_null($external_url)) {
            array_push($this->openAPINullablesSetToNull, 'external_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('external_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['external_url'] = $external_url;

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
            throw new \InvalidArgumentException('non-nullable html_url cannot be null');
        }
        $this->container['html_url'] = $html_url;

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
     * @param int $id Unique identifier of the GitHub app
     *
     * @return self
     */
    public function setId($id)
    {
        if (is_null($id)) {
            array_push($this->openAPINullablesSetToNull, 'id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['id'] = $id;

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
     * @param string $name The name of the GitHub app
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
     * Gets owner
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User
     */
    public function getOwner()
    {
        return $this->container['owner'];
    }

    /**
     * Sets owner
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User $owner owner
     *
     * @return self
     */
    public function setOwner($owner)
    {
        if (is_null($owner)) {
            array_push($this->openAPINullablesSetToNull, 'owner');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('owner', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['owner'] = $owner;

        return $this;
    }

    /**
     * Gets permissions
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App1Permissions|null
     */
    public function getPermissions()
    {
        return $this->container['permissions'];
    }

    /**
     * Sets permissions
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App1Permissions|null $permissions permissions
     *
     * @return self
     */
    public function setPermissions($permissions)
    {
        if (is_null($permissions)) {
            throw new \InvalidArgumentException('non-nullable permissions cannot be null');
        }
        $this->container['permissions'] = $permissions;

        return $this;
    }

    /**
     * Gets slug
     *
     * @return string|null
     */
    public function getSlug()
    {
        return $this->container['slug'];
    }

    /**
     * Sets slug
     *
     * @param string|null $slug The slug name of the GitHub app
     *
     * @return self
     */
    public function setSlug($slug)
    {
        if (is_null($slug)) {
            throw new \InvalidArgumentException('non-nullable slug cannot be null');
        }
        $this->container['slug'] = $slug;

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
            array_push($this->openAPINullablesSetToNull, 'updated_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('updated_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
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


