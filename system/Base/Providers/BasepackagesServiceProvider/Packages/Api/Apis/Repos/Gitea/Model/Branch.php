<?php
/**
 * Branch
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
 * Branch Class Doc Comment
 *
 * @category Class
 * @description Branch represents a repository branch
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Branch implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Branch';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'commit' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\PayloadCommit',
        'effective_branch_protection_name' => 'string',
        'enable_status_check' => 'bool',
        'name' => 'string',
        'protected' => 'bool',
        'required_approvals' => 'int',
        'status_check_contexts' => 'string[]',
        'user_can_merge' => 'bool',
        'user_can_push' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'commit' => null,
        'effective_branch_protection_name' => null,
        'enable_status_check' => null,
        'name' => null,
        'protected' => null,
        'required_approvals' => 'int64',
        'status_check_contexts' => null,
        'user_can_merge' => null,
        'user_can_push' => null
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
        'commit' => 'commit',
        'effective_branch_protection_name' => 'effective_branch_protection_name',
        'enable_status_check' => 'enable_status_check',
        'name' => 'name',
        'protected' => 'protected',
        'required_approvals' => 'required_approvals',
        'status_check_contexts' => 'status_check_contexts',
        'user_can_merge' => 'user_can_merge',
        'user_can_push' => 'user_can_push'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'commit' => 'setCommit',
        'effective_branch_protection_name' => 'setEffectiveBranchProtectionName',
        'enable_status_check' => 'setEnableStatusCheck',
        'name' => 'setName',
        'protected' => 'setProtected',
        'required_approvals' => 'setRequiredApprovals',
        'status_check_contexts' => 'setStatusCheckContexts',
        'user_can_merge' => 'setUserCanMerge',
        'user_can_push' => 'setUserCanPush'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'commit' => 'getCommit',
        'effective_branch_protection_name' => 'getEffectiveBranchProtectionName',
        'enable_status_check' => 'getEnableStatusCheck',
        'name' => 'getName',
        'protected' => 'getProtected',
        'required_approvals' => 'getRequiredApprovals',
        'status_check_contexts' => 'getStatusCheckContexts',
        'user_can_merge' => 'getUserCanMerge',
        'user_can_push' => 'getUserCanPush'
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
        $this->container['commit'] = isset($data['commit']) ? $data['commit'] : null;
        $this->container['effective_branch_protection_name'] = isset($data['effective_branch_protection_name']) ? $data['effective_branch_protection_name'] : null;
        $this->container['enable_status_check'] = isset($data['enable_status_check']) ? $data['enable_status_check'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['protected'] = isset($data['protected']) ? $data['protected'] : null;
        $this->container['required_approvals'] = isset($data['required_approvals']) ? $data['required_approvals'] : null;
        $this->container['status_check_contexts'] = isset($data['status_check_contexts']) ? $data['status_check_contexts'] : null;
        $this->container['user_can_merge'] = isset($data['user_can_merge']) ? $data['user_can_merge'] : null;
        $this->container['user_can_push'] = isset($data['user_can_push']) ? $data['user_can_push'] : null;
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
     * Gets commit
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\PayloadCommit
     */
    public function getCommit()
    {
        return $this->container['commit'];
    }

    /**
     * Sets commit
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea\Model\PayloadCommit $commit commit
     *
     * @return $this
     */
    public function setCommit($commit)
    {
        $this->container['commit'] = $commit;

        return $this;
    }

    /**
     * Gets effective_branch_protection_name
     *
     * @return string
     */
    public function getEffectiveBranchProtectionName()
    {
        return $this->container['effective_branch_protection_name'];
    }

    /**
     * Sets effective_branch_protection_name
     *
     * @param string $effective_branch_protection_name effective_branch_protection_name
     *
     * @return $this
     */
    public function setEffectiveBranchProtectionName($effective_branch_protection_name)
    {
        $this->container['effective_branch_protection_name'] = $effective_branch_protection_name;

        return $this;
    }

    /**
     * Gets enable_status_check
     *
     * @return bool
     */
    public function getEnableStatusCheck()
    {
        return $this->container['enable_status_check'];
    }

    /**
     * Sets enable_status_check
     *
     * @param bool $enable_status_check enable_status_check
     *
     * @return $this
     */
    public function setEnableStatusCheck($enable_status_check)
    {
        $this->container['enable_status_check'] = $enable_status_check;

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
     * @return $this
     */
    public function setName($name)
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets protected
     *
     * @return bool
     */
    public function getProtected()
    {
        return $this->container['protected'];
    }

    /**
     * Sets protected
     *
     * @param bool $protected protected
     *
     * @return $this
     */
    public function setProtected($protected)
    {
        $this->container['protected'] = $protected;

        return $this;
    }

    /**
     * Gets required_approvals
     *
     * @return int
     */
    public function getRequiredApprovals()
    {
        return $this->container['required_approvals'];
    }

    /**
     * Sets required_approvals
     *
     * @param int $required_approvals required_approvals
     *
     * @return $this
     */
    public function setRequiredApprovals($required_approvals)
    {
        $this->container['required_approvals'] = $required_approvals;

        return $this;
    }

    /**
     * Gets status_check_contexts
     *
     * @return string[]
     */
    public function getStatusCheckContexts()
    {
        return $this->container['status_check_contexts'];
    }

    /**
     * Sets status_check_contexts
     *
     * @param string[] $status_check_contexts status_check_contexts
     *
     * @return $this
     */
    public function setStatusCheckContexts($status_check_contexts)
    {
        $this->container['status_check_contexts'] = $status_check_contexts;

        return $this;
    }

    /**
     * Gets user_can_merge
     *
     * @return bool
     */
    public function getUserCanMerge()
    {
        return $this->container['user_can_merge'];
    }

    /**
     * Sets user_can_merge
     *
     * @param bool $user_can_merge user_can_merge
     *
     * @return $this
     */
    public function setUserCanMerge($user_can_merge)
    {
        $this->container['user_can_merge'] = $user_can_merge;

        return $this;
    }

    /**
     * Gets user_can_push
     *
     * @return bool
     */
    public function getUserCanPush()
    {
        return $this->container['user_can_push'];
    }

    /**
     * Sets user_can_push
     *
     * @param bool $user_can_push user_can_push
     *
     * @return $this
     */
    public function setUserCanPush($user_can_push)
    {
        $this->container['user_can_push'] = $user_can_push;

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


