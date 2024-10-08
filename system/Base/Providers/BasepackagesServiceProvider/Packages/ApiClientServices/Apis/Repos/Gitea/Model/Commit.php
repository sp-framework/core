<?php
/**
 * Commit
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Gitea API
 *
 * This documentation describes the Gitea API.
 *
 * The version of the OpenAPI document: 1.21.7
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.5.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model;

use \ArrayAccess;
use \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\ObjectSerializer;

/**
 * Commit Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Commit implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Commit';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'author' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\User',
        'commit' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\RepoCommit',
        'committer' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\User',
        'created' => '\DateTime',
        'files' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitAffectedFiles[]',
        'html_url' => 'string',
        'parents' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitMeta[]',
        'sha' => 'string',
        'stats' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitStats',
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
        'author' => null,
        'commit' => null,
        'committer' => null,
        'created' => 'date-time',
        'files' => null,
        'html_url' => null,
        'parents' => null,
        'sha' => null,
        'stats' => null,
        'url' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'author' => false,
        'commit' => false,
        'committer' => false,
        'created' => false,
        'files' => false,
        'html_url' => false,
        'parents' => false,
        'sha' => false,
        'stats' => false,
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
        'author' => 'author',
        'commit' => 'commit',
        'committer' => 'committer',
        'created' => 'created',
        'files' => 'files',
        'html_url' => 'html_url',
        'parents' => 'parents',
        'sha' => 'sha',
        'stats' => 'stats',
        'url' => 'url'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'author' => 'setAuthor',
        'commit' => 'setCommit',
        'committer' => 'setCommitter',
        'created' => 'setCreated',
        'files' => 'setFiles',
        'html_url' => 'setHtmlUrl',
        'parents' => 'setParents',
        'sha' => 'setSha',
        'stats' => 'setStats',
        'url' => 'setUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'author' => 'getAuthor',
        'commit' => 'getCommit',
        'committer' => 'getCommitter',
        'created' => 'getCreated',
        'files' => 'getFiles',
        'html_url' => 'getHtmlUrl',
        'parents' => 'getParents',
        'sha' => 'getSha',
        'stats' => 'getStats',
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
        $this->setIfExists('author', $data ?? [], null);
        $this->setIfExists('commit', $data ?? [], null);
        $this->setIfExists('committer', $data ?? [], null);
        $this->setIfExists('created', $data ?? [], null);
        $this->setIfExists('files', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('parents', $data ?? [], null);
        $this->setIfExists('sha', $data ?? [], null);
        $this->setIfExists('stats', $data ?? [], null);
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
     * Gets author
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\User|null
     */
    public function getAuthor()
    {
        return $this->container['author'];
    }

    /**
     * Sets author
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\User|null $author author
     *
     * @return self
     */
    public function setAuthor($author)
    {
        if (is_null($author)) {
            throw new \InvalidArgumentException('non-nullable author cannot be null');
        }
        $this->container['author'] = $author;

        return $this;
    }

    /**
     * Gets commit
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\RepoCommit|null
     */
    public function getCommit()
    {
        return $this->container['commit'];
    }

    /**
     * Sets commit
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\RepoCommit|null $commit commit
     *
     * @return self
     */
    public function setCommit($commit)
    {
        if (is_null($commit)) {
            throw new \InvalidArgumentException('non-nullable commit cannot be null');
        }
        $this->container['commit'] = $commit;

        return $this;
    }

    /**
     * Gets committer
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\User|null
     */
    public function getCommitter()
    {
        return $this->container['committer'];
    }

    /**
     * Sets committer
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\User|null $committer committer
     *
     * @return self
     */
    public function setCommitter($committer)
    {
        if (is_null($committer)) {
            throw new \InvalidArgumentException('non-nullable committer cannot be null');
        }
        $this->container['committer'] = $committer;

        return $this;
    }

    /**
     * Gets created
     *
     * @return \DateTime|null
     */
    public function getCreated()
    {
        return $this->container['created'];
    }

    /**
     * Sets created
     *
     * @param \DateTime|null $created created
     *
     * @return self
     */
    public function setCreated($created)
    {
        if (is_null($created)) {
            throw new \InvalidArgumentException('non-nullable created cannot be null');
        }
        $this->container['created'] = $created;

        return $this;
    }

    /**
     * Gets files
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitAffectedFiles[]|null
     */
    public function getFiles()
    {
        return $this->container['files'];
    }

    /**
     * Sets files
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitAffectedFiles[]|null $files files
     *
     * @return self
     */
    public function setFiles($files)
    {
        if (is_null($files)) {
            throw new \InvalidArgumentException('non-nullable files cannot be null');
        }
        $this->container['files'] = $files;

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
     * Gets parents
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitMeta[]|null
     */
    public function getParents()
    {
        return $this->container['parents'];
    }

    /**
     * Sets parents
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitMeta[]|null $parents parents
     *
     * @return self
     */
    public function setParents($parents)
    {
        if (is_null($parents)) {
            throw new \InvalidArgumentException('non-nullable parents cannot be null');
        }
        $this->container['parents'] = $parents;

        return $this;
    }

    /**
     * Gets sha
     *
     * @return string|null
     */
    public function getSha()
    {
        return $this->container['sha'];
    }

    /**
     * Sets sha
     *
     * @param string|null $sha sha
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
     * Gets stats
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitStats|null
     */
    public function getStats()
    {
        return $this->container['stats'];
    }

    /**
     * Sets stats
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\CommitStats|null $stats stats
     *
     * @return self
     */
    public function setStats($stats)
    {
        if (is_null($stats)) {
            throw new \InvalidArgumentException('non-nullable stats cannot be null');
        }
        $this->container['stats'] = $stats;

        return $this;
    }

    /**
     * Gets url
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string|null $url url
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


