<?php
/**
 * SecretScanningLocationDetails
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
 * SecretScanningLocationDetails Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class SecretScanningLocationDetails implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'secret_scanning_location_details';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'path' => 'string',
        'start_line' => 'float',
        'end_line' => 'float',
        'start_column' => 'float',
        'end_column' => 'float',
        'blob_sha' => 'string',
        'blob_url' => 'string',
        'commit_sha' => 'string',
        'commit_url' => 'string',
        'issue_title_url' => 'string',
        'issue_body_url' => 'string',
        'issue_comment_url' => 'string',
        'discussion_title_url' => 'string',
        'discussion_body_url' => 'string',
        'discussion_comment_url' => 'string',
        'pull_request_title_url' => 'string',
        'pull_request_body_url' => 'string',
        'pull_request_comment_url' => 'string',
        'pull_request_review_url' => 'string',
        'pull_request_review_comment_url' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'path' => null,
        'start_line' => null,
        'end_line' => null,
        'start_column' => null,
        'end_column' => null,
        'blob_sha' => null,
        'blob_url' => null,
        'commit_sha' => null,
        'commit_url' => null,
        'issue_title_url' => 'uri',
        'issue_body_url' => 'uri',
        'issue_comment_url' => 'uri',
        'discussion_title_url' => 'uri',
        'discussion_body_url' => 'uri',
        'discussion_comment_url' => 'uri',
        'pull_request_title_url' => 'uri',
        'pull_request_body_url' => 'uri',
        'pull_request_comment_url' => 'uri',
        'pull_request_review_url' => 'uri',
        'pull_request_review_comment_url' => 'uri'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'path' => false,
        'start_line' => false,
        'end_line' => false,
        'start_column' => false,
        'end_column' => false,
        'blob_sha' => false,
        'blob_url' => false,
        'commit_sha' => false,
        'commit_url' => false,
        'issue_title_url' => false,
        'issue_body_url' => false,
        'issue_comment_url' => false,
        'discussion_title_url' => false,
        'discussion_body_url' => false,
        'discussion_comment_url' => false,
        'pull_request_title_url' => false,
        'pull_request_body_url' => false,
        'pull_request_comment_url' => false,
        'pull_request_review_url' => false,
        'pull_request_review_comment_url' => false
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
        'path' => 'path',
        'start_line' => 'start_line',
        'end_line' => 'end_line',
        'start_column' => 'start_column',
        'end_column' => 'end_column',
        'blob_sha' => 'blob_sha',
        'blob_url' => 'blob_url',
        'commit_sha' => 'commit_sha',
        'commit_url' => 'commit_url',
        'issue_title_url' => 'issue_title_url',
        'issue_body_url' => 'issue_body_url',
        'issue_comment_url' => 'issue_comment_url',
        'discussion_title_url' => 'discussion_title_url',
        'discussion_body_url' => 'discussion_body_url',
        'discussion_comment_url' => 'discussion_comment_url',
        'pull_request_title_url' => 'pull_request_title_url',
        'pull_request_body_url' => 'pull_request_body_url',
        'pull_request_comment_url' => 'pull_request_comment_url',
        'pull_request_review_url' => 'pull_request_review_url',
        'pull_request_review_comment_url' => 'pull_request_review_comment_url'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'path' => 'setPath',
        'start_line' => 'setStartLine',
        'end_line' => 'setEndLine',
        'start_column' => 'setStartColumn',
        'end_column' => 'setEndColumn',
        'blob_sha' => 'setBlobSha',
        'blob_url' => 'setBlobUrl',
        'commit_sha' => 'setCommitSha',
        'commit_url' => 'setCommitUrl',
        'issue_title_url' => 'setIssueTitleUrl',
        'issue_body_url' => 'setIssueBodyUrl',
        'issue_comment_url' => 'setIssueCommentUrl',
        'discussion_title_url' => 'setDiscussionTitleUrl',
        'discussion_body_url' => 'setDiscussionBodyUrl',
        'discussion_comment_url' => 'setDiscussionCommentUrl',
        'pull_request_title_url' => 'setPullRequestTitleUrl',
        'pull_request_body_url' => 'setPullRequestBodyUrl',
        'pull_request_comment_url' => 'setPullRequestCommentUrl',
        'pull_request_review_url' => 'setPullRequestReviewUrl',
        'pull_request_review_comment_url' => 'setPullRequestReviewCommentUrl'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'path' => 'getPath',
        'start_line' => 'getStartLine',
        'end_line' => 'getEndLine',
        'start_column' => 'getStartColumn',
        'end_column' => 'getEndColumn',
        'blob_sha' => 'getBlobSha',
        'blob_url' => 'getBlobUrl',
        'commit_sha' => 'getCommitSha',
        'commit_url' => 'getCommitUrl',
        'issue_title_url' => 'getIssueTitleUrl',
        'issue_body_url' => 'getIssueBodyUrl',
        'issue_comment_url' => 'getIssueCommentUrl',
        'discussion_title_url' => 'getDiscussionTitleUrl',
        'discussion_body_url' => 'getDiscussionBodyUrl',
        'discussion_comment_url' => 'getDiscussionCommentUrl',
        'pull_request_title_url' => 'getPullRequestTitleUrl',
        'pull_request_body_url' => 'getPullRequestBodyUrl',
        'pull_request_comment_url' => 'getPullRequestCommentUrl',
        'pull_request_review_url' => 'getPullRequestReviewUrl',
        'pull_request_review_comment_url' => 'getPullRequestReviewCommentUrl'
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
        $this->setIfExists('path', $data ?? [], null);
        $this->setIfExists('start_line', $data ?? [], null);
        $this->setIfExists('end_line', $data ?? [], null);
        $this->setIfExists('start_column', $data ?? [], null);
        $this->setIfExists('end_column', $data ?? [], null);
        $this->setIfExists('blob_sha', $data ?? [], null);
        $this->setIfExists('blob_url', $data ?? [], null);
        $this->setIfExists('commit_sha', $data ?? [], null);
        $this->setIfExists('commit_url', $data ?? [], null);
        $this->setIfExists('issue_title_url', $data ?? [], null);
        $this->setIfExists('issue_body_url', $data ?? [], null);
        $this->setIfExists('issue_comment_url', $data ?? [], null);
        $this->setIfExists('discussion_title_url', $data ?? [], null);
        $this->setIfExists('discussion_body_url', $data ?? [], null);
        $this->setIfExists('discussion_comment_url', $data ?? [], null);
        $this->setIfExists('pull_request_title_url', $data ?? [], null);
        $this->setIfExists('pull_request_body_url', $data ?? [], null);
        $this->setIfExists('pull_request_comment_url', $data ?? [], null);
        $this->setIfExists('pull_request_review_url', $data ?? [], null);
        $this->setIfExists('pull_request_review_comment_url', $data ?? [], null);
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

        if ($this->container['path'] === null) {
            $invalidProperties[] = "'path' can't be null";
        }
        if ($this->container['start_line'] === null) {
            $invalidProperties[] = "'start_line' can't be null";
        }
        if ($this->container['end_line'] === null) {
            $invalidProperties[] = "'end_line' can't be null";
        }
        if ($this->container['start_column'] === null) {
            $invalidProperties[] = "'start_column' can't be null";
        }
        if ($this->container['end_column'] === null) {
            $invalidProperties[] = "'end_column' can't be null";
        }
        if ($this->container['blob_sha'] === null) {
            $invalidProperties[] = "'blob_sha' can't be null";
        }
        if ($this->container['blob_url'] === null) {
            $invalidProperties[] = "'blob_url' can't be null";
        }
        if ($this->container['commit_sha'] === null) {
            $invalidProperties[] = "'commit_sha' can't be null";
        }
        if ($this->container['commit_url'] === null) {
            $invalidProperties[] = "'commit_url' can't be null";
        }
        if ($this->container['issue_title_url'] === null) {
            $invalidProperties[] = "'issue_title_url' can't be null";
        }
        if ($this->container['issue_body_url'] === null) {
            $invalidProperties[] = "'issue_body_url' can't be null";
        }
        if ($this->container['issue_comment_url'] === null) {
            $invalidProperties[] = "'issue_comment_url' can't be null";
        }
        if ($this->container['discussion_title_url'] === null) {
            $invalidProperties[] = "'discussion_title_url' can't be null";
        }
        if ($this->container['discussion_body_url'] === null) {
            $invalidProperties[] = "'discussion_body_url' can't be null";
        }
        if ($this->container['discussion_comment_url'] === null) {
            $invalidProperties[] = "'discussion_comment_url' can't be null";
        }
        if ($this->container['pull_request_title_url'] === null) {
            $invalidProperties[] = "'pull_request_title_url' can't be null";
        }
        if ($this->container['pull_request_body_url'] === null) {
            $invalidProperties[] = "'pull_request_body_url' can't be null";
        }
        if ($this->container['pull_request_comment_url'] === null) {
            $invalidProperties[] = "'pull_request_comment_url' can't be null";
        }
        if ($this->container['pull_request_review_url'] === null) {
            $invalidProperties[] = "'pull_request_review_url' can't be null";
        }
        if ($this->container['pull_request_review_comment_url'] === null) {
            $invalidProperties[] = "'pull_request_review_comment_url' can't be null";
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
     * Gets path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->container['path'];
    }

    /**
     * Sets path
     *
     * @param string $path The file path in the repository
     *
     * @return self
     */
    public function setPath($path)
    {
        if (is_null($path)) {
            throw new \InvalidArgumentException('non-nullable path cannot be null');
        }
        $this->container['path'] = $path;

        return $this;
    }

    /**
     * Gets start_line
     *
     * @return float
     */
    public function getStartLine()
    {
        return $this->container['start_line'];
    }

    /**
     * Sets start_line
     *
     * @param float $start_line Line number at which the secret starts in the file
     *
     * @return self
     */
    public function setStartLine($start_line)
    {
        if (is_null($start_line)) {
            throw new \InvalidArgumentException('non-nullable start_line cannot be null');
        }
        $this->container['start_line'] = $start_line;

        return $this;
    }

    /**
     * Gets end_line
     *
     * @return float
     */
    public function getEndLine()
    {
        return $this->container['end_line'];
    }

    /**
     * Sets end_line
     *
     * @param float $end_line Line number at which the secret ends in the file
     *
     * @return self
     */
    public function setEndLine($end_line)
    {
        if (is_null($end_line)) {
            throw new \InvalidArgumentException('non-nullable end_line cannot be null');
        }
        $this->container['end_line'] = $end_line;

        return $this;
    }

    /**
     * Gets start_column
     *
     * @return float
     */
    public function getStartColumn()
    {
        return $this->container['start_column'];
    }

    /**
     * Sets start_column
     *
     * @param float $start_column The column at which the secret starts within the start line when the file is interpreted as 8BIT ASCII
     *
     * @return self
     */
    public function setStartColumn($start_column)
    {
        if (is_null($start_column)) {
            throw new \InvalidArgumentException('non-nullable start_column cannot be null');
        }
        $this->container['start_column'] = $start_column;

        return $this;
    }

    /**
     * Gets end_column
     *
     * @return float
     */
    public function getEndColumn()
    {
        return $this->container['end_column'];
    }

    /**
     * Sets end_column
     *
     * @param float $end_column The column at which the secret ends within the end line when the file is interpreted as 8BIT ASCII
     *
     * @return self
     */
    public function setEndColumn($end_column)
    {
        if (is_null($end_column)) {
            throw new \InvalidArgumentException('non-nullable end_column cannot be null');
        }
        $this->container['end_column'] = $end_column;

        return $this;
    }

    /**
     * Gets blob_sha
     *
     * @return string
     */
    public function getBlobSha()
    {
        return $this->container['blob_sha'];
    }

    /**
     * Sets blob_sha
     *
     * @param string $blob_sha SHA-1 hash ID of the associated blob
     *
     * @return self
     */
    public function setBlobSha($blob_sha)
    {
        if (is_null($blob_sha)) {
            throw new \InvalidArgumentException('non-nullable blob_sha cannot be null');
        }
        $this->container['blob_sha'] = $blob_sha;

        return $this;
    }

    /**
     * Gets blob_url
     *
     * @return string
     */
    public function getBlobUrl()
    {
        return $this->container['blob_url'];
    }

    /**
     * Sets blob_url
     *
     * @param string $blob_url The API URL to get the associated blob resource
     *
     * @return self
     */
    public function setBlobUrl($blob_url)
    {
        if (is_null($blob_url)) {
            throw new \InvalidArgumentException('non-nullable blob_url cannot be null');
        }
        $this->container['blob_url'] = $blob_url;

        return $this;
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
     * @param string $commit_sha SHA-1 hash ID of the associated commit
     *
     * @return self
     */
    public function setCommitSha($commit_sha)
    {
        if (is_null($commit_sha)) {
            throw new \InvalidArgumentException('non-nullable commit_sha cannot be null');
        }
        $this->container['commit_sha'] = $commit_sha;

        return $this;
    }

    /**
     * Gets commit_url
     *
     * @return string
     */
    public function getCommitUrl()
    {
        return $this->container['commit_url'];
    }

    /**
     * Sets commit_url
     *
     * @param string $commit_url The API URL to get the associated commit resource
     *
     * @return self
     */
    public function setCommitUrl($commit_url)
    {
        if (is_null($commit_url)) {
            throw new \InvalidArgumentException('non-nullable commit_url cannot be null');
        }
        $this->container['commit_url'] = $commit_url;

        return $this;
    }

    /**
     * Gets issue_title_url
     *
     * @return string
     */
    public function getIssueTitleUrl()
    {
        return $this->container['issue_title_url'];
    }

    /**
     * Sets issue_title_url
     *
     * @param string $issue_title_url The API URL to get the issue where the secret was detected.
     *
     * @return self
     */
    public function setIssueTitleUrl($issue_title_url)
    {
        if (is_null($issue_title_url)) {
            throw new \InvalidArgumentException('non-nullable issue_title_url cannot be null');
        }
        $this->container['issue_title_url'] = $issue_title_url;

        return $this;
    }

    /**
     * Gets issue_body_url
     *
     * @return string
     */
    public function getIssueBodyUrl()
    {
        return $this->container['issue_body_url'];
    }

    /**
     * Sets issue_body_url
     *
     * @param string $issue_body_url The API URL to get the issue where the secret was detected.
     *
     * @return self
     */
    public function setIssueBodyUrl($issue_body_url)
    {
        if (is_null($issue_body_url)) {
            throw new \InvalidArgumentException('non-nullable issue_body_url cannot be null');
        }
        $this->container['issue_body_url'] = $issue_body_url;

        return $this;
    }

    /**
     * Gets issue_comment_url
     *
     * @return string
     */
    public function getIssueCommentUrl()
    {
        return $this->container['issue_comment_url'];
    }

    /**
     * Sets issue_comment_url
     *
     * @param string $issue_comment_url The API URL to get the issue comment where the secret was detected.
     *
     * @return self
     */
    public function setIssueCommentUrl($issue_comment_url)
    {
        if (is_null($issue_comment_url)) {
            throw new \InvalidArgumentException('non-nullable issue_comment_url cannot be null');
        }
        $this->container['issue_comment_url'] = $issue_comment_url;

        return $this;
    }

    /**
     * Gets discussion_title_url
     *
     * @return string
     */
    public function getDiscussionTitleUrl()
    {
        return $this->container['discussion_title_url'];
    }

    /**
     * Sets discussion_title_url
     *
     * @param string $discussion_title_url The URL to the discussion where the secret was detected.
     *
     * @return self
     */
    public function setDiscussionTitleUrl($discussion_title_url)
    {
        if (is_null($discussion_title_url)) {
            throw new \InvalidArgumentException('non-nullable discussion_title_url cannot be null');
        }
        $this->container['discussion_title_url'] = $discussion_title_url;

        return $this;
    }

    /**
     * Gets discussion_body_url
     *
     * @return string
     */
    public function getDiscussionBodyUrl()
    {
        return $this->container['discussion_body_url'];
    }

    /**
     * Sets discussion_body_url
     *
     * @param string $discussion_body_url The URL to the discussion where the secret was detected.
     *
     * @return self
     */
    public function setDiscussionBodyUrl($discussion_body_url)
    {
        if (is_null($discussion_body_url)) {
            throw new \InvalidArgumentException('non-nullable discussion_body_url cannot be null');
        }
        $this->container['discussion_body_url'] = $discussion_body_url;

        return $this;
    }

    /**
     * Gets discussion_comment_url
     *
     * @return string
     */
    public function getDiscussionCommentUrl()
    {
        return $this->container['discussion_comment_url'];
    }

    /**
     * Sets discussion_comment_url
     *
     * @param string $discussion_comment_url The API URL to get the discussion comment where the secret was detected.
     *
     * @return self
     */
    public function setDiscussionCommentUrl($discussion_comment_url)
    {
        if (is_null($discussion_comment_url)) {
            throw new \InvalidArgumentException('non-nullable discussion_comment_url cannot be null');
        }
        $this->container['discussion_comment_url'] = $discussion_comment_url;

        return $this;
    }

    /**
     * Gets pull_request_title_url
     *
     * @return string
     */
    public function getPullRequestTitleUrl()
    {
        return $this->container['pull_request_title_url'];
    }

    /**
     * Sets pull_request_title_url
     *
     * @param string $pull_request_title_url The API URL to get the pull request where the secret was detected.
     *
     * @return self
     */
    public function setPullRequestTitleUrl($pull_request_title_url)
    {
        if (is_null($pull_request_title_url)) {
            throw new \InvalidArgumentException('non-nullable pull_request_title_url cannot be null');
        }
        $this->container['pull_request_title_url'] = $pull_request_title_url;

        return $this;
    }

    /**
     * Gets pull_request_body_url
     *
     * @return string
     */
    public function getPullRequestBodyUrl()
    {
        return $this->container['pull_request_body_url'];
    }

    /**
     * Sets pull_request_body_url
     *
     * @param string $pull_request_body_url The API URL to get the pull request where the secret was detected.
     *
     * @return self
     */
    public function setPullRequestBodyUrl($pull_request_body_url)
    {
        if (is_null($pull_request_body_url)) {
            throw new \InvalidArgumentException('non-nullable pull_request_body_url cannot be null');
        }
        $this->container['pull_request_body_url'] = $pull_request_body_url;

        return $this;
    }

    /**
     * Gets pull_request_comment_url
     *
     * @return string
     */
    public function getPullRequestCommentUrl()
    {
        return $this->container['pull_request_comment_url'];
    }

    /**
     * Sets pull_request_comment_url
     *
     * @param string $pull_request_comment_url The API URL to get the pull request comment where the secret was detected.
     *
     * @return self
     */
    public function setPullRequestCommentUrl($pull_request_comment_url)
    {
        if (is_null($pull_request_comment_url)) {
            throw new \InvalidArgumentException('non-nullable pull_request_comment_url cannot be null');
        }
        $this->container['pull_request_comment_url'] = $pull_request_comment_url;

        return $this;
    }

    /**
     * Gets pull_request_review_url
     *
     * @return string
     */
    public function getPullRequestReviewUrl()
    {
        return $this->container['pull_request_review_url'];
    }

    /**
     * Sets pull_request_review_url
     *
     * @param string $pull_request_review_url The API URL to get the pull request review where the secret was detected.
     *
     * @return self
     */
    public function setPullRequestReviewUrl($pull_request_review_url)
    {
        if (is_null($pull_request_review_url)) {
            throw new \InvalidArgumentException('non-nullable pull_request_review_url cannot be null');
        }
        $this->container['pull_request_review_url'] = $pull_request_review_url;

        return $this;
    }

    /**
     * Gets pull_request_review_comment_url
     *
     * @return string
     */
    public function getPullRequestReviewCommentUrl()
    {
        return $this->container['pull_request_review_comment_url'];
    }

    /**
     * Sets pull_request_review_comment_url
     *
     * @param string $pull_request_review_comment_url The API URL to get the pull request review comment where the secret was detected.
     *
     * @return self
     */
    public function setPullRequestReviewCommentUrl($pull_request_review_comment_url)
    {
        if (is_null($pull_request_review_comment_url)) {
            throw new \InvalidArgumentException('non-nullable pull_request_review_comment_url cannot be null');
        }
        $this->container['pull_request_review_comment_url'] = $pull_request_review_comment_url;

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


