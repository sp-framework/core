<?php
/**
 * ExternalTracker
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
 * ExternalTracker Class Doc Comment
 *
 * @category Class
 * @description ExternalTracker represents settings for external tracker
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class ExternalTracker implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'ExternalTracker';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'external_tracker_format' => 'string',
        'external_tracker_regexp_pattern' => 'string',
        'external_tracker_style' => 'string',
        'external_tracker_url' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'external_tracker_format' => null,
        'external_tracker_regexp_pattern' => null,
        'external_tracker_style' => null,
        'external_tracker_url' => null
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
        'external_tracker_format' => 'external_tracker_format',
        'external_tracker_regexp_pattern' => 'external_tracker_regexp_pattern',
        'external_tracker_style' => 'external_tracker_style',
        'external_tracker_url' => 'external_tracker_url'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'external_tracker_format' => 'setExternalTrackerFormat',
        'external_tracker_regexp_pattern' => 'setExternalTrackerRegexpPattern',
        'external_tracker_style' => 'setExternalTrackerStyle',
        'external_tracker_url' => 'setExternalTrackerUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'external_tracker_format' => 'getExternalTrackerFormat',
        'external_tracker_regexp_pattern' => 'getExternalTrackerRegexpPattern',
        'external_tracker_style' => 'getExternalTrackerStyle',
        'external_tracker_url' => 'getExternalTrackerUrl'
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
        $this->container['external_tracker_format'] = isset($data['external_tracker_format']) ? $data['external_tracker_format'] : null;
        $this->container['external_tracker_regexp_pattern'] = isset($data['external_tracker_regexp_pattern']) ? $data['external_tracker_regexp_pattern'] : null;
        $this->container['external_tracker_style'] = isset($data['external_tracker_style']) ? $data['external_tracker_style'] : null;
        $this->container['external_tracker_url'] = isset($data['external_tracker_url']) ? $data['external_tracker_url'] : null;
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
     * Gets external_tracker_format
     *
     * @return string
     */
    public function getExternalTrackerFormat()
    {
        return $this->container['external_tracker_format'];
    }

    /**
     * Sets external_tracker_format
     *
     * @param string $external_tracker_format External Issue Tracker URL Format. Use the placeholders {user}, {repo} and {index} for the username, repository name and issue index.
     *
     * @return $this
     */
    public function setExternalTrackerFormat($external_tracker_format)
    {
        $this->container['external_tracker_format'] = $external_tracker_format;

        return $this;
    }

    /**
     * Gets external_tracker_regexp_pattern
     *
     * @return string
     */
    public function getExternalTrackerRegexpPattern()
    {
        return $this->container['external_tracker_regexp_pattern'];
    }

    /**
     * Sets external_tracker_regexp_pattern
     *
     * @param string $external_tracker_regexp_pattern External Issue Tracker issue regular expression
     *
     * @return $this
     */
    public function setExternalTrackerRegexpPattern($external_tracker_regexp_pattern)
    {
        $this->container['external_tracker_regexp_pattern'] = $external_tracker_regexp_pattern;

        return $this;
    }

    /**
     * Gets external_tracker_style
     *
     * @return string
     */
    public function getExternalTrackerStyle()
    {
        return $this->container['external_tracker_style'];
    }

    /**
     * Sets external_tracker_style
     *
     * @param string $external_tracker_style External Issue Tracker Number Format, either `numeric`, `alphanumeric`, or `regexp`
     *
     * @return $this
     */
    public function setExternalTrackerStyle($external_tracker_style)
    {
        $this->container['external_tracker_style'] = $external_tracker_style;

        return $this;
    }

    /**
     * Gets external_tracker_url
     *
     * @return string
     */
    public function getExternalTrackerUrl()
    {
        return $this->container['external_tracker_url'];
    }

    /**
     * Sets external_tracker_url
     *
     * @param string $external_tracker_url URL of external issue tracker.
     *
     * @return $this
     */
    public function setExternalTrackerUrl($external_tracker_url)
    {
        $this->container['external_tracker_url'] = $external_tracker_url;

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


