<?php
/**
 * Deployment
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
 * Deployment Class Doc Comment
 *
 * @category Class
 * @description The [deployment](https://docs.github.com/rest/deployments/deployments#list-deployments).
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Deployment implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Deployment';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'created_at' => 'string',
        'creator' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User',
        'description' => 'string',
        'environment' => 'string',
        'id' => 'int',
        'node_id' => 'string',
        'original_environment' => 'string',
        'payload' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DeploymentPayload',
        'performed_via_github_app' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App5',
        'production_environment' => 'bool',
        'ref' => 'string',
        'repository_url' => 'string',
        'sha' => 'string',
        'statuses_url' => 'string',
        'task' => 'string',
        'transient_environment' => 'bool',
        'updated_at' => 'string',
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
        'created_at' => null,
        'creator' => null,
        'description' => null,
        'environment' => null,
        'id' => null,
        'node_id' => null,
        'original_environment' => null,
        'payload' => null,
        'performed_via_github_app' => null,
        'production_environment' => null,
        'ref' => null,
        'repository_url' => 'uri',
        'sha' => null,
        'statuses_url' => 'uri',
        'task' => null,
        'transient_environment' => null,
        'updated_at' => null,
        'url' => 'uri'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'created_at' => false,
        'creator' => true,
        'description' => true,
        'environment' => false,
        'id' => false,
        'node_id' => false,
        'original_environment' => false,
        'payload' => false,
        'performed_via_github_app' => true,
        'production_environment' => false,
        'ref' => false,
        'repository_url' => false,
        'sha' => false,
        'statuses_url' => false,
        'task' => false,
        'transient_environment' => false,
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
        'created_at' => 'created_at',
        'creator' => 'creator',
        'description' => 'description',
        'environment' => 'environment',
        'id' => 'id',
        'node_id' => 'node_id',
        'original_environment' => 'original_environment',
        'payload' => 'payload',
        'performed_via_github_app' => 'performed_via_github_app',
        'production_environment' => 'production_environment',
        'ref' => 'ref',
        'repository_url' => 'repository_url',
        'sha' => 'sha',
        'statuses_url' => 'statuses_url',
        'task' => 'task',
        'transient_environment' => 'transient_environment',
        'updated_at' => 'updated_at',
        'url' => 'url'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'created_at' => 'setCreatedAt',
        'creator' => 'setCreator',
        'description' => 'setDescription',
        'environment' => 'setEnvironment',
        'id' => 'setId',
        'node_id' => 'setNodeId',
        'original_environment' => 'setOriginalEnvironment',
        'payload' => 'setPayload',
        'performed_via_github_app' => 'setPerformedViaGithubApp',
        'production_environment' => 'setProductionEnvironment',
        'ref' => 'setRef',
        'repository_url' => 'setRepositoryUrl',
        'sha' => 'setSha',
        'statuses_url' => 'setStatusesUrl',
        'task' => 'setTask',
        'transient_environment' => 'setTransientEnvironment',
        'updated_at' => 'setUpdatedAt',
        'url' => 'setUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'created_at' => 'getCreatedAt',
        'creator' => 'getCreator',
        'description' => 'getDescription',
        'environment' => 'getEnvironment',
        'id' => 'getId',
        'node_id' => 'getNodeId',
        'original_environment' => 'getOriginalEnvironment',
        'payload' => 'getPayload',
        'performed_via_github_app' => 'getPerformedViaGithubApp',
        'production_environment' => 'getProductionEnvironment',
        'ref' => 'getRef',
        'repository_url' => 'getRepositoryUrl',
        'sha' => 'getSha',
        'statuses_url' => 'getStatusesUrl',
        'task' => 'getTask',
        'transient_environment' => 'getTransientEnvironment',
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
        $this->setIfExists('creator', $data ?? [], null);
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('environment', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('original_environment', $data ?? [], null);
        $this->setIfExists('payload', $data ?? [], null);
        $this->setIfExists('performed_via_github_app', $data ?? [], null);
        $this->setIfExists('production_environment', $data ?? [], null);
        $this->setIfExists('ref', $data ?? [], null);
        $this->setIfExists('repository_url', $data ?? [], null);
        $this->setIfExists('sha', $data ?? [], null);
        $this->setIfExists('statuses_url', $data ?? [], null);
        $this->setIfExists('task', $data ?? [], null);
        $this->setIfExists('transient_environment', $data ?? [], null);
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

        if ($this->container['created_at'] === null) {
            $invalidProperties[] = "'created_at' can't be null";
        }
        if ($this->container['creator'] === null) {
            $invalidProperties[] = "'creator' can't be null";
        }
        if ($this->container['description'] === null) {
            $invalidProperties[] = "'description' can't be null";
        }
        if ($this->container['environment'] === null) {
            $invalidProperties[] = "'environment' can't be null";
        }
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['node_id'] === null) {
            $invalidProperties[] = "'node_id' can't be null";
        }
        if ($this->container['original_environment'] === null) {
            $invalidProperties[] = "'original_environment' can't be null";
        }
        if ($this->container['payload'] === null) {
            $invalidProperties[] = "'payload' can't be null";
        }
        if ($this->container['ref'] === null) {
            $invalidProperties[] = "'ref' can't be null";
        }
        if ($this->container['repository_url'] === null) {
            $invalidProperties[] = "'repository_url' can't be null";
        }
        if ($this->container['sha'] === null) {
            $invalidProperties[] = "'sha' can't be null";
        }
        if ($this->container['statuses_url'] === null) {
            $invalidProperties[] = "'statuses_url' can't be null";
        }
        if ($this->container['task'] === null) {
            $invalidProperties[] = "'task' can't be null";
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
     * Gets created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param string $created_at created_at
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
     * Gets creator
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User
     */
    public function getCreator()
    {
        return $this->container['creator'];
    }

    /**
     * Sets creator
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User $creator creator
     *
     * @return self
     */
    public function setCreator($creator)
    {
        if (is_null($creator)) {
            array_push($this->openAPINullablesSetToNull, 'creator');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('creator', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['creator'] = $creator;

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
     * Gets environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->container['environment'];
    }

    /**
     * Sets environment
     *
     * @param string $environment environment
     *
     * @return self
     */
    public function setEnvironment($environment)
    {
        if (is_null($environment)) {
            throw new \InvalidArgumentException('non-nullable environment cannot be null');
        }
        $this->container['environment'] = $environment;

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
     * Gets original_environment
     *
     * @return string
     */
    public function getOriginalEnvironment()
    {
        return $this->container['original_environment'];
    }

    /**
     * Sets original_environment
     *
     * @param string $original_environment original_environment
     *
     * @return self
     */
    public function setOriginalEnvironment($original_environment)
    {
        if (is_null($original_environment)) {
            throw new \InvalidArgumentException('non-nullable original_environment cannot be null');
        }
        $this->container['original_environment'] = $original_environment;

        return $this;
    }

    /**
     * Gets payload
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DeploymentPayload
     */
    public function getPayload()
    {
        return $this->container['payload'];
    }

    /**
     * Sets payload
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DeploymentPayload $payload payload
     *
     * @return self
     */
    public function setPayload($payload)
    {
        if (is_null($payload)) {
            throw new \InvalidArgumentException('non-nullable payload cannot be null');
        }
        $this->container['payload'] = $payload;

        return $this;
    }

    /**
     * Gets performed_via_github_app
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App5|null
     */
    public function getPerformedViaGithubApp()
    {
        return $this->container['performed_via_github_app'];
    }

    /**
     * Sets performed_via_github_app
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\App5|null $performed_via_github_app performed_via_github_app
     *
     * @return self
     */
    public function setPerformedViaGithubApp($performed_via_github_app)
    {
        if (is_null($performed_via_github_app)) {
            array_push($this->openAPINullablesSetToNull, 'performed_via_github_app');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('performed_via_github_app', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['performed_via_github_app'] = $performed_via_github_app;

        return $this;
    }

    /**
     * Gets production_environment
     *
     * @return bool|null
     */
    public function getProductionEnvironment()
    {
        return $this->container['production_environment'];
    }

    /**
     * Sets production_environment
     *
     * @param bool|null $production_environment production_environment
     *
     * @return self
     */
    public function setProductionEnvironment($production_environment)
    {
        if (is_null($production_environment)) {
            throw new \InvalidArgumentException('non-nullable production_environment cannot be null');
        }
        $this->container['production_environment'] = $production_environment;

        return $this;
    }

    /**
     * Gets ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->container['ref'];
    }

    /**
     * Sets ref
     *
     * @param string $ref ref
     *
     * @return self
     */
    public function setRef($ref)
    {
        if (is_null($ref)) {
            throw new \InvalidArgumentException('non-nullable ref cannot be null');
        }
        $this->container['ref'] = $ref;

        return $this;
    }

    /**
     * Gets repository_url
     *
     * @return string
     */
    public function getRepositoryUrl()
    {
        return $this->container['repository_url'];
    }

    /**
     * Sets repository_url
     *
     * @param string $repository_url repository_url
     *
     * @return self
     */
    public function setRepositoryUrl($repository_url)
    {
        if (is_null($repository_url)) {
            throw new \InvalidArgumentException('non-nullable repository_url cannot be null');
        }
        $this->container['repository_url'] = $repository_url;

        return $this;
    }

    /**
     * Gets sha
     *
     * @return string
     */
    public function getSha()
    {
        return $this->container['sha'];
    }

    /**
     * Sets sha
     *
     * @param string $sha sha
     *
     * @return self
     */
    public function setSha($sha)
    {
        if (is_null($sha)) {
            throw new \InvalidArgumentException('non-nullable sha cannot be null');
        }
        $this->container['sha'] = $sha;

        return $this;
    }

    /**
     * Gets statuses_url
     *
     * @return string
     */
    public function getStatusesUrl()
    {
        return $this->container['statuses_url'];
    }

    /**
     * Sets statuses_url
     *
     * @param string $statuses_url statuses_url
     *
     * @return self
     */
    public function setStatusesUrl($statuses_url)
    {
        if (is_null($statuses_url)) {
            throw new \InvalidArgumentException('non-nullable statuses_url cannot be null');
        }
        $this->container['statuses_url'] = $statuses_url;

        return $this;
    }

    /**
     * Gets task
     *
     * @return string
     */
    public function getTask()
    {
        return $this->container['task'];
    }

    /**
     * Sets task
     *
     * @param string $task task
     *
     * @return self
     */
    public function setTask($task)
    {
        if (is_null($task)) {
            throw new \InvalidArgumentException('non-nullable task cannot be null');
        }
        $this->container['task'] = $task;

        return $this;
    }

    /**
     * Gets transient_environment
     *
     * @return bool|null
     */
    public function getTransientEnvironment()
    {
        return $this->container['transient_environment'];
    }

    /**
     * Sets transient_environment
     *
     * @param bool|null $transient_environment transient_environment
     *
     * @return self
     */
    public function setTransientEnvironment($transient_environment)
    {
        if (is_null($transient_environment)) {
            throw new \InvalidArgumentException('non-nullable transient_environment cannot be null');
        }
        $this->container['transient_environment'] = $transient_environment;

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->container['updated_at'];
    }

    /**
     * Sets updated_at
     *
     * @param string $updated_at updated_at
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


