<?php
/**
 * EditTeamOption
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
 * EditTeamOption Class Doc Comment
 *
 * @category Class
 * @description EditTeamOption options for editing a team
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class EditTeamOption implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'EditTeamOption';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'can_create_org_repo' => 'bool',
        'description' => 'string',
        'includes_all_repositories' => 'bool',
        'name' => 'string',
        'permission' => 'string',
        'units' => 'string[]',
        'units_map' => 'map[string,string]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'can_create_org_repo' => null,
        'description' => null,
        'includes_all_repositories' => null,
        'name' => null,
        'permission' => null,
        'units' => null,
        'units_map' => null
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
        'can_create_org_repo' => 'can_create_org_repo',
        'description' => 'description',
        'includes_all_repositories' => 'includes_all_repositories',
        'name' => 'name',
        'permission' => 'permission',
        'units' => 'units',
        'units_map' => 'units_map'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'can_create_org_repo' => 'setCanCreateOrgRepo',
        'description' => 'setDescription',
        'includes_all_repositories' => 'setIncludesAllRepositories',
        'name' => 'setName',
        'permission' => 'setPermission',
        'units' => 'setUnits',
        'units_map' => 'setUnitsMap'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'can_create_org_repo' => 'getCanCreateOrgRepo',
        'description' => 'getDescription',
        'includes_all_repositories' => 'getIncludesAllRepositories',
        'name' => 'getName',
        'permission' => 'getPermission',
        'units' => 'getUnits',
        'units_map' => 'getUnitsMap'
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

    const PERMISSION_READ = 'read';
    const PERMISSION_WRITE = 'write';
    const PERMISSION_ADMIN = 'admin';



    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPermissionAllowableValues()
    {
        return [
            self::PERMISSION_READ,
            self::PERMISSION_WRITE,
            self::PERMISSION_ADMIN,
        ];
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
        $this->container['can_create_org_repo'] = isset($data['can_create_org_repo']) ? $data['can_create_org_repo'] : null;
        $this->container['description'] = isset($data['description']) ? $data['description'] : null;
        $this->container['includes_all_repositories'] = isset($data['includes_all_repositories']) ? $data['includes_all_repositories'] : null;
        $this->container['name'] = isset($data['name']) ? $data['name'] : null;
        $this->container['permission'] = isset($data['permission']) ? $data['permission'] : null;
        $this->container['units'] = isset($data['units']) ? $data['units'] : null;
        $this->container['units_map'] = isset($data['units_map']) ? $data['units_map'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        $allowedValues = $this->getPermissionAllowableValues();
        if (!is_null($this->container['permission']) && !in_array($this->container['permission'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'permission', must be one of '%s'",
                implode("', '", $allowedValues)
            );
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
     * Gets can_create_org_repo
     *
     * @return bool
     */
    public function getCanCreateOrgRepo()
    {
        return $this->container['can_create_org_repo'];
    }

    /**
     * Sets can_create_org_repo
     *
     * @param bool $can_create_org_repo can_create_org_repo
     *
     * @return $this
     */
    public function setCanCreateOrgRepo($can_create_org_repo)
    {
        $this->container['can_create_org_repo'] = $can_create_org_repo;

        return $this;
    }

    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->container['description'];
    }

    /**
     * Sets description
     *
     * @param string $description description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets includes_all_repositories
     *
     * @return bool
     */
    public function getIncludesAllRepositories()
    {
        return $this->container['includes_all_repositories'];
    }

    /**
     * Sets includes_all_repositories
     *
     * @param bool $includes_all_repositories includes_all_repositories
     *
     * @return $this
     */
    public function setIncludesAllRepositories($includes_all_repositories)
    {
        $this->container['includes_all_repositories'] = $includes_all_repositories;

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
     * Gets permission
     *
     * @return string
     */
    public function getPermission()
    {
        return $this->container['permission'];
    }

    /**
     * Sets permission
     *
     * @param string $permission permission
     *
     * @return $this
     */
    public function setPermission($permission)
    {
        $allowedValues = $this->getPermissionAllowableValues();
        if (!is_null($permission) && !in_array($permission, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'permission', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['permission'] = $permission;

        return $this;
    }

    /**
     * Gets units
     *
     * @return string[]
     */
    public function getUnits()
    {
        return $this->container['units'];
    }

    /**
     * Sets units
     *
     * @param string[] $units units
     *
     * @return $this
     */
    public function setUnits($units)
    {
        $this->container['units'] = $units;

        return $this;
    }

    /**
     * Gets units_map
     *
     * @return map[string,string]
     */
    public function getUnitsMap()
    {
        return $this->container['units_map'];
    }

    /**
     * Sets units_map
     *
     * @param map[string,string] $units_map units_map
     *
     * @return $this
     */
    public function setUnitsMap($units_map)
    {
        $this->container['units_map'] = $units_map;

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


