<?php
/**
 * ReposUpdateReleaseRequest
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * GitHub v3 REST API
 *
 * GitHub's v3 REST API.
 *
 * The version of the OpenAPI document: 1.1.4
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.5.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model;

use \ArrayAccess;
use \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\ObjectSerializer;

/**
 * ReposUpdateReleaseRequest Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class ReposUpdateReleaseRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'repos_update_release_request';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'tag_name' => 'string',
        'target_commitish' => 'string',
        'name' => 'string',
        'body' => 'string',
        'draft' => 'bool',
        'prerelease' => 'bool',
        'make_latest' => 'string',
        'discussion_category_name' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'tag_name' => null,
        'target_commitish' => null,
        'name' => null,
        'body' => null,
        'draft' => null,
        'prerelease' => null,
        'make_latest' => null,
        'discussion_category_name' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'tag_name' => false,
        'target_commitish' => false,
        'name' => false,
        'body' => false,
        'draft' => false,
        'prerelease' => false,
        'make_latest' => false,
        'discussion_category_name' => false
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
        'tag_name' => 'tag_name',
        'target_commitish' => 'target_commitish',
        'name' => 'name',
        'body' => 'body',
        'draft' => 'draft',
        'prerelease' => 'prerelease',
        'make_latest' => 'make_latest',
        'discussion_category_name' => 'discussion_category_name'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'tag_name' => 'setTagName',
        'target_commitish' => 'setTargetCommitish',
        'name' => 'setName',
        'body' => 'setBody',
        'draft' => 'setDraft',
        'prerelease' => 'setPrerelease',
        'make_latest' => 'setMakeLatest',
        'discussion_category_name' => 'setDiscussionCategoryName'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'tag_name' => 'getTagName',
        'target_commitish' => 'getTargetCommitish',
        'name' => 'getName',
        'body' => 'getBody',
        'draft' => 'getDraft',
        'prerelease' => 'getPrerelease',
        'make_latest' => 'getMakeLatest',
        'discussion_category_name' => 'getDiscussionCategoryName'
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

    public const MAKE_LATEST_TRUE = 'true';
    public const MAKE_LATEST_FALSE = 'false';
    public const MAKE_LATEST_LEGACY = 'legacy';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getMakeLatestAllowableValues()
    {
        return [
            self::MAKE_LATEST_TRUE,
            self::MAKE_LATEST_FALSE,
            self::MAKE_LATEST_LEGACY,
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
        $this->setIfExists('tag_name', $data ?? [], null);
        $this->setIfExists('target_commitish', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('body', $data ?? [], null);
        $this->setIfExists('draft', $data ?? [], null);
        $this->setIfExists('prerelease', $data ?? [], null);
        $this->setIfExists('make_latest', $data ?? [], 'true');
        $this->setIfExists('discussion_category_name', $data ?? [], null);
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

        $allowedValues = $this->getMakeLatestAllowableValues();
        if (!is_null($this->container['make_latest']) && !in_array($this->container['make_latest'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'make_latest', must be one of '%s'",
                $this->container['make_latest'],
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
     * Gets tag_name
     *
     * @return string|null
     */
    public function getTagName()
    {
        return $this->container['tag_name'];
    }

    /**
     * Sets tag_name
     *
     * @param string|null $tag_name The name of the tag.
     *
     * @return self
     */
    public function setTagName($tag_name)
    {
        if (is_null($tag_name)) {
            throw new \InvalidArgumentException('non-nullable tag_name cannot be null');
        }
        $this->container['tag_name'] = $tag_name;

        return $this;
    }

    /**
     * Gets target_commitish
     *
     * @return string|null
     */
    public function getTargetCommitish()
    {
        return $this->container['target_commitish'];
    }

    /**
     * Sets target_commitish
     *
     * @param string|null $target_commitish Specifies the commitish value that determines where the Git tag is created from. Can be any branch or commit SHA. Unused if the Git tag already exists. Default: the repository's default branch.
     *
     * @return self
     */
    public function setTargetCommitish($target_commitish)
    {
        if (is_null($target_commitish)) {
            throw new \InvalidArgumentException('non-nullable target_commitish cannot be null');
        }
        $this->container['target_commitish'] = $target_commitish;

        return $this;
    }

    /**
     * Gets name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->container['name'];
    }

    /**
     * Sets name
     *
     * @param string|null $name The name of the release.
     *
     * @return self
     */
    public function setName($name)
    {
        if (is_null($name)) {
            throw new \InvalidArgumentException('non-nullable name cannot be null');
        }
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * Gets body
     *
     * @return string|null
     */
    public function getBody()
    {
        return $this->container['body'];
    }

    /**
     * Sets body
     *
     * @param string|null $body Text describing the contents of the tag.
     *
     * @return self
     */
    public function setBody($body)
    {
        if (is_null($body)) {
            throw new \InvalidArgumentException('non-nullable body cannot be null');
        }
        $this->container['body'] = $body;

        return $this;
    }

    /**
     * Gets draft
     *
     * @return bool|null
     */
    public function getDraft()
    {
        return $this->container['draft'];
    }

    /**
     * Sets draft
     *
     * @param bool|null $draft `true` makes the release a draft, and `false` publishes the release.
     *
     * @return self
     */
    public function setDraft($draft)
    {
        if (is_null($draft)) {
            throw new \InvalidArgumentException('non-nullable draft cannot be null');
        }
        $this->container['draft'] = $draft;

        return $this;
    }

    /**
     * Gets prerelease
     *
     * @return bool|null
     */
    public function getPrerelease()
    {
        return $this->container['prerelease'];
    }

    /**
     * Sets prerelease
     *
     * @param bool|null $prerelease `true` to identify the release as a prerelease, `false` to identify the release as a full release.
     *
     * @return self
     */
    public function setPrerelease($prerelease)
    {
        if (is_null($prerelease)) {
            throw new \InvalidArgumentException('non-nullable prerelease cannot be null');
        }
        $this->container['prerelease'] = $prerelease;

        return $this;
    }

    /**
     * Gets make_latest
     *
     * @return string|null
     */
    public function getMakeLatest()
    {
        return $this->container['make_latest'];
    }

    /**
     * Sets make_latest
     *
     * @param string|null $make_latest Specifies whether this release should be set as the latest release for the repository. Drafts and prereleases cannot be set as latest. Defaults to `true` for newly published releases. `legacy` specifies that the latest release should be determined based on the release creation date and higher semantic version.
     *
     * @return self
     */
    public function setMakeLatest($make_latest)
    {
        if (is_null($make_latest)) {
            throw new \InvalidArgumentException('non-nullable make_latest cannot be null');
        }
        $allowedValues = $this->getMakeLatestAllowableValues();
        if (!in_array($make_latest, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'make_latest', must be one of '%s'",
                    $make_latest,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['make_latest'] = $make_latest;

        return $this;
    }

    /**
     * Gets discussion_category_name
     *
     * @return string|null
     */
    public function getDiscussionCategoryName()
    {
        return $this->container['discussion_category_name'];
    }

    /**
     * Sets discussion_category_name
     *
     * @param string|null $discussion_category_name If specified, a discussion of the specified category is created and linked to the release. The value must be a category that already exists in the repository. If there is already a discussion linked to the release, this parameter is ignored. For more information, see \"[Managing categories for discussions in your repository](https://docs.github.com/discussions/managing-discussions-for-your-community/managing-categories-for-discussions-in-your-repository).\"
     *
     * @return self
     */
    public function setDiscussionCategoryName($discussion_category_name)
    {
        if (is_null($discussion_category_name)) {
            throw new \InvalidArgumentException('non-nullable discussion_category_name cannot be null');
        }
        $this->container['discussion_category_name'] = $discussion_category_name;

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


