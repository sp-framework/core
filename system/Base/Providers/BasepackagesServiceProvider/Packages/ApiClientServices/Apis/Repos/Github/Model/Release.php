<?php
/**
 * Release
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
 * Release Class Doc Comment
 *
 * @category Class
 * @description The [release](https://docs.github.com/enterprise-server@3.12/rest/releases/releases/#get-a-release) object.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Release implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Release';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'assets' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReleaseAsset1[]',
        'assets_url' => 'string',
        'author' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User',
        'body' => 'string',
        'created_at' => '\DateTime',
        'discussion_url' => 'string',
        'draft' => 'bool',
        'html_url' => 'string',
        'id' => 'int',
        'name' => 'string',
        'node_id' => 'string',
        'prerelease' => 'bool',
        'published_at' => '\DateTime',
        'reactions' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Reactions',
        'tag_name' => 'string',
        'tarball_url' => 'string',
        'target_commitish' => 'string',
        'upload_url' => 'string',
        'url' => 'string',
        'zipball_url' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'assets' => null,
        'assets_url' => 'uri',
        'author' => null,
        'body' => null,
        'created_at' => 'date-time',
        'discussion_url' => 'uri',
        'draft' => null,
        'html_url' => 'uri',
        'id' => null,
        'name' => null,
        'node_id' => null,
        'prerelease' => null,
        'published_at' => 'date-time',
        'reactions' => null,
        'tag_name' => null,
        'tarball_url' => 'uri',
        'target_commitish' => null,
        'upload_url' => 'uri-template',
        'url' => 'uri',
        'zipball_url' => 'uri'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'assets' => false,
        'assets_url' => false,
        'author' => true,
        'body' => true,
        'created_at' => true,
        'discussion_url' => false,
        'draft' => false,
        'html_url' => false,
        'id' => false,
        'name' => true,
        'node_id' => false,
        'prerelease' => false,
        'published_at' => true,
        'reactions' => false,
        'tag_name' => false,
        'tarball_url' => true,
        'target_commitish' => false,
        'upload_url' => false,
        'url' => false,
        'zipball_url' => true
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
        'assets' => 'assets',
        'assets_url' => 'assets_url',
        'author' => 'author',
        'body' => 'body',
        'created_at' => 'created_at',
        'discussion_url' => 'discussion_url',
        'draft' => 'draft',
        'html_url' => 'html_url',
        'id' => 'id',
        'name' => 'name',
        'node_id' => 'node_id',
        'prerelease' => 'prerelease',
        'published_at' => 'published_at',
        'reactions' => 'reactions',
        'tag_name' => 'tag_name',
        'tarball_url' => 'tarball_url',
        'target_commitish' => 'target_commitish',
        'upload_url' => 'upload_url',
        'url' => 'url',
        'zipball_url' => 'zipball_url'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'assets' => 'setAssets',
        'assets_url' => 'setAssetsUrl',
        'author' => 'setAuthor',
        'body' => 'setBody',
        'created_at' => 'setCreatedAt',
        'discussion_url' => 'setDiscussionUrl',
        'draft' => 'setDraft',
        'html_url' => 'setHtmlUrl',
        'id' => 'setId',
        'name' => 'setName',
        'node_id' => 'setNodeId',
        'prerelease' => 'setPrerelease',
        'published_at' => 'setPublishedAt',
        'reactions' => 'setReactions',
        'tag_name' => 'setTagName',
        'tarball_url' => 'setTarballUrl',
        'target_commitish' => 'setTargetCommitish',
        'upload_url' => 'setUploadUrl',
        'url' => 'setUrl',
        'zipball_url' => 'setZipballUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'assets' => 'getAssets',
        'assets_url' => 'getAssetsUrl',
        'author' => 'getAuthor',
        'body' => 'getBody',
        'created_at' => 'getCreatedAt',
        'discussion_url' => 'getDiscussionUrl',
        'draft' => 'getDraft',
        'html_url' => 'getHtmlUrl',
        'id' => 'getId',
        'name' => 'getName',
        'node_id' => 'getNodeId',
        'prerelease' => 'getPrerelease',
        'published_at' => 'getPublishedAt',
        'reactions' => 'getReactions',
        'tag_name' => 'getTagName',
        'tarball_url' => 'getTarballUrl',
        'target_commitish' => 'getTargetCommitish',
        'upload_url' => 'getUploadUrl',
        'url' => 'getUrl',
        'zipball_url' => 'getZipballUrl'
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
        $this->setIfExists('assets', $data ?? [], null);
        $this->setIfExists('assets_url', $data ?? [], null);
        $this->setIfExists('author', $data ?? [], null);
        $this->setIfExists('body', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('discussion_url', $data ?? [], null);
        $this->setIfExists('draft', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('prerelease', $data ?? [], null);
        $this->setIfExists('published_at', $data ?? [], null);
        $this->setIfExists('reactions', $data ?? [], null);
        $this->setIfExists('tag_name', $data ?? [], null);
        $this->setIfExists('tarball_url', $data ?? [], null);
        $this->setIfExists('target_commitish', $data ?? [], null);
        $this->setIfExists('upload_url', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
        $this->setIfExists('zipball_url', $data ?? [], null);
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

        if ($this->container['assets'] === null) {
            $invalidProperties[] = "'assets' can't be null";
        }
        if ($this->container['assets_url'] === null) {
            $invalidProperties[] = "'assets_url' can't be null";
        }
        if ($this->container['author'] === null) {
            $invalidProperties[] = "'author' can't be null";
        }
        if ($this->container['body'] === null) {
            $invalidProperties[] = "'body' can't be null";
        }
        if ($this->container['created_at'] === null) {
            $invalidProperties[] = "'created_at' can't be null";
        }
        if ($this->container['draft'] === null) {
            $invalidProperties[] = "'draft' can't be null";
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
        if ($this->container['prerelease'] === null) {
            $invalidProperties[] = "'prerelease' can't be null";
        }
        if ($this->container['published_at'] === null) {
            $invalidProperties[] = "'published_at' can't be null";
        }
        if ($this->container['tag_name'] === null) {
            $invalidProperties[] = "'tag_name' can't be null";
        }
        if ($this->container['tarball_url'] === null) {
            $invalidProperties[] = "'tarball_url' can't be null";
        }
        if ($this->container['target_commitish'] === null) {
            $invalidProperties[] = "'target_commitish' can't be null";
        }
        if ($this->container['upload_url'] === null) {
            $invalidProperties[] = "'upload_url' can't be null";
        }
        if ($this->container['url'] === null) {
            $invalidProperties[] = "'url' can't be null";
        }
        if ($this->container['zipball_url'] === null) {
            $invalidProperties[] = "'zipball_url' can't be null";
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
     * Gets assets
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReleaseAsset1[]
     */
    public function getAssets()
    {
        return $this->container['assets'];
    }

    /**
     * Sets assets
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReleaseAsset1[] $assets assets
     *
     * @return self
     */
    public function setAssets($assets)
    {
        if (is_null($assets)) {
            throw new \InvalidArgumentException('non-nullable assets cannot be null');
        }
        $this->container['assets'] = $assets;

        return $this;
    }

    /**
     * Gets assets_url
     *
     * @return string
     */
    public function getAssetsUrl()
    {
        return $this->container['assets_url'];
    }

    /**
     * Sets assets_url
     *
     * @param string $assets_url assets_url
     *
     * @return self
     */
    public function setAssetsUrl($assets_url)
    {
        if (is_null($assets_url)) {
            throw new \InvalidArgumentException('non-nullable assets_url cannot be null');
        }
        $this->container['assets_url'] = $assets_url;

        return $this;
    }

    /**
     * Gets author
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User
     */
    public function getAuthor()
    {
        return $this->container['author'];
    }

    /**
     * Sets author
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\User $author author
     *
     * @return self
     */
    public function setAuthor($author)
    {
        if (is_null($author)) {
            array_push($this->openAPINullablesSetToNull, 'author');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('author', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['author'] = $author;

        return $this;
    }

    /**
     * Gets body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->container['body'];
    }

    /**
     * Sets body
     *
     * @param string $body body
     *
     * @return self
     */
    public function setBody($body)
    {
        if (is_null($body)) {
            array_push($this->openAPINullablesSetToNull, 'body');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('body', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['body'] = $body;

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
     * Gets discussion_url
     *
     * @return string|null
     */
    public function getDiscussionUrl()
    {
        return $this->container['discussion_url'];
    }

    /**
     * Sets discussion_url
     *
     * @param string|null $discussion_url discussion_url
     *
     * @return self
     */
    public function setDiscussionUrl($discussion_url)
    {
        if (is_null($discussion_url)) {
            throw new \InvalidArgumentException('non-nullable discussion_url cannot be null');
        }
        $this->container['discussion_url'] = $discussion_url;

        return $this;
    }

    /**
     * Gets draft
     *
     * @return bool
     */
    public function getDraft()
    {
        return $this->container['draft'];
    }

    /**
     * Sets draft
     *
     * @param bool $draft Whether the release is a draft or published
     *
     * @return self
     */
    public function setDraft($draft)
    {
        if (is_null($draft)) {
            throw new \InvalidArgumentException('non-nullable draft cannot be null');
        }
        $this->container['draft'] = $draft;

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
     * @param string $name name
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
     * Gets prerelease
     *
     * @return bool
     */
    public function getPrerelease()
    {
        return $this->container['prerelease'];
    }

    /**
     * Sets prerelease
     *
     * @param bool $prerelease Whether the release is identified as a prerelease or a full release.
     *
     * @return self
     */
    public function setPrerelease($prerelease)
    {
        if (is_null($prerelease)) {
            throw new \InvalidArgumentException('non-nullable prerelease cannot be null');
        }
        $this->container['prerelease'] = $prerelease;

        return $this;
    }

    /**
     * Gets published_at
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->container['published_at'];
    }

    /**
     * Sets published_at
     *
     * @param \DateTime $published_at published_at
     *
     * @return self
     */
    public function setPublishedAt($published_at)
    {
        if (is_null($published_at)) {
            array_push($this->openAPINullablesSetToNull, 'published_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('published_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['published_at'] = $published_at;

        return $this;
    }

    /**
     * Gets reactions
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Reactions|null
     */
    public function getReactions()
    {
        return $this->container['reactions'];
    }

    /**
     * Sets reactions
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Reactions|null $reactions reactions
     *
     * @return self
     */
    public function setReactions($reactions)
    {
        if (is_null($reactions)) {
            throw new \InvalidArgumentException('non-nullable reactions cannot be null');
        }
        $this->container['reactions'] = $reactions;

        return $this;
    }

    /**
     * Gets tag_name
     *
     * @return string
     */
    public function getTagName()
    {
        return $this->container['tag_name'];
    }

    /**
     * Sets tag_name
     *
     * @param string $tag_name The name of the tag.
     *
     * @return self
     */
    public function setTagName($tag_name)
    {
        if (is_null($tag_name)) {
            throw new \InvalidArgumentException('non-nullable tag_name cannot be null');
        }
        $this->container['tag_name'] = $tag_name;

        return $this;
    }

    /**
     * Gets tarball_url
     *
     * @return string
     */
    public function getTarballUrl()
    {
        return $this->container['tarball_url'];
    }

    /**
     * Sets tarball_url
     *
     * @param string $tarball_url tarball_url
     *
     * @return self
     */
    public function setTarballUrl($tarball_url)
    {
        if (is_null($tarball_url)) {
            array_push($this->openAPINullablesSetToNull, 'tarball_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('tarball_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['tarball_url'] = $tarball_url;

        return $this;
    }

    /**
     * Gets target_commitish
     *
     * @return string
     */
    public function getTargetCommitish()
    {
        return $this->container['target_commitish'];
    }

    /**
     * Sets target_commitish
     *
     * @param string $target_commitish Specifies the commitish value that determines where the Git tag is created from.
     *
     * @return self
     */
    public function setTargetCommitish($target_commitish)
    {
        if (is_null($target_commitish)) {
            throw new \InvalidArgumentException('non-nullable target_commitish cannot be null');
        }
        $this->container['target_commitish'] = $target_commitish;

        return $this;
    }

    /**
     * Gets upload_url
     *
     * @return string
     */
    public function getUploadUrl()
    {
        return $this->container['upload_url'];
    }

    /**
     * Sets upload_url
     *
     * @param string $upload_url upload_url
     *
     * @return self
     */
    public function setUploadUrl($upload_url)
    {
        if (is_null($upload_url)) {
            throw new \InvalidArgumentException('non-nullable upload_url cannot be null');
        }
        $this->container['upload_url'] = $upload_url;

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
     * Gets zipball_url
     *
     * @return string
     */
    public function getZipballUrl()
    {
        return $this->container['zipball_url'];
    }

    /**
     * Sets zipball_url
     *
     * @param string $zipball_url zipball_url
     *
     * @return self
     */
    public function setZipballUrl($zipball_url)
    {
        if (is_null($zipball_url)) {
            array_push($this->openAPINullablesSetToNull, 'zipball_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('zipball_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['zipball_url'] = $zipball_url;

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


