<?php
/**
 * CreateFileOptions
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
 * CreateFileOptions Class Doc Comment
 *
 * @category Class
 * @description CreateFileOptions options for creating files Note: &#x60;author&#x60; and &#x60;committer&#x60; are optional (if only one is given, it will be used for the other, otherwise the authenticated user will be used)
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreateFileOptions implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'CreateFileOptions';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'author' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\Identity',
        'branch' => 'string',
        'committer' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\Identity',
        'content' => 'string',
        'dates' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitDateOptions',
        'message' => 'string',
        'new_branch' => 'string',
        'signoff' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'author' => null,
        'branch' => null,
        'committer' => null,
        'content' => null,
        'dates' => null,
        'message' => null,
        'new_branch' => null,
        'signoff' => null
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
        'branch' => 'branch',
        'committer' => 'committer',
        'content' => 'content',
        'dates' => 'dates',
        'message' => 'message',
        'new_branch' => 'new_branch',
        'signoff' => 'signoff'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'author' => 'setAuthor',
        'branch' => 'setBranch',
        'committer' => 'setCommitter',
        'content' => 'setContent',
        'dates' => 'setDates',
        'message' => 'setMessage',
        'new_branch' => 'setNewBranch',
        'signoff' => 'setSignoff'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'author' => 'getAuthor',
        'branch' => 'getBranch',
        'committer' => 'getCommitter',
        'content' => 'getContent',
        'dates' => 'getDates',
        'message' => 'getMessage',
        'new_branch' => 'getNewBranch',
        'signoff' => 'getSignoff'
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
        $this->container['branch'] = isset($data['branch']) ? $data['branch'] : null;
        $this->container['committer'] = isset($data['committer']) ? $data['committer'] : null;
        $this->container['content'] = isset($data['content']) ? $data['content'] : null;
        $this->container['dates'] = isset($data['dates']) ? $data['dates'] : null;
        $this->container['message'] = isset($data['message']) ? $data['message'] : null;
        $this->container['new_branch'] = isset($data['new_branch']) ? $data['new_branch'] : null;
        $this->container['signoff'] = isset($data['signoff']) ? $data['signoff'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['content'] === null) {
            $invalidProperties[] = "'content' can't be null";
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
     * Gets author
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\Identity
     */
    public function getAuthor()
    {
        return $this->container['author'];
    }

    /**
     * Sets author
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\Identity $author author
     *
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->container['author'] = $author;

        return $this;
    }

    /**
     * Gets branch
     *
     * @return string
     */
    public function getBranch()
    {
        return $this->container['branch'];
    }

    /**
     * Sets branch
     *
     * @param string $branch branch (optional) to base this file from. if not given, the default branch is used
     *
     * @return $this
     */
    public function setBranch($branch)
    {
        $this->container['branch'] = $branch;

        return $this;
    }

    /**
     * Gets committer
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\Identity
     */
    public function getCommitter()
    {
        return $this->container['committer'];
    }

    /**
     * Sets committer
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\Identity $committer committer
     *
     * @return $this
     */
    public function setCommitter($committer)
    {
        $this->container['committer'] = $committer;

        return $this;
    }

    /**
     * Gets content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->container['content'];
    }

    /**
     * Sets content
     *
     * @param string $content content must be base64 encoded
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->container['content'] = $content;

        return $this;
    }

    /**
     * Gets dates
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitDateOptions
     */
    public function getDates()
    {
        return $this->container['dates'];
    }

    /**
     * Sets dates
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\CommitDateOptions $dates dates
     *
     * @return $this
     */
    public function setDates($dates)
    {
        $this->container['dates'] = $dates;

        return $this;
    }

    /**
     * Gets message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->container['message'];
    }

    /**
     * Sets message
     *
     * @param string $message message (optional) for the commit of this file. if not supplied, a default message will be used
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->container['message'] = $message;

        return $this;
    }

    /**
     * Gets new_branch
     *
     * @return string
     */
    public function getNewBranch()
    {
        return $this->container['new_branch'];
    }

    /**
     * Sets new_branch
     *
     * @param string $new_branch new_branch (optional) will make a new branch from `branch` before creating the file
     *
     * @return $this
     */
    public function setNewBranch($new_branch)
    {
        $this->container['new_branch'] = $new_branch;

        return $this;
    }

    /**
     * Gets signoff
     *
     * @return bool
     */
    public function getSignoff()
    {
        return $this->container['signoff'];
    }

    /**
     * Sets signoff
     *
     * @param bool $signoff Add a Signed-off-by trailer by the committer at the end of the commit log message.
     *
     * @return $this
     */
    public function setSignoff($signoff)
    {
        $this->container['signoff'] = $signoff;

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


