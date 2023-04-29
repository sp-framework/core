<?php
/**
 * MergePullRequestOption
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
 * MergePullRequestOption Class Doc Comment
 *
 * @category Class
 * @description MergePullRequestForm form for merging Pull Request
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos\Gitea
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class MergePullRequestOption implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'MergePullRequestOption';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'do' => 'string',
        'merge_commit_id' => 'string',
        'merge_message_field' => 'string',
        'merge_title_field' => 'string',
        'delete_branch_after_merge' => 'bool',
        'force_merge' => 'bool',
        'head_commit_id' => 'string',
        'merge_when_checks_succeed' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'do' => null,
        'merge_commit_id' => null,
        'merge_message_field' => null,
        'merge_title_field' => null,
        'delete_branch_after_merge' => null,
        'force_merge' => null,
        'head_commit_id' => null,
        'merge_when_checks_succeed' => null
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
        'do' => 'Do',
        'merge_commit_id' => 'MergeCommitID',
        'merge_message_field' => 'MergeMessageField',
        'merge_title_field' => 'MergeTitleField',
        'delete_branch_after_merge' => 'delete_branch_after_merge',
        'force_merge' => 'force_merge',
        'head_commit_id' => 'head_commit_id',
        'merge_when_checks_succeed' => 'merge_when_checks_succeed'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'do' => 'setDo',
        'merge_commit_id' => 'setMergeCommitId',
        'merge_message_field' => 'setMergeMessageField',
        'merge_title_field' => 'setMergeTitleField',
        'delete_branch_after_merge' => 'setDeleteBranchAfterMerge',
        'force_merge' => 'setForceMerge',
        'head_commit_id' => 'setHeadCommitId',
        'merge_when_checks_succeed' => 'setMergeWhenChecksSucceed'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'do' => 'getDo',
        'merge_commit_id' => 'getMergeCommitId',
        'merge_message_field' => 'getMergeMessageField',
        'merge_title_field' => 'getMergeTitleField',
        'delete_branch_after_merge' => 'getDeleteBranchAfterMerge',
        'force_merge' => 'getForceMerge',
        'head_commit_id' => 'getHeadCommitId',
        'merge_when_checks_succeed' => 'getMergeWhenChecksSucceed'
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

    const MODEL_DO_MERGE = 'merge';
    const MODEL_DO_REBASE = 'rebase';
    const MODEL_DO_REBASE_MERGE = 'rebase-merge';
    const MODEL_DO_SQUASH = 'squash';
    const MODEL_DO_MANUALLY_MERGED = 'manually-merged';



    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getDoAllowableValues()
    {
        return [
            self::MODEL_DO_MERGE,
            self::MODEL_DO_REBASE,
            self::MODEL_DO_REBASE_MERGE,
            self::MODEL_DO_SQUASH,
            self::MODEL_DO_MANUALLY_MERGED,
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
        $this->container['do'] = isset($data['do']) ? $data['do'] : null;
        $this->container['merge_commit_id'] = isset($data['merge_commit_id']) ? $data['merge_commit_id'] : null;
        $this->container['merge_message_field'] = isset($data['merge_message_field']) ? $data['merge_message_field'] : null;
        $this->container['merge_title_field'] = isset($data['merge_title_field']) ? $data['merge_title_field'] : null;
        $this->container['delete_branch_after_merge'] = isset($data['delete_branch_after_merge']) ? $data['delete_branch_after_merge'] : null;
        $this->container['force_merge'] = isset($data['force_merge']) ? $data['force_merge'] : null;
        $this->container['head_commit_id'] = isset($data['head_commit_id']) ? $data['head_commit_id'] : null;
        $this->container['merge_when_checks_succeed'] = isset($data['merge_when_checks_succeed']) ? $data['merge_when_checks_succeed'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['do'] === null) {
            $invalidProperties[] = "'do' can't be null";
        }
        $allowedValues = $this->getDoAllowableValues();
        if (!is_null($this->container['do']) && !in_array($this->container['do'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'do', must be one of '%s'",
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
     * Gets do
     *
     * @return string
     */
    public function getDo()
    {
        return $this->container['do'];
    }

    /**
     * Sets do
     *
     * @param string $do do
     *
     * @return $this
     */
    public function setDo($do)
    {
        $allowedValues = $this->getDoAllowableValues();
        if (!in_array($do, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'do', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['do'] = $do;

        return $this;
    }

    /**
     * Gets merge_commit_id
     *
     * @return string
     */
    public function getMergeCommitId()
    {
        return $this->container['merge_commit_id'];
    }

    /**
     * Sets merge_commit_id
     *
     * @param string $merge_commit_id merge_commit_id
     *
     * @return $this
     */
    public function setMergeCommitId($merge_commit_id)
    {
        $this->container['merge_commit_id'] = $merge_commit_id;

        return $this;
    }

    /**
     * Gets merge_message_field
     *
     * @return string
     */
    public function getMergeMessageField()
    {
        return $this->container['merge_message_field'];
    }

    /**
     * Sets merge_message_field
     *
     * @param string $merge_message_field merge_message_field
     *
     * @return $this
     */
    public function setMergeMessageField($merge_message_field)
    {
        $this->container['merge_message_field'] = $merge_message_field;

        return $this;
    }

    /**
     * Gets merge_title_field
     *
     * @return string
     */
    public function getMergeTitleField()
    {
        return $this->container['merge_title_field'];
    }

    /**
     * Sets merge_title_field
     *
     * @param string $merge_title_field merge_title_field
     *
     * @return $this
     */
    public function setMergeTitleField($merge_title_field)
    {
        $this->container['merge_title_field'] = $merge_title_field;

        return $this;
    }

    /**
     * Gets delete_branch_after_merge
     *
     * @return bool
     */
    public function getDeleteBranchAfterMerge()
    {
        return $this->container['delete_branch_after_merge'];
    }

    /**
     * Sets delete_branch_after_merge
     *
     * @param bool $delete_branch_after_merge delete_branch_after_merge
     *
     * @return $this
     */
    public function setDeleteBranchAfterMerge($delete_branch_after_merge)
    {
        $this->container['delete_branch_after_merge'] = $delete_branch_after_merge;

        return $this;
    }

    /**
     * Gets force_merge
     *
     * @return bool
     */
    public function getForceMerge()
    {
        return $this->container['force_merge'];
    }

    /**
     * Sets force_merge
     *
     * @param bool $force_merge force_merge
     *
     * @return $this
     */
    public function setForceMerge($force_merge)
    {
        $this->container['force_merge'] = $force_merge;

        return $this;
    }

    /**
     * Gets head_commit_id
     *
     * @return string
     */
    public function getHeadCommitId()
    {
        return $this->container['head_commit_id'];
    }

    /**
     * Sets head_commit_id
     *
     * @param string $head_commit_id head_commit_id
     *
     * @return $this
     */
    public function setHeadCommitId($head_commit_id)
    {
        $this->container['head_commit_id'] = $head_commit_id;

        return $this;
    }

    /**
     * Gets merge_when_checks_succeed
     *
     * @return bool
     */
    public function getMergeWhenChecksSucceed()
    {
        return $this->container['merge_when_checks_succeed'];
    }

    /**
     * Sets merge_when_checks_succeed
     *
     * @param bool $merge_when_checks_succeed merge_when_checks_succeed
     *
     * @return $this
     */
    public function setMergeWhenChecksSucceed($merge_when_checks_succeed)
    {
        $this->container['merge_when_checks_succeed'] = $merge_when_checks_succeed;

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


