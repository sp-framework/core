<?php
/**
 * CodeScanningUploadSarifRequest
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
 * CodeScanningUploadSarifRequest Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class CodeScanningUploadSarifRequest implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'code_scanning_upload_sarif_request';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'commit_sha' => 'string',
        'ref' => 'string',
        'sarif' => 'string',
        'checkout_uri' => 'string',
        'started_at' => '\DateTime',
        'tool_name' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'commit_sha' => null,
        'ref' => null,
        'sarif' => null,
        'checkout_uri' => 'uri',
        'started_at' => 'date-time',
        'tool_name' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'commit_sha' => false,
        'ref' => false,
        'sarif' => false,
        'checkout_uri' => false,
        'started_at' => false,
        'tool_name' => false
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
        'commit_sha' => 'commit_sha',
        'ref' => 'ref',
        'sarif' => 'sarif',
        'checkout_uri' => 'checkout_uri',
        'started_at' => 'started_at',
        'tool_name' => 'tool_name'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'commit_sha' => 'setCommitSha',
        'ref' => 'setRef',
        'sarif' => 'setSarif',
        'checkout_uri' => 'setCheckoutUri',
        'started_at' => 'setStartedAt',
        'tool_name' => 'setToolName'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'commit_sha' => 'getCommitSha',
        'ref' => 'getRef',
        'sarif' => 'getSarif',
        'checkout_uri' => 'getCheckoutUri',
        'started_at' => 'getStartedAt',
        'tool_name' => 'getToolName'
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
        $this->setIfExists('commit_sha', $data ?? [], null);
        $this->setIfExists('ref', $data ?? [], null);
        $this->setIfExists('sarif', $data ?? [], null);
        $this->setIfExists('checkout_uri', $data ?? [], null);
        $this->setIfExists('started_at', $data ?? [], null);
        $this->setIfExists('tool_name', $data ?? [], null);
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

        if ($this->container['commit_sha'] === null) {
            $invalidProperties[] = "'commit_sha' can't be null";
        }
        if ((mb_strlen($this->container['commit_sha']) > 40)) {
            $invalidProperties[] = "invalid value for 'commit_sha', the character length must be smaller than or equal to 40.";
        }

        if ((mb_strlen($this->container['commit_sha']) < 40)) {
            $invalidProperties[] = "invalid value for 'commit_sha', the character length must be bigger than or equal to 40.";
        }

        if (!preg_match("/^[0-9a-fA-F]+$/", $this->container['commit_sha'])) {
            $invalidProperties[] = "invalid value for 'commit_sha', must be conform to the pattern /^[0-9a-fA-F]+$/.";
        }

        if ($this->container['ref'] === null) {
            $invalidProperties[] = "'ref' can't be null";
        }
        if (!preg_match("/^refs\/(heads|tags|pull)\/.*$/", $this->container['ref'])) {
            $invalidProperties[] = "invalid value for 'ref', must be conform to the pattern /^refs\/(heads|tags|pull)\/.*$/.";
        }

        if ($this->container['sarif'] === null) {
            $invalidProperties[] = "'sarif' can't be null";
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
     * Gets commit_sha
     *
     * @return string
     */
    public function getCommitSha()
    {
        return $this->container['commit_sha'];
    }

    /**
     * Sets commit_sha
     *
     * @param string $commit_sha The SHA of the commit to which the analysis you are uploading relates.
     *
     * @return self
     */
    public function setCommitSha($commit_sha)
    {
        if (is_null($commit_sha)) {
            throw new \InvalidArgumentException('non-nullable commit_sha cannot be null');
        }
        if ((mb_strlen($commit_sha) > 40)) {
            throw new \InvalidArgumentException('invalid length for $commit_sha when calling CodeScanningUploadSarifRequest., must be smaller than or equal to 40.');
        }
        if ((mb_strlen($commit_sha) < 40)) {
            throw new \InvalidArgumentException('invalid length for $commit_sha when calling CodeScanningUploadSarifRequest., must be bigger than or equal to 40.');
        }
        if ((!preg_match("/^[0-9a-fA-F]+$/", ObjectSerializer::toString($commit_sha)))) {
            throw new \InvalidArgumentException("invalid value for \$commit_sha when calling CodeScanningUploadSarifRequest., must conform to the pattern /^[0-9a-fA-F]+$/.");
        }

        $this->container['commit_sha'] = $commit_sha;

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
     * @param string $ref The full Git reference, formatted as `refs/heads/<branch name>`, `refs/tags/<tag>`, `refs/pull/<number>/merge`, or `refs/pull/<number>/head`.
     *
     * @return self
     */
    public function setRef($ref)
    {
        if (is_null($ref)) {
            throw new \InvalidArgumentException('non-nullable ref cannot be null');
        }

        if ((!preg_match("/^refs\/(heads|tags|pull)\/.*$/", ObjectSerializer::toString($ref)))) {
            throw new \InvalidArgumentException("invalid value for \$ref when calling CodeScanningUploadSarifRequest., must conform to the pattern /^refs\/(heads|tags|pull)\/.*$/.");
        }

        $this->container['ref'] = $ref;

        return $this;
    }

    /**
     * Gets sarif
     *
     * @return string
     */
    public function getSarif()
    {
        return $this->container['sarif'];
    }

    /**
     * Sets sarif
     *
     * @param string $sarif A Base64 string representing the SARIF file to upload. You must first compress your SARIF file using [`gzip`](http://www.gnu.org/software/gzip/manual/gzip.html) and then translate the contents of the file into a Base64 encoding string. For more information, see \"[SARIF support for code scanning](https://docs.github.com/enterprise-server@3.12/code-security/secure-coding/sarif-support-for-code-scanning).\"
     *
     * @return self
     */
    public function setSarif($sarif)
    {
        if (is_null($sarif)) {
            throw new \InvalidArgumentException('non-nullable sarif cannot be null');
        }
        $this->container['sarif'] = $sarif;

        return $this;
    }

    /**
     * Gets checkout_uri
     *
     * @return string|null
     */
    public function getCheckoutUri()
    {
        return $this->container['checkout_uri'];
    }

    /**
     * Sets checkout_uri
     *
     * @param string|null $checkout_uri The base directory used in the analysis, as it appears in the SARIF file. This property is used to convert file paths from absolute to relative, so that alerts can be mapped to their correct location in the repository.
     *
     * @return self
     */
    public function setCheckoutUri($checkout_uri)
    {
        if (is_null($checkout_uri)) {
            throw new \InvalidArgumentException('non-nullable checkout_uri cannot be null');
        }
        $this->container['checkout_uri'] = $checkout_uri;

        return $this;
    }

    /**
     * Gets started_at
     *
     * @return \DateTime|null
     */
    public function getStartedAt()
    {
        return $this->container['started_at'];
    }

    /**
     * Sets started_at
     *
     * @param \DateTime|null $started_at The time that the analysis run began. This is a timestamp in [ISO 8601](https://en.wikipedia.org/wiki/ISO_8601) format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setStartedAt($started_at)
    {
        if (is_null($started_at)) {
            throw new \InvalidArgumentException('non-nullable started_at cannot be null');
        }
        $this->container['started_at'] = $started_at;

        return $this;
    }

    /**
     * Gets tool_name
     *
     * @return string|null
     */
    public function getToolName()
    {
        return $this->container['tool_name'];
    }

    /**
     * Sets tool_name
     *
     * @param string|null $tool_name The name of the tool used to generate the code scanning analysis. If this parameter is not used, the tool name defaults to \"API\". If the uploaded SARIF contains a tool GUID, this will be available for filtering using the `tool_guid` parameter of operations such as `GET /repos/{owner}/{repo}/code-scanning/alerts`.
     *
     * @return self
     */
    public function setToolName($tool_name)
    {
        if (is_null($tool_name)) {
            throw new \InvalidArgumentException('non-nullable tool_name cannot be null');
        }
        $this->container['tool_name'] = $tool_name;

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


