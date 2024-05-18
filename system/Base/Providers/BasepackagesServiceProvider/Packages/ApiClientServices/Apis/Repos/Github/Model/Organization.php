<?php
/**
 * Organization
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
 * Organization Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Organization implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Organization';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'avatar_url' => 'string',
        'description' => 'string',
        'events_url' => 'string',
        'hooks_url' => 'string',
        'html_url' => 'string',
        'id' => 'int',
        'issues_url' => 'string',
        'login' => 'string',
        'members_url' => 'string',
        'node_id' => 'string',
        'public_members_url' => 'string',
        'repos_url' => 'string',
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
        'avatar_url' => 'uri',
        'description' => null,
        'events_url' => 'uri',
        'hooks_url' => 'uri',
        'html_url' => 'uri',
        'id' => null,
        'issues_url' => 'uri',
        'login' => null,
        'members_url' => 'uri-template',
        'node_id' => null,
        'public_members_url' => 'uri-template',
        'repos_url' => 'uri',
        'url' => 'uri'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'avatar_url' => false,
        'description' => true,
        'events_url' => false,
        'hooks_url' => false,
        'html_url' => false,
        'id' => false,
        'issues_url' => false,
        'login' => false,
        'members_url' => false,
        'node_id' => false,
        'public_members_url' => false,
        'repos_url' => false,
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
        'avatar_url' => 'avatar_url',
        'description' => 'description',
        'events_url' => 'events_url',
        'hooks_url' => 'hooks_url',
        'html_url' => 'html_url',
        'id' => 'id',
        'issues_url' => 'issues_url',
        'login' => 'login',
        'members_url' => 'members_url',
        'node_id' => 'node_id',
        'public_members_url' => 'public_members_url',
        'repos_url' => 'repos_url',
        'url' => 'url'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'avatar_url' => 'setAvatarUrl',
        'description' => 'setDescription',
        'events_url' => 'setEventsUrl',
        'hooks_url' => 'setHooksUrl',
        'html_url' => 'setHtmlUrl',
        'id' => 'setId',
        'issues_url' => 'setIssuesUrl',
        'login' => 'setLogin',
        'members_url' => 'setMembersUrl',
        'node_id' => 'setNodeId',
        'public_members_url' => 'setPublicMembersUrl',
        'repos_url' => 'setReposUrl',
        'url' => 'setUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'avatar_url' => 'getAvatarUrl',
        'description' => 'getDescription',
        'events_url' => 'getEventsUrl',
        'hooks_url' => 'getHooksUrl',
        'html_url' => 'getHtmlUrl',
        'id' => 'getId',
        'issues_url' => 'getIssuesUrl',
        'login' => 'getLogin',
        'members_url' => 'getMembersUrl',
        'node_id' => 'getNodeId',
        'public_members_url' => 'getPublicMembersUrl',
        'repos_url' => 'getReposUrl',
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
        $this->setIfExists('avatar_url', $data ?? [], null);
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('events_url', $data ?? [], null);
        $this->setIfExists('hooks_url', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('issues_url', $data ?? [], null);
        $this->setIfExists('login', $data ?? [], null);
        $this->setIfExists('members_url', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('public_members_url', $data ?? [], null);
        $this->setIfExists('repos_url', $data ?? [], null);
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

        if ($this->container['avatar_url'] === null) {
            $invalidProperties[] = "'avatar_url' can't be null";
        }
        if ($this->container['description'] === null) {
            $invalidProperties[] = "'description' can't be null";
        }
        if ($this->container['events_url'] === null) {
            $invalidProperties[] = "'events_url' can't be null";
        }
        if ($this->container['hooks_url'] === null) {
            $invalidProperties[] = "'hooks_url' can't be null";
        }
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['issues_url'] === null) {
            $invalidProperties[] = "'issues_url' can't be null";
        }
        if ($this->container['login'] === null) {
            $invalidProperties[] = "'login' can't be null";
        }
        if ($this->container['members_url'] === null) {
            $invalidProperties[] = "'members_url' can't be null";
        }
        if ($this->container['node_id'] === null) {
            $invalidProperties[] = "'node_id' can't be null";
        }
        if ($this->container['public_members_url'] === null) {
            $invalidProperties[] = "'public_members_url' can't be null";
        }
        if ($this->container['repos_url'] === null) {
            $invalidProperties[] = "'repos_url' can't be null";
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
     * Gets avatar_url
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->container['avatar_url'];
    }

    /**
     * Sets avatar_url
     *
     * @param string $avatar_url avatar_url
     *
     * @return self
     */
    public function setAvatarUrl($avatar_url)
    {
        if (is_null($avatar_url)) {
            throw new \InvalidArgumentException('non-nullable avatar_url cannot be null');
        }
        $this->container['avatar_url'] = $avatar_url;

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
     * Gets events_url
     *
     * @return string
     */
    public function getEventsUrl()
    {
        return $this->container['events_url'];
    }

    /**
     * Sets events_url
     *
     * @param string $events_url events_url
     *
     * @return self
     */
    public function setEventsUrl($events_url)
    {
        if (is_null($events_url)) {
            throw new \InvalidArgumentException('non-nullable events_url cannot be null');
        }
        $this->container['events_url'] = $events_url;

        return $this;
    }

    /**
     * Gets hooks_url
     *
     * @return string
     */
    public function getHooksUrl()
    {
        return $this->container['hooks_url'];
    }

    /**
     * Sets hooks_url
     *
     * @param string $hooks_url hooks_url
     *
     * @return self
     */
    public function setHooksUrl($hooks_url)
    {
        if (is_null($hooks_url)) {
            throw new \InvalidArgumentException('non-nullable hooks_url cannot be null');
        }
        $this->container['hooks_url'] = $hooks_url;

        return $this;
    }

    /**
     * Gets html_url
     *
     * @return string|null
     */
    public function getHtmlUrl()
    {
        return $this->container['html_url'];
    }

    /**
     * Sets html_url
     *
     * @param string|null $html_url html_url
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
     * Gets issues_url
     *
     * @return string
     */
    public function getIssuesUrl()
    {
        return $this->container['issues_url'];
    }

    /**
     * Sets issues_url
     *
     * @param string $issues_url issues_url
     *
     * @return self
     */
    public function setIssuesUrl($issues_url)
    {
        if (is_null($issues_url)) {
            throw new \InvalidArgumentException('non-nullable issues_url cannot be null');
        }
        $this->container['issues_url'] = $issues_url;

        return $this;
    }

    /**
     * Gets login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->container['login'];
    }

    /**
     * Sets login
     *
     * @param string $login login
     *
     * @return self
     */
    public function setLogin($login)
    {
        if (is_null($login)) {
            throw new \InvalidArgumentException('non-nullable login cannot be null');
        }
        $this->container['login'] = $login;

        return $this;
    }

    /**
     * Gets members_url
     *
     * @return string
     */
    public function getMembersUrl()
    {
        return $this->container['members_url'];
    }

    /**
     * Sets members_url
     *
     * @param string $members_url members_url
     *
     * @return self
     */
    public function setMembersUrl($members_url)
    {
        if (is_null($members_url)) {
            throw new \InvalidArgumentException('non-nullable members_url cannot be null');
        }
        $this->container['members_url'] = $members_url;

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
     * Gets public_members_url
     *
     * @return string
     */
    public function getPublicMembersUrl()
    {
        return $this->container['public_members_url'];
    }

    /**
     * Sets public_members_url
     *
     * @param string $public_members_url public_members_url
     *
     * @return self
     */
    public function setPublicMembersUrl($public_members_url)
    {
        if (is_null($public_members_url)) {
            throw new \InvalidArgumentException('non-nullable public_members_url cannot be null');
        }
        $this->container['public_members_url'] = $public_members_url;

        return $this;
    }

    /**
     * Gets repos_url
     *
     * @return string
     */
    public function getReposUrl()
    {
        return $this->container['repos_url'];
    }

    /**
     * Sets repos_url
     *
     * @param string $repos_url repos_url
     *
     * @return self
     */
    public function setReposUrl($repos_url)
    {
        if (is_null($repos_url)) {
            throw new \InvalidArgumentException('non-nullable repos_url cannot be null');
        }
        $this->container['repos_url'] = $repos_url;

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


