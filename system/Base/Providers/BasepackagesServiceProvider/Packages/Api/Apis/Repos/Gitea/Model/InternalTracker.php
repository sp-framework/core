<?php
/**
 * InternalTracker
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
 * InternalTracker Class Doc Comment
 *
 * @category Class
 * @description InternalTracker represents settings for internal tracker
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class InternalTracker implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'InternalTracker';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'allow_only_contributors_to_track_time' => 'bool',
        'enable_issue_dependencies' => 'bool',
        'enable_time_tracker' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'allow_only_contributors_to_track_time' => null,
        'enable_issue_dependencies' => null,
        'enable_time_tracker' => null
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
        'allow_only_contributors_to_track_time' => 'allow_only_contributors_to_track_time',
        'enable_issue_dependencies' => 'enable_issue_dependencies',
        'enable_time_tracker' => 'enable_time_tracker'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'allow_only_contributors_to_track_time' => 'setAllowOnlyContributorsToTrackTime',
        'enable_issue_dependencies' => 'setEnableIssueDependencies',
        'enable_time_tracker' => 'setEnableTimeTracker'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'allow_only_contributors_to_track_time' => 'getAllowOnlyContributorsToTrackTime',
        'enable_issue_dependencies' => 'getEnableIssueDependencies',
        'enable_time_tracker' => 'getEnableTimeTracker'
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
        $this->container['allow_only_contributors_to_track_time'] = isset($data['allow_only_contributors_to_track_time']) ? $data['allow_only_contributors_to_track_time'] : null;
        $this->container['enable_issue_dependencies'] = isset($data['enable_issue_dependencies']) ? $data['enable_issue_dependencies'] : null;
        $this->container['enable_time_tracker'] = isset($data['enable_time_tracker']) ? $data['enable_time_tracker'] : null;
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
     * Gets allow_only_contributors_to_track_time
     *
     * @return bool
     */
    public function getAllowOnlyContributorsToTrackTime()
    {
        return $this->container['allow_only_contributors_to_track_time'];
    }

    /**
     * Sets allow_only_contributors_to_track_time
     *
     * @param bool $allow_only_contributors_to_track_time Let only contributors track time (Built-in issue tracker)
     *
     * @return $this
     */
    public function setAllowOnlyContributorsToTrackTime($allow_only_contributors_to_track_time)
    {
        $this->container['allow_only_contributors_to_track_time'] = $allow_only_contributors_to_track_time;

        return $this;
    }

    /**
     * Gets enable_issue_dependencies
     *
     * @return bool
     */
    public function getEnableIssueDependencies()
    {
        return $this->container['enable_issue_dependencies'];
    }

    /**
     * Sets enable_issue_dependencies
     *
     * @param bool $enable_issue_dependencies Enable dependencies for issues and pull requests (Built-in issue tracker)
     *
     * @return $this
     */
    public function setEnableIssueDependencies($enable_issue_dependencies)
    {
        $this->container['enable_issue_dependencies'] = $enable_issue_dependencies;

        return $this;
    }

    /**
     * Gets enable_time_tracker
     *
     * @return bool
     */
    public function getEnableTimeTracker()
    {
        return $this->container['enable_time_tracker'];
    }

    /**
     * Sets enable_time_tracker
     *
     * @param bool $enable_time_tracker Enable time tracking (Built-in issue tracker)
     *
     * @return $this
     */
    public function setEnableTimeTracker($enable_time_tracker)
    {
        $this->container['enable_time_tracker'] = $enable_time_tracker;

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


