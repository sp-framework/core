<?php
/**
 * GPGKey
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
 * GPGKey Class Doc Comment
 *
 * @category Class
 * @description GPGKey a user GPG key to sign commit and tag in repository
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class GPGKey implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'GPGKey';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'can_certify' => 'bool',
        'can_encrypt_comms' => 'bool',
        'can_encrypt_storage' => 'bool',
        'can_sign' => 'bool',
        'created_at' => '\DateTime',
        'emails' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\GPGKeyEmail[]',
        'expires_at' => '\DateTime',
        'id' => 'int',
        'key_id' => 'string',
        'primary_key_id' => 'string',
        'public_key' => 'string',
        'subkeys' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\GPGKey[]',
        'verified' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'can_certify' => null,
        'can_encrypt_comms' => null,
        'can_encrypt_storage' => null,
        'can_sign' => null,
        'created_at' => 'date-time',
        'emails' => null,
        'expires_at' => 'date-time',
        'id' => 'int64',
        'key_id' => null,
        'primary_key_id' => null,
        'public_key' => null,
        'subkeys' => null,
        'verified' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'can_certify' => false,
        'can_encrypt_comms' => false,
        'can_encrypt_storage' => false,
        'can_sign' => false,
        'created_at' => false,
        'emails' => false,
        'expires_at' => false,
        'id' => false,
        'key_id' => false,
        'primary_key_id' => false,
        'public_key' => false,
        'subkeys' => false,
        'verified' => false
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
        'can_certify' => 'can_certify',
        'can_encrypt_comms' => 'can_encrypt_comms',
        'can_encrypt_storage' => 'can_encrypt_storage',
        'can_sign' => 'can_sign',
        'created_at' => 'created_at',
        'emails' => 'emails',
        'expires_at' => 'expires_at',
        'id' => 'id',
        'key_id' => 'key_id',
        'primary_key_id' => 'primary_key_id',
        'public_key' => 'public_key',
        'subkeys' => 'subkeys',
        'verified' => 'verified'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'can_certify' => 'setCanCertify',
        'can_encrypt_comms' => 'setCanEncryptComms',
        'can_encrypt_storage' => 'setCanEncryptStorage',
        'can_sign' => 'setCanSign',
        'created_at' => 'setCreatedAt',
        'emails' => 'setEmails',
        'expires_at' => 'setExpiresAt',
        'id' => 'setId',
        'key_id' => 'setKeyId',
        'primary_key_id' => 'setPrimaryKeyId',
        'public_key' => 'setPublicKey',
        'subkeys' => 'setSubkeys',
        'verified' => 'setVerified'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'can_certify' => 'getCanCertify',
        'can_encrypt_comms' => 'getCanEncryptComms',
        'can_encrypt_storage' => 'getCanEncryptStorage',
        'can_sign' => 'getCanSign',
        'created_at' => 'getCreatedAt',
        'emails' => 'getEmails',
        'expires_at' => 'getExpiresAt',
        'id' => 'getId',
        'key_id' => 'getKeyId',
        'primary_key_id' => 'getPrimaryKeyId',
        'public_key' => 'getPublicKey',
        'subkeys' => 'getSubkeys',
        'verified' => 'getVerified'
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
        $this->setIfExists('can_certify', $data ?? [], null);
        $this->setIfExists('can_encrypt_comms', $data ?? [], null);
        $this->setIfExists('can_encrypt_storage', $data ?? [], null);
        $this->setIfExists('can_sign', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('emails', $data ?? [], null);
        $this->setIfExists('expires_at', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('key_id', $data ?? [], null);
        $this->setIfExists('primary_key_id', $data ?? [], null);
        $this->setIfExists('public_key', $data ?? [], null);
        $this->setIfExists('subkeys', $data ?? [], null);
        $this->setIfExists('verified', $data ?? [], null);
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
     * Gets can_certify
     *
     * @return bool|null
     */
    public function getCanCertify()
    {
        return $this->container['can_certify'];
    }

    /**
     * Sets can_certify
     *
     * @param bool|null $can_certify can_certify
     *
     * @return self
     */
    public function setCanCertify($can_certify)
    {
        if (is_null($can_certify)) {
            throw new \InvalidArgumentException('non-nullable can_certify cannot be null');
        }
        $this->container['can_certify'] = $can_certify;

        return $this;
    }

    /**
     * Gets can_encrypt_comms
     *
     * @return bool|null
     */
    public function getCanEncryptComms()
    {
        return $this->container['can_encrypt_comms'];
    }

    /**
     * Sets can_encrypt_comms
     *
     * @param bool|null $can_encrypt_comms can_encrypt_comms
     *
     * @return self
     */
    public function setCanEncryptComms($can_encrypt_comms)
    {
        if (is_null($can_encrypt_comms)) {
            throw new \InvalidArgumentException('non-nullable can_encrypt_comms cannot be null');
        }
        $this->container['can_encrypt_comms'] = $can_encrypt_comms;

        return $this;
    }

    /**
     * Gets can_encrypt_storage
     *
     * @return bool|null
     */
    public function getCanEncryptStorage()
    {
        return $this->container['can_encrypt_storage'];
    }

    /**
     * Sets can_encrypt_storage
     *
     * @param bool|null $can_encrypt_storage can_encrypt_storage
     *
     * @return self
     */
    public function setCanEncryptStorage($can_encrypt_storage)
    {
        if (is_null($can_encrypt_storage)) {
            throw new \InvalidArgumentException('non-nullable can_encrypt_storage cannot be null');
        }
        $this->container['can_encrypt_storage'] = $can_encrypt_storage;

        return $this;
    }

    /**
     * Gets can_sign
     *
     * @return bool|null
     */
    public function getCanSign()
    {
        return $this->container['can_sign'];
    }

    /**
     * Sets can_sign
     *
     * @param bool|null $can_sign can_sign
     *
     * @return self
     */
    public function setCanSign($can_sign)
    {
        if (is_null($can_sign)) {
            throw new \InvalidArgumentException('non-nullable can_sign cannot be null');
        }
        $this->container['can_sign'] = $can_sign;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param \DateTime|null $created_at created_at
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
     * Gets emails
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\GPGKeyEmail[]|null
     */
    public function getEmails()
    {
        return $this->container['emails'];
    }

    /**
     * Sets emails
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\GPGKeyEmail[]|null $emails emails
     *
     * @return self
     */
    public function setEmails($emails)
    {
        if (is_null($emails)) {
            throw new \InvalidArgumentException('non-nullable emails cannot be null');
        }
        $this->container['emails'] = $emails;

        return $this;
    }

    /**
     * Gets expires_at
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->container['expires_at'];
    }

    /**
     * Sets expires_at
     *
     * @param \DateTime|null $expires_at expires_at
     *
     * @return self
     */
    public function setExpiresAt($expires_at)
    {
        if (is_null($expires_at)) {
            throw new \InvalidArgumentException('non-nullable expires_at cannot be null');
        }
        $this->container['expires_at'] = $expires_at;

        return $this;
    }

    /**
     * Gets id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param int|null $id id
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
     * Gets key_id
     *
     * @return string|null
     */
    public function getKeyId()
    {
        return $this->container['key_id'];
    }

    /**
     * Sets key_id
     *
     * @param string|null $key_id key_id
     *
     * @return self
     */
    public function setKeyId($key_id)
    {
        if (is_null($key_id)) {
            throw new \InvalidArgumentException('non-nullable key_id cannot be null');
        }
        $this->container['key_id'] = $key_id;

        return $this;
    }

    /**
     * Gets primary_key_id
     *
     * @return string|null
     */
    public function getPrimaryKeyId()
    {
        return $this->container['primary_key_id'];
    }

    /**
     * Sets primary_key_id
     *
     * @param string|null $primary_key_id primary_key_id
     *
     * @return self
     */
    public function setPrimaryKeyId($primary_key_id)
    {
        if (is_null($primary_key_id)) {
            throw new \InvalidArgumentException('non-nullable primary_key_id cannot be null');
        }
        $this->container['primary_key_id'] = $primary_key_id;

        return $this;
    }

    /**
     * Gets public_key
     *
     * @return string|null
     */
    public function getPublicKey()
    {
        return $this->container['public_key'];
    }

    /**
     * Sets public_key
     *
     * @param string|null $public_key public_key
     *
     * @return self
     */
    public function setPublicKey($public_key)
    {
        if (is_null($public_key)) {
            throw new \InvalidArgumentException('non-nullable public_key cannot be null');
        }
        $this->container['public_key'] = $public_key;

        return $this;
    }

    /**
     * Gets subkeys
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\GPGKey[]|null
     */
    public function getSubkeys()
    {
        return $this->container['subkeys'];
    }

    /**
     * Sets subkeys
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\Model\GPGKey[]|null $subkeys subkeys
     *
     * @return self
     */
    public function setSubkeys($subkeys)
    {
        if (is_null($subkeys)) {
            throw new \InvalidArgumentException('non-nullable subkeys cannot be null');
        }
        $this->container['subkeys'] = $subkeys;

        return $this;
    }

    /**
     * Gets verified
     *
     * @return bool|null
     */
    public function getVerified()
    {
        return $this->container['verified'];
    }

    /**
     * Sets verified
     *
     * @param bool|null $verified verified
     *
     * @return self
     */
    public function setVerified($verified)
    {
        if (is_null($verified)) {
            throw new \InvalidArgumentException('non-nullable verified cannot be null');
        }
        $this->container['verified'] = $verified;

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


