<?php
/**
 * Feed
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
 * Feed Class Doc Comment
 *
 * @category Class
 * @description Feed
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Feed implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'feed';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'timeline_url' => 'string',
        'user_url' => 'string',
        'current_user_public_url' => 'string',
        'current_user_url' => 'string',
        'current_user_actor_url' => 'string',
        'current_user_organization_url' => 'string',
        'current_user_organization_urls' => 'string[]',
        'security_advisories_url' => 'string',
        'repository_discussions_url' => 'string',
        'repository_discussions_category_url' => 'string',
        '_links' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\FeedLinks'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'timeline_url' => null,
        'user_url' => null,
        'current_user_public_url' => null,
        'current_user_url' => null,
        'current_user_actor_url' => null,
        'current_user_organization_url' => null,
        'current_user_organization_urls' => 'uri',
        'security_advisories_url' => null,
        'repository_discussions_url' => null,
        'repository_discussions_category_url' => null,
        '_links' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'timeline_url' => false,
        'user_url' => false,
        'current_user_public_url' => false,
        'current_user_url' => false,
        'current_user_actor_url' => false,
        'current_user_organization_url' => false,
        'current_user_organization_urls' => false,
        'security_advisories_url' => false,
        'repository_discussions_url' => false,
        'repository_discussions_category_url' => false,
        '_links' => false
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
        'timeline_url' => 'timeline_url',
        'user_url' => 'user_url',
        'current_user_public_url' => 'current_user_public_url',
        'current_user_url' => 'current_user_url',
        'current_user_actor_url' => 'current_user_actor_url',
        'current_user_organization_url' => 'current_user_organization_url',
        'current_user_organization_urls' => 'current_user_organization_urls',
        'security_advisories_url' => 'security_advisories_url',
        'repository_discussions_url' => 'repository_discussions_url',
        'repository_discussions_category_url' => 'repository_discussions_category_url',
        '_links' => '_links'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'timeline_url' => 'setTimelineUrl',
        'user_url' => 'setUserUrl',
        'current_user_public_url' => 'setCurrentUserPublicUrl',
        'current_user_url' => 'setCurrentUserUrl',
        'current_user_actor_url' => 'setCurrentUserActorUrl',
        'current_user_organization_url' => 'setCurrentUserOrganizationUrl',
        'current_user_organization_urls' => 'setCurrentUserOrganizationUrls',
        'security_advisories_url' => 'setSecurityAdvisoriesUrl',
        'repository_discussions_url' => 'setRepositoryDiscussionsUrl',
        'repository_discussions_category_url' => 'setRepositoryDiscussionsCategoryUrl',
        '_links' => 'setLinks'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'timeline_url' => 'getTimelineUrl',
        'user_url' => 'getUserUrl',
        'current_user_public_url' => 'getCurrentUserPublicUrl',
        'current_user_url' => 'getCurrentUserUrl',
        'current_user_actor_url' => 'getCurrentUserActorUrl',
        'current_user_organization_url' => 'getCurrentUserOrganizationUrl',
        'current_user_organization_urls' => 'getCurrentUserOrganizationUrls',
        'security_advisories_url' => 'getSecurityAdvisoriesUrl',
        'repository_discussions_url' => 'getRepositoryDiscussionsUrl',
        'repository_discussions_category_url' => 'getRepositoryDiscussionsCategoryUrl',
        '_links' => 'getLinks'
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
        $this->setIfExists('timeline_url', $data ?? [], null);
        $this->setIfExists('user_url', $data ?? [], null);
        $this->setIfExists('current_user_public_url', $data ?? [], null);
        $this->setIfExists('current_user_url', $data ?? [], null);
        $this->setIfExists('current_user_actor_url', $data ?? [], null);
        $this->setIfExists('current_user_organization_url', $data ?? [], null);
        $this->setIfExists('current_user_organization_urls', $data ?? [], null);
        $this->setIfExists('security_advisories_url', $data ?? [], null);
        $this->setIfExists('repository_discussions_url', $data ?? [], null);
        $this->setIfExists('repository_discussions_category_url', $data ?? [], null);
        $this->setIfExists('_links', $data ?? [], null);
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

        if ($this->container['timeline_url'] === null) {
            $invalidProperties[] = "'timeline_url' can't be null";
        }
        if ($this->container['user_url'] === null) {
            $invalidProperties[] = "'user_url' can't be null";
        }
        if ($this->container['_links'] === null) {
            $invalidProperties[] = "'_links' can't be null";
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
     * Gets timeline_url
     *
     * @return string
     */
    public function getTimelineUrl()
    {
        return $this->container['timeline_url'];
    }

    /**
     * Sets timeline_url
     *
     * @param string $timeline_url timeline_url
     *
     * @return self
     */
    public function setTimelineUrl($timeline_url)
    {
        if (is_null($timeline_url)) {
            throw new \InvalidArgumentException('non-nullable timeline_url cannot be null');
        }
        $this->container['timeline_url'] = $timeline_url;

        return $this;
    }

    /**
     * Gets user_url
     *
     * @return string
     */
    public function getUserUrl()
    {
        return $this->container['user_url'];
    }

    /**
     * Sets user_url
     *
     * @param string $user_url user_url
     *
     * @return self
     */
    public function setUserUrl($user_url)
    {
        if (is_null($user_url)) {
            throw new \InvalidArgumentException('non-nullable user_url cannot be null');
        }
        $this->container['user_url'] = $user_url;

        return $this;
    }

    /**
     * Gets current_user_public_url
     *
     * @return string|null
     */
    public function getCurrentUserPublicUrl()
    {
        return $this->container['current_user_public_url'];
    }

    /**
     * Sets current_user_public_url
     *
     * @param string|null $current_user_public_url current_user_public_url
     *
     * @return self
     */
    public function setCurrentUserPublicUrl($current_user_public_url)
    {
        if (is_null($current_user_public_url)) {
            throw new \InvalidArgumentException('non-nullable current_user_public_url cannot be null');
        }
        $this->container['current_user_public_url'] = $current_user_public_url;

        return $this;
    }

    /**
     * Gets current_user_url
     *
     * @return string|null
     */
    public function getCurrentUserUrl()
    {
        return $this->container['current_user_url'];
    }

    /**
     * Sets current_user_url
     *
     * @param string|null $current_user_url current_user_url
     *
     * @return self
     */
    public function setCurrentUserUrl($current_user_url)
    {
        if (is_null($current_user_url)) {
            throw new \InvalidArgumentException('non-nullable current_user_url cannot be null');
        }
        $this->container['current_user_url'] = $current_user_url;

        return $this;
    }

    /**
     * Gets current_user_actor_url
     *
     * @return string|null
     */
    public function getCurrentUserActorUrl()
    {
        return $this->container['current_user_actor_url'];
    }

    /**
     * Sets current_user_actor_url
     *
     * @param string|null $current_user_actor_url current_user_actor_url
     *
     * @return self
     */
    public function setCurrentUserActorUrl($current_user_actor_url)
    {
        if (is_null($current_user_actor_url)) {
            throw new \InvalidArgumentException('non-nullable current_user_actor_url cannot be null');
        }
        $this->container['current_user_actor_url'] = $current_user_actor_url;

        return $this;
    }

    /**
     * Gets current_user_organization_url
     *
     * @return string|null
     */
    public function getCurrentUserOrganizationUrl()
    {
        return $this->container['current_user_organization_url'];
    }

    /**
     * Sets current_user_organization_url
     *
     * @param string|null $current_user_organization_url current_user_organization_url
     *
     * @return self
     */
    public function setCurrentUserOrganizationUrl($current_user_organization_url)
    {
        if (is_null($current_user_organization_url)) {
            throw new \InvalidArgumentException('non-nullable current_user_organization_url cannot be null');
        }
        $this->container['current_user_organization_url'] = $current_user_organization_url;

        return $this;
    }

    /**
     * Gets current_user_organization_urls
     *
     * @return string[]|null
     */
    public function getCurrentUserOrganizationUrls()
    {
        return $this->container['current_user_organization_urls'];
    }

    /**
     * Sets current_user_organization_urls
     *
     * @param string[]|null $current_user_organization_urls current_user_organization_urls
     *
     * @return self
     */
    public function setCurrentUserOrganizationUrls($current_user_organization_urls)
    {
        if (is_null($current_user_organization_urls)) {
            throw new \InvalidArgumentException('non-nullable current_user_organization_urls cannot be null');
        }
        $this->container['current_user_organization_urls'] = $current_user_organization_urls;

        return $this;
    }

    /**
     * Gets security_advisories_url
     *
     * @return string|null
     */
    public function getSecurityAdvisoriesUrl()
    {
        return $this->container['security_advisories_url'];
    }

    /**
     * Sets security_advisories_url
     *
     * @param string|null $security_advisories_url security_advisories_url
     *
     * @return self
     */
    public function setSecurityAdvisoriesUrl($security_advisories_url)
    {
        if (is_null($security_advisories_url)) {
            throw new \InvalidArgumentException('non-nullable security_advisories_url cannot be null');
        }
        $this->container['security_advisories_url'] = $security_advisories_url;

        return $this;
    }

    /**
     * Gets repository_discussions_url
     *
     * @return string|null
     */
    public function getRepositoryDiscussionsUrl()
    {
        return $this->container['repository_discussions_url'];
    }

    /**
     * Sets repository_discussions_url
     *
     * @param string|null $repository_discussions_url A feed of discussions for a given repository.
     *
     * @return self
     */
    public function setRepositoryDiscussionsUrl($repository_discussions_url)
    {
        if (is_null($repository_discussions_url)) {
            throw new \InvalidArgumentException('non-nullable repository_discussions_url cannot be null');
        }
        $this->container['repository_discussions_url'] = $repository_discussions_url;

        return $this;
    }

    /**
     * Gets repository_discussions_category_url
     *
     * @return string|null
     */
    public function getRepositoryDiscussionsCategoryUrl()
    {
        return $this->container['repository_discussions_category_url'];
    }

    /**
     * Sets repository_discussions_category_url
     *
     * @param string|null $repository_discussions_category_url A feed of discussions for a given repository and category.
     *
     * @return self
     */
    public function setRepositoryDiscussionsCategoryUrl($repository_discussions_category_url)
    {
        if (is_null($repository_discussions_category_url)) {
            throw new \InvalidArgumentException('non-nullable repository_discussions_category_url cannot be null');
        }
        $this->container['repository_discussions_category_url'] = $repository_discussions_category_url;

        return $this;
    }

    /**
     * Gets _links
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\FeedLinks
     */
    public function getLinks()
    {
        return $this->container['_links'];
    }

    /**
     * Sets _links
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\FeedLinks $_links _links
     *
     * @return self
     */
    public function setLinks($_links)
    {
        if (is_null($_links)) {
            throw new \InvalidArgumentException('non-nullable _links cannot be null');
        }
        $this->container['_links'] = $_links;

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


