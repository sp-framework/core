<?php
/**
 * Commit
 *
 * PHP version 5
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Gitea API.
 *
 * This documentation describes the Gitea API.
 *
 * OpenAPI spec version: 1.19.1
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.32-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model;

use \ArrayAccess;
use \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\ObjectSerializer;

/**
 * Commit Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Commit implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Commit';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'author' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\User',
        'commit' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\RepoCommit',
        'committer' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\User',
        'created' => '\DateTime',
        'files' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitAffectedFiles[]',
        'html_url' => 'string',
        'parents' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitMeta[]',
        'sha' => 'string',
        'stats' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitStats',
        'url' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
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
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
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
        return self::$swaggerModelName;
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
        $this->container['author'] = isset($data['author']) ? $data['author'] : null;
        $this->container['commit'] = isset($data['commit']) ? $data['commit'] : null;
        $this->container['committer'] = isset($data['committer']) ? $data['committer'] : null;
        $this->container['created'] = isset($data['created']) ? $data['created'] : null;
        $this->container['files'] = isset($data['files']) ? $data['files'] : null;
        $this->container['html_url'] = isset($data['html_url']) ? $data['html_url'] : null;
        $this->container['parents'] = isset($data['parents']) ? $data['parents'] : null;
        $this->container['sha'] = isset($data['sha']) ? $data['sha'] : null;
        $this->container['stats'] = isset($data['stats']) ? $data['stats'] : null;
        $this->container['url'] = isset($data['url']) ? $data['url'] : null;
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
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\User
     */
    public function getAuthor()
    {
        return $this->container['author'];
    }

    /**
     * Sets author
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\User $author author
     *
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->container['author'] = $author;

        return $this;
    }

    /**
     * Gets commit
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\RepoCommit
     */
    public function getCommit()
    {
        return $this->container['commit'];
    }

    /**
     * Sets commit
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\RepoCommit $commit commit
     *
     * @return $this
     */
    public function setCommit($commit)
    {
        $this->container['commit'] = $commit;

        return $this;
    }

    /**
     * Gets committer
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\User
     */
    public function getCommitter()
    {
        return $this->container['committer'];
    }

    /**
     * Sets committer
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\User $committer committer
     *
     * @return $this
     */
    public function setCommitter($committer)
    {
        $this->container['committer'] = $committer;

        return $this;
    }

    /**
     * Gets created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->container['created'];
    }

    /**
     * Sets created
     *
     * @param \DateTime $created created
     *
     * @return $this
     */
    public function setCreated($created)
    {
        $this->container['created'] = $created;

        return $this;
    }

    /**
     * Gets files
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitAffectedFiles[]
     */
    public function getFiles()
    {
        return $this->container['files'];
    }

    /**
     * Sets files
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitAffectedFiles[] $files files
     *
     * @return $this
     */
    public function setFiles($files)
    {
        $this->container['files'] = $files;

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
     * @return $this
     */
    public function setHtmlUrl($html_url)
    {
        $this->container['html_url'] = $html_url;

        return $this;
    }

    /**
     * Gets parents
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitMeta[]
     */
    public function getParents()
    {
        return $this->container['parents'];
    }

    /**
     * Sets parents
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitMeta[] $parents parents
     *
     * @return $this
     */
    public function setParents($parents)
    {
        $this->container['parents'] = $parents;

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
     * @return $this
     */
    public function setSha($sha)
    {
        $this->container['sha'] = $sha;

        return $this;
    }

    /**
     * Gets stats
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitStats
     */
    public function getStats()
    {
        return $this->container['stats'];
    }

    /**
     * Sets stats
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitStats $stats stats
     *
     * @return $this
     */
    public function setStats($stats)
    {
        $this->container['stats'] = $stats;

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
     * @return $this
     */
    public function setUrl($url)
    {
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
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
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
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


