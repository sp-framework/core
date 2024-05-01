<?php
/**
 * EditIssueOption
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
 * EditIssueOption Class Doc Comment
 *
 * @category Class
 * @description EditIssueOption options for editing an issue
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class EditIssueOption implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'EditIssueOption';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'assignee' => 'string',
        'assignees' => 'string[]',
        'body' => 'string',
        'due_date' => '\DateTime',
        'milestone' => 'int',
        'ref' => 'string',
        'state' => 'string',
        'title' => 'string',
        'unset_due_date' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'assignee' => null,
        'assignees' => null,
        'body' => null,
        'due_date' => 'date-time',
        'milestone' => 'int64',
        'ref' => null,
        'state' => null,
        'title' => null,
        'unset_due_date' => null
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
        'assignee' => 'assignee',
        'assignees' => 'assignees',
        'body' => 'body',
        'due_date' => 'due_date',
        'milestone' => 'milestone',
        'ref' => 'ref',
        'state' => 'state',
        'title' => 'title',
        'unset_due_date' => 'unset_due_date'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'assignee' => 'setAssignee',
        'assignees' => 'setAssignees',
        'body' => 'setBody',
        'due_date' => 'setDueDate',
        'milestone' => 'setMilestone',
        'ref' => 'setRef',
        'state' => 'setState',
        'title' => 'setTitle',
        'unset_due_date' => 'setUnsetDueDate'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'assignee' => 'getAssignee',
        'assignees' => 'getAssignees',
        'body' => 'getBody',
        'due_date' => 'getDueDate',
        'milestone' => 'getMilestone',
        'ref' => 'getRef',
        'state' => 'getState',
        'title' => 'getTitle',
        'unset_due_date' => 'getUnsetDueDate'
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
        $this->container['assignee'] = isset($data['assignee']) ? $data['assignee'] : null;
        $this->container['assignees'] = isset($data['assignees']) ? $data['assignees'] : null;
        $this->container['body'] = isset($data['body']) ? $data['body'] : null;
        $this->container['due_date'] = isset($data['due_date']) ? $data['due_date'] : null;
        $this->container['milestone'] = isset($data['milestone']) ? $data['milestone'] : null;
        $this->container['ref'] = isset($data['ref']) ? $data['ref'] : null;
        $this->container['state'] = isset($data['state']) ? $data['state'] : null;
        $this->container['title'] = isset($data['title']) ? $data['title'] : null;
        $this->container['unset_due_date'] = isset($data['unset_due_date']) ? $data['unset_due_date'] : null;
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
     * Gets assignee
     *
     * @return string
     */
    public function getAssignee()
    {
        return $this->container['assignee'];
    }

    /**
     * Sets assignee
     *
     * @param string $assignee deprecated
     *
     * @return $this
     */
    public function setAssignee($assignee)
    {
        $this->container['assignee'] = $assignee;

        return $this;
    }

    /**
     * Gets assignees
     *
     * @return string[]
     */
    public function getAssignees()
    {
        return $this->container['assignees'];
    }

    /**
     * Sets assignees
     *
     * @param string[] $assignees assignees
     *
     * @return $this
     */
    public function setAssignees($assignees)
    {
        $this->container['assignees'] = $assignees;

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
     * @return $this
     */
    public function setBody($body)
    {
        $this->container['body'] = $body;

        return $this;
    }

    /**
     * Gets due_date
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->container['due_date'];
    }

    /**
     * Sets due_date
     *
     * @param \DateTime $due_date due_date
     *
     * @return $this
     */
    public function setDueDate($due_date)
    {
        $this->container['due_date'] = $due_date;

        return $this;
    }

    /**
     * Gets milestone
     *
     * @return int
     */
    public function getMilestone()
    {
        return $this->container['milestone'];
    }

    /**
     * Sets milestone
     *
     * @param int $milestone milestone
     *
     * @return $this
     */
    public function setMilestone($milestone)
    {
        $this->container['milestone'] = $milestone;

        return $this;
    }

    /**
     * Gets ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->container['ref'];
    }

    /**
     * Sets ref
     *
     * @param string $ref ref
     *
     * @return $this
     */
    public function setRef($ref)
    {
        $this->container['ref'] = $ref;

        return $this;
    }

    /**
     * Gets state
     *
     * @return string
     */
    public function getState()
    {
        return $this->container['state'];
    }

    /**
     * Sets state
     *
     * @param string $state state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->container['state'] = $state;

        return $this;
    }

    /**
     * Gets title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->container['title'];
    }

    /**
     * Sets title
     *
     * @param string $title title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->container['title'] = $title;

        return $this;
    }

    /**
     * Gets unset_due_date
     *
     * @return bool
     */
    public function getUnsetDueDate()
    {
        return $this->container['unset_due_date'];
    }

    /**
     * Sets unset_due_date
     *
     * @param bool $unset_due_date unset_due_date
     *
     * @return $this
     */
    public function setUnsetDueDate($unset_due_date)
    {
        $this->container['unset_due_date'] = $unset_due_date;

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


