<?php
/**
 * NullableSimpleUser
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
 * NullableSimpleUser Class Doc Comment
 *
 * @category Class
 * @description A GitHub user.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class NullableSimpleUser implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'nullable-simple-user';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'name' => 'string',
        'email' => 'string',
        'login' => 'string',
        'id' => 'int',
        'node_id' => 'string',
        'avatar_url' => 'string',
        'gravatar_id' => 'string',
        'url' => 'string',
        'html_url' => 'string',
        'followers_url' => 'string',
        'following_url' => 'string',
        'gists_url' => 'string',
        'starred_url' => 'string',
        'subscriptions_url' => 'string',
        'organizations_url' => 'string',
        'repos_url' => 'string',
        'events_url' => 'string',
        'received_events_url' => 'string',
        'type' => 'string',
        'site_admin' => 'bool',
        'starred_at' => 'string'
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
        'email' => null,
        'login' => null,
        'id' => null,
        'node_id' => null,
        'avatar_url' => 'uri',
        'gravatar_id' => null,
        'url' => 'uri',
        'html_url' => 'uri',
        'followers_url' => 'uri',
        'following_url' => null,
        'gists_url' => null,
        'starred_url' => null,
        'subscriptions_url' => 'uri',
        'organizations_url' => 'uri',
        'repos_url' => 'uri',
        'events_url' => null,
        'received_events_url' => 'uri',
        'type' => null,
        'site_admin' => null,
        'starred_at' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'name' => true,
        'email' => true,
        'login' => false,
        'id' => false,
        'node_id' => false,
        'avatar_url' => false,
        'gravatar_id' => true,
        'url' => false,
        'html_url' => false,
        'followers_url' => false,
        'following_url' => false,
        'gists_url' => false,
        'starred_url' => false,
        'subscriptions_url' => false,
        'organizations_url' => false,
        'repos_url' => false,
        'events_url' => false,
        'received_events_url' => false,
        'type' => false,
        'site_admin' => false,
        'starred_at' => false
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
        'email' => 'email',
        'login' => 'login',
        'id' => 'id',
        'node_id' => 'node_id',
        'avatar_url' => 'avatar_url',
        'gravatar_id' => 'gravatar_id',
        'url' => 'url',
        'html_url' => 'html_url',
        'followers_url' => 'followers_url',
        'following_url' => 'following_url',
        'gists_url' => 'gists_url',
        'starred_url' => 'starred_url',
        'subscriptions_url' => 'subscriptions_url',
        'organizations_url' => 'organizations_url',
        'repos_url' => 'repos_url',
        'events_url' => 'events_url',
        'received_events_url' => 'received_events_url',
        'type' => 'type',
        'site_admin' => 'site_admin',
        'starred_at' => 'starred_at'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'name' => 'setName',
        'email' => 'setEmail',
        'login' => 'setLogin',
        'id' => 'setId',
        'node_id' => 'setNodeId',
        'avatar_url' => 'setAvatarUrl',
        'gravatar_id' => 'setGravatarId',
        'url' => 'setUrl',
        'html_url' => 'setHtmlUrl',
        'followers_url' => 'setFollowersUrl',
        'following_url' => 'setFollowingUrl',
        'gists_url' => 'setGistsUrl',
        'starred_url' => 'setStarredUrl',
        'subscriptions_url' => 'setSubscriptionsUrl',
        'organizations_url' => 'setOrganizationsUrl',
        'repos_url' => 'setReposUrl',
        'events_url' => 'setEventsUrl',
        'received_events_url' => 'setReceivedEventsUrl',
        'type' => 'setType',
        'site_admin' => 'setSiteAdmin',
        'starred_at' => 'setStarredAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'name' => 'getName',
        'email' => 'getEmail',
        'login' => 'getLogin',
        'id' => 'getId',
        'node_id' => 'getNodeId',
        'avatar_url' => 'getAvatarUrl',
        'gravatar_id' => 'getGravatarId',
        'url' => 'getUrl',
        'html_url' => 'getHtmlUrl',
        'followers_url' => 'getFollowersUrl',
        'following_url' => 'getFollowingUrl',
        'gists_url' => 'getGistsUrl',
        'starred_url' => 'getStarredUrl',
        'subscriptions_url' => 'getSubscriptionsUrl',
        'organizations_url' => 'getOrganizationsUrl',
        'repos_url' => 'getReposUrl',
        'events_url' => 'getEventsUrl',
        'received_events_url' => 'getReceivedEventsUrl',
        'type' => 'getType',
        'site_admin' => 'getSiteAdmin',
        'starred_at' => 'getStarredAt'
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
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('email', $data ?? [], null);
        $this->setIfExists('login', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('avatar_url', $data ?? [], null);
        $this->setIfExists('gravatar_id', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('followers_url', $data ?? [], null);
        $this->setIfExists('following_url', $data ?? [], null);
        $this->setIfExists('gists_url', $data ?? [], null);
        $this->setIfExists('starred_url', $data ?? [], null);
        $this->setIfExists('subscriptions_url', $data ?? [], null);
        $this->setIfExists('organizations_url', $data ?? [], null);
        $this->setIfExists('repos_url', $data ?? [], null);
        $this->setIfExists('events_url', $data ?? [], null);
        $this->setIfExists('received_events_url', $data ?? [], null);
        $this->setIfExists('type', $data ?? [], null);
        $this->setIfExists('site_admin', $data ?? [], null);
        $this->setIfExists('starred_at', $data ?? [], null);
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

        if ($this->container['login'] === null) {
            $invalidProperties[] = "'login' can't be null";
        }
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['node_id'] === null) {
            $invalidProperties[] = "'node_id' can't be null";
        }
        if ($this->container['avatar_url'] === null) {
            $invalidProperties[] = "'avatar_url' can't be null";
        }
        if ($this->container['gravatar_id'] === null) {
            $invalidProperties[] = "'gravatar_id' can't be null";
        }
        if ($this->container['url'] === null) {
            $invalidProperties[] = "'url' can't be null";
        }
        if ($this->container['html_url'] === null) {
            $invalidProperties[] = "'html_url' can't be null";
        }
        if ($this->container['followers_url'] === null) {
            $invalidProperties[] = "'followers_url' can't be null";
        }
        if ($this->container['following_url'] === null) {
            $invalidProperties[] = "'following_url' can't be null";
        }
        if ($this->container['gists_url'] === null) {
            $invalidProperties[] = "'gists_url' can't be null";
        }
        if ($this->container['starred_url'] === null) {
            $invalidProperties[] = "'starred_url' can't be null";
        }
        if ($this->container['subscriptions_url'] === null) {
            $invalidProperties[] = "'subscriptions_url' can't be null";
        }
        if ($this->container['organizations_url'] === null) {
            $invalidProperties[] = "'organizations_url' can't be null";
        }
        if ($this->container['repos_url'] === null) {
            $invalidProperties[] = "'repos_url' can't be null";
        }
        if ($this->container['events_url'] === null) {
            $invalidProperties[] = "'events_url' can't be null";
        }
        if ($this->container['received_events_url'] === null) {
            $invalidProperties[] = "'received_events_url' can't be null";
        }
        if ($this->container['type'] === null) {
            $invalidProperties[] = "'type' can't be null";
        }
        if ($this->container['site_admin'] === null) {
            $invalidProperties[] = "'site_admin' can't be null";
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
     * @return string|null
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string|null $name name
     *
     * @return self
     */
    public function setName($name)
    {
        if (is_null($name)) {
            array_push($this->openAPINullablesSetToNull, 'name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     *
     * @param string|null $email email
     *
     * @return self
     */
    public function setEmail($email)
    {
        if (is_null($email)) {
            array_push($this->openAPINullablesSetToNull, 'email');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('email', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['email'] = $email;

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
     * Gets gravatar_id
     *
     * @return string
     */
    public function getGravatarId()
    {
        return $this->container['gravatar_id'];
    }

    /**
     * Sets gravatar_id
     *
     * @param string $gravatar_id gravatar_id
     *
     * @return self
     */
    public function setGravatarId($gravatar_id)
    {
        if (is_null($gravatar_id)) {
            array_push($this->openAPINullablesSetToNull, 'gravatar_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('gravatar_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['gravatar_id'] = $gravatar_id;

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
            throw new \InvalidArgumentException('non-nullable html_url cannot be null');
        }
        $this->container['html_url'] = $html_url;

        return $this;
    }

    /**
     * Gets followers_url
     *
     * @return string
     */
    public function getFollowersUrl()
    {
        return $this->container['followers_url'];
    }

    /**
     * Sets followers_url
     *
     * @param string $followers_url followers_url
     *
     * @return self
     */
    public function setFollowersUrl($followers_url)
    {
        if (is_null($followers_url)) {
            throw new \InvalidArgumentException('non-nullable followers_url cannot be null');
        }
        $this->container['followers_url'] = $followers_url;

        return $this;
    }

    /**
     * Gets following_url
     *
     * @return string
     */
    public function getFollowingUrl()
    {
        return $this->container['following_url'];
    }

    /**
     * Sets following_url
     *
     * @param string $following_url following_url
     *
     * @return self
     */
    public function setFollowingUrl($following_url)
    {
        if (is_null($following_url)) {
            throw new \InvalidArgumentException('non-nullable following_url cannot be null');
        }
        $this->container['following_url'] = $following_url;

        return $this;
    }

    /**
     * Gets gists_url
     *
     * @return string
     */
    public function getGistsUrl()
    {
        return $this->container['gists_url'];
    }

    /**
     * Sets gists_url
     *
     * @param string $gists_url gists_url
     *
     * @return self
     */
    public function setGistsUrl($gists_url)
    {
        if (is_null($gists_url)) {
            throw new \InvalidArgumentException('non-nullable gists_url cannot be null');
        }
        $this->container['gists_url'] = $gists_url;

        return $this;
    }

    /**
     * Gets starred_url
     *
     * @return string
     */
    public function getStarredUrl()
    {
        return $this->container['starred_url'];
    }

    /**
     * Sets starred_url
     *
     * @param string $starred_url starred_url
     *
     * @return self
     */
    public function setStarredUrl($starred_url)
    {
        if (is_null($starred_url)) {
            throw new \InvalidArgumentException('non-nullable starred_url cannot be null');
        }
        $this->container['starred_url'] = $starred_url;

        return $this;
    }

    /**
     * Gets subscriptions_url
     *
     * @return string
     */
    public function getSubscriptionsUrl()
    {
        return $this->container['subscriptions_url'];
    }

    /**
     * Sets subscriptions_url
     *
     * @param string $subscriptions_url subscriptions_url
     *
     * @return self
     */
    public function setSubscriptionsUrl($subscriptions_url)
    {
        if (is_null($subscriptions_url)) {
            throw new \InvalidArgumentException('non-nullable subscriptions_url cannot be null');
        }
        $this->container['subscriptions_url'] = $subscriptions_url;

        return $this;
    }

    /**
     * Gets organizations_url
     *
     * @return string
     */
    public function getOrganizationsUrl()
    {
        return $this->container['organizations_url'];
    }

    /**
     * Sets organizations_url
     *
     * @param string $organizations_url organizations_url
     *
     * @return self
     */
    public function setOrganizationsUrl($organizations_url)
    {
        if (is_null($organizations_url)) {
            throw new \InvalidArgumentException('non-nullable organizations_url cannot be null');
        }
        $this->container['organizations_url'] = $organizations_url;

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
     * Gets received_events_url
     *
     * @return string
     */
    public function getReceivedEventsUrl()
    {
        return $this->container['received_events_url'];
    }

    /**
     * Sets received_events_url
     *
     * @param string $received_events_url received_events_url
     *
     * @return self
     */
    public function setReceivedEventsUrl($received_events_url)
    {
        if (is_null($received_events_url)) {
            throw new \InvalidArgumentException('non-nullable received_events_url cannot be null');
        }
        $this->container['received_events_url'] = $received_events_url;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->container['type'];
    }

    /**
     * Sets type
     *
     * @param string $type type
     *
     * @return self
     */
    public function setType($type)
    {
        if (is_null($type)) {
            throw new \InvalidArgumentException('non-nullable type cannot be null');
        }
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * Gets site_admin
     *
     * @return bool
     */
    public function getSiteAdmin()
    {
        return $this->container['site_admin'];
    }

    /**
     * Sets site_admin
     *
     * @param bool $site_admin site_admin
     *
     * @return self
     */
    public function setSiteAdmin($site_admin)
    {
        if (is_null($site_admin)) {
            throw new \InvalidArgumentException('non-nullable site_admin cannot be null');
        }
        $this->container['site_admin'] = $site_admin;

        return $this;
    }

    /**
     * Gets starred_at
     *
     * @return string|null
     */
    public function getStarredAt()
    {
        return $this->container['starred_at'];
    }

    /**
     * Sets starred_at
     *
     * @param string|null $starred_at starred_at
     *
     * @return self
     */
    public function setStarredAt($starred_at)
    {
        if (is_null($starred_at)) {
            throw new \InvalidArgumentException('non-nullable starred_at cannot be null');
        }
        $this->container['starred_at'] = $starred_at;

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


