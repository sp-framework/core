<?php
/**
 * CreateUserOption
 *
 * PHP version 5
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Gitea API.
 *
 * This documentation describes the Gitea API.
 *
 * OpenAPI spec version: 1.21.7
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.32-SNAPSHOT
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model;

use \ArrayAccess;
use \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Base\ObjectSerializer;

/**
 * CreateUserOption Class Doc Comment
 *
 * @category Class
 * @description CreateUserOption create user options
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreateUserOption implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'CreateUserOption';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'created_at' => '\DateTime',
        'email' => 'string',
        'full_name' => 'string',
        'login_name' => 'string',
        'must_change_password' => 'bool',
        'password' => 'string',
        'restricted' => 'bool',
        'send_notify' => 'bool',
        'source_id' => 'int',
        'username' => 'string',
        'visibility' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'created_at' => 'date-time',
        'email' => 'email',
        'full_name' => null,
        'login_name' => null,
        'must_change_password' => null,
        'password' => null,
        'restricted' => null,
        'send_notify' => null,
        'source_id' => 'int64',
        'username' => null,
        'visibility' => null
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
        'created_at' => 'created_at',
        'email' => 'email',
        'full_name' => 'full_name',
        'login_name' => 'login_name',
        'must_change_password' => 'must_change_password',
        'password' => 'password',
        'restricted' => 'restricted',
        'send_notify' => 'send_notify',
        'source_id' => 'source_id',
        'username' => 'username',
        'visibility' => 'visibility'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'created_at' => 'setCreatedAt',
        'email' => 'setEmail',
        'full_name' => 'setFullName',
        'login_name' => 'setLoginName',
        'must_change_password' => 'setMustChangePassword',
        'password' => 'setPassword',
        'restricted' => 'setRestricted',
        'send_notify' => 'setSendNotify',
        'source_id' => 'setSourceId',
        'username' => 'setUsername',
        'visibility' => 'setVisibility'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'created_at' => 'getCreatedAt',
        'email' => 'getEmail',
        'full_name' => 'getFullName',
        'login_name' => 'getLoginName',
        'must_change_password' => 'getMustChangePassword',
        'password' => 'getPassword',
        'restricted' => 'getRestricted',
        'send_notify' => 'getSendNotify',
        'source_id' => 'getSourceId',
        'username' => 'getUsername',
        'visibility' => 'getVisibility'
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
        $this->container['created_at'] = isset($data['created_at']) ? $data['created_at'] : null;
        $this->container['email'] = isset($data['email']) ? $data['email'] : null;
        $this->container['full_name'] = isset($data['full_name']) ? $data['full_name'] : null;
        $this->container['login_name'] = isset($data['login_name']) ? $data['login_name'] : null;
        $this->container['must_change_password'] = isset($data['must_change_password']) ? $data['must_change_password'] : null;
        $this->container['password'] = isset($data['password']) ? $data['password'] : null;
        $this->container['restricted'] = isset($data['restricted']) ? $data['restricted'] : null;
        $this->container['send_notify'] = isset($data['send_notify']) ? $data['send_notify'] : null;
        $this->container['source_id'] = isset($data['source_id']) ? $data['source_id'] : null;
        $this->container['username'] = isset($data['username']) ? $data['username'] : null;
        $this->container['visibility'] = isset($data['visibility']) ? $data['visibility'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['email'] === null) {
            $invalidProperties[] = "'email' can't be null";
        }
        if ($this->container['username'] === null) {
            $invalidProperties[] = "'username' can't be null";
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
     * @param \DateTime $created_at For explicitly setting the user creation timestamp. Useful when users are migrated from other systems. When omitted, the user's creation timestamp will be set to \"now\".
     *
     * @return $this
     */
    public function setCreatedAt($created_at)
    {
        $this->container['created_at'] = $created_at;

        return $this;
    }

    /**
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->container['email'];
    }

    /**
     * Sets email
     *
     * @param string $email email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->container['email'] = $email;

        return $this;
    }

    /**
     * Gets full_name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->container['full_name'];
    }

    /**
     * Sets full_name
     *
     * @param string $full_name full_name
     *
     * @return $this
     */
    public function setFullName($full_name)
    {
        $this->container['full_name'] = $full_name;

        return $this;
    }

    /**
     * Gets login_name
     *
     * @return string
     */
    public function getLoginName()
    {
        return $this->container['login_name'];
    }

    /**
     * Sets login_name
     *
     * @param string $login_name login_name
     *
     * @return $this
     */
    public function setLoginName($login_name)
    {
        $this->container['login_name'] = $login_name;

        return $this;
    }

    /**
     * Gets must_change_password
     *
     * @return bool
     */
    public function getMustChangePassword()
    {
        return $this->container['must_change_password'];
    }

    /**
     * Sets must_change_password
     *
     * @param bool $must_change_password must_change_password
     *
     * @return $this
     */
    public function setMustChangePassword($must_change_password)
    {
        $this->container['must_change_password'] = $must_change_password;

        return $this;
    }

    /**
     * Gets password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->container['password'];
    }

    /**
     * Sets password
     *
     * @param string $password password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->container['password'] = $password;

        return $this;
    }

    /**
     * Gets restricted
     *
     * @return bool
     */
    public function getRestricted()
    {
        return $this->container['restricted'];
    }

    /**
     * Sets restricted
     *
     * @param bool $restricted restricted
     *
     * @return $this
     */
    public function setRestricted($restricted)
    {
        $this->container['restricted'] = $restricted;

        return $this;
    }

    /**
     * Gets send_notify
     *
     * @return bool
     */
    public function getSendNotify()
    {
        return $this->container['send_notify'];
    }

    /**
     * Sets send_notify
     *
     * @param bool $send_notify send_notify
     *
     * @return $this
     */
    public function setSendNotify($send_notify)
    {
        $this->container['send_notify'] = $send_notify;

        return $this;
    }

    /**
     * Gets source_id
     *
     * @return int
     */
    public function getSourceId()
    {
        return $this->container['source_id'];
    }

    /**
     * Sets source_id
     *
     * @param int $source_id source_id
     *
     * @return $this
     */
    public function setSourceId($source_id)
    {
        $this->container['source_id'] = $source_id;

        return $this;
    }

    /**
     * Gets username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->container['username'];
    }

    /**
     * Sets username
     *
     * @param string $username username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->container['username'] = $username;

        return $this;
    }

    /**
     * Gets visibility
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->container['visibility'];
    }

    /**
     * Sets visibility
     *
     * @param string $visibility visibility
     *
     * @return $this
     */
    public function setVisibility($visibility)
    {
        $this->container['visibility'] = $visibility;

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


