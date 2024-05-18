<?php
/**
 * IssueEventForIssue
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
 * IssueEventForIssue Class Doc Comment
 *
 * @category Class
 * @description Issue Event for Issue
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class IssueEventForIssue implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'issue-event-for-issue';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'node_id' => 'string',
        'url' => 'string',
        'actor' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser',
        'event' => 'string',
        'commit_id' => 'string',
        'commit_url' => 'string',
        'created_at' => 'string',
        'performed_via_github_app' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Integration',
        'label' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\LabeledIssueEventLabel',
        'assignee' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser',
        'assigner' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser',
        'milestone' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\MilestonedIssueEventMilestone',
        'rename' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RenamedIssueEventRename',
        'review_requester' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser',
        'requested_team' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Team',
        'requested_reviewer' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser',
        'dismissed_review' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReviewDismissedIssueEventDismissedReview',
        'lock_reason' => 'string',
        'project_card' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\AddedToProjectIssueEventProjectCard'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'id' => null,
        'node_id' => null,
        'url' => null,
        'actor' => null,
        'event' => null,
        'commit_id' => null,
        'commit_url' => null,
        'created_at' => null,
        'performed_via_github_app' => null,
        'label' => null,
        'assignee' => null,
        'assigner' => null,
        'milestone' => null,
        'rename' => null,
        'review_requester' => null,
        'requested_team' => null,
        'requested_reviewer' => null,
        'dismissed_review' => null,
        'lock_reason' => null,
        'project_card' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'node_id' => false,
        'url' => false,
        'actor' => false,
        'event' => false,
        'commit_id' => true,
        'commit_url' => true,
        'created_at' => false,
        'performed_via_github_app' => true,
        'label' => false,
        'assignee' => false,
        'assigner' => false,
        'milestone' => false,
        'rename' => false,
        'review_requester' => false,
        'requested_team' => false,
        'requested_reviewer' => false,
        'dismissed_review' => false,
        'lock_reason' => true,
        'project_card' => false
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
        'id' => 'id',
        'node_id' => 'node_id',
        'url' => 'url',
        'actor' => 'actor',
        'event' => 'event',
        'commit_id' => 'commit_id',
        'commit_url' => 'commit_url',
        'created_at' => 'created_at',
        'performed_via_github_app' => 'performed_via_github_app',
        'label' => 'label',
        'assignee' => 'assignee',
        'assigner' => 'assigner',
        'milestone' => 'milestone',
        'rename' => 'rename',
        'review_requester' => 'review_requester',
        'requested_team' => 'requested_team',
        'requested_reviewer' => 'requested_reviewer',
        'dismissed_review' => 'dismissed_review',
        'lock_reason' => 'lock_reason',
        'project_card' => 'project_card'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'node_id' => 'setNodeId',
        'url' => 'setUrl',
        'actor' => 'setActor',
        'event' => 'setEvent',
        'commit_id' => 'setCommitId',
        'commit_url' => 'setCommitUrl',
        'created_at' => 'setCreatedAt',
        'performed_via_github_app' => 'setPerformedViaGithubApp',
        'label' => 'setLabel',
        'assignee' => 'setAssignee',
        'assigner' => 'setAssigner',
        'milestone' => 'setMilestone',
        'rename' => 'setRename',
        'review_requester' => 'setReviewRequester',
        'requested_team' => 'setRequestedTeam',
        'requested_reviewer' => 'setRequestedReviewer',
        'dismissed_review' => 'setDismissedReview',
        'lock_reason' => 'setLockReason',
        'project_card' => 'setProjectCard'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'node_id' => 'getNodeId',
        'url' => 'getUrl',
        'actor' => 'getActor',
        'event' => 'getEvent',
        'commit_id' => 'getCommitId',
        'commit_url' => 'getCommitUrl',
        'created_at' => 'getCreatedAt',
        'performed_via_github_app' => 'getPerformedViaGithubApp',
        'label' => 'getLabel',
        'assignee' => 'getAssignee',
        'assigner' => 'getAssigner',
        'milestone' => 'getMilestone',
        'rename' => 'getRename',
        'review_requester' => 'getReviewRequester',
        'requested_team' => 'getRequestedTeam',
        'requested_reviewer' => 'getRequestedReviewer',
        'dismissed_review' => 'getDismissedReview',
        'lock_reason' => 'getLockReason',
        'project_card' => 'getProjectCard'
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
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('node_id', $data ?? [], null);
        $this->setIfExists('url', $data ?? [], null);
        $this->setIfExists('actor', $data ?? [], null);
        $this->setIfExists('event', $data ?? [], null);
        $this->setIfExists('commit_id', $data ?? [], null);
        $this->setIfExists('commit_url', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('performed_via_github_app', $data ?? [], null);
        $this->setIfExists('label', $data ?? [], null);
        $this->setIfExists('assignee', $data ?? [], null);
        $this->setIfExists('assigner', $data ?? [], null);
        $this->setIfExists('milestone', $data ?? [], null);
        $this->setIfExists('rename', $data ?? [], null);
        $this->setIfExists('review_requester', $data ?? [], null);
        $this->setIfExists('requested_team', $data ?? [], null);
        $this->setIfExists('requested_reviewer', $data ?? [], null);
        $this->setIfExists('dismissed_review', $data ?? [], null);
        $this->setIfExists('lock_reason', $data ?? [], null);
        $this->setIfExists('project_card', $data ?? [], null);
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

        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['node_id'] === null) {
            $invalidProperties[] = "'node_id' can't be null";
        }
        if ($this->container['url'] === null) {
            $invalidProperties[] = "'url' can't be null";
        }
        if ($this->container['actor'] === null) {
            $invalidProperties[] = "'actor' can't be null";
        }
        if ($this->container['event'] === null) {
            $invalidProperties[] = "'event' can't be null";
        }
        if ($this->container['commit_id'] === null) {
            $invalidProperties[] = "'commit_id' can't be null";
        }
        if ($this->container['commit_url'] === null) {
            $invalidProperties[] = "'commit_url' can't be null";
        }
        if ($this->container['created_at'] === null) {
            $invalidProperties[] = "'created_at' can't be null";
        }
        if ($this->container['performed_via_github_app'] === null) {
            $invalidProperties[] = "'performed_via_github_app' can't be null";
        }
        if ($this->container['label'] === null) {
            $invalidProperties[] = "'label' can't be null";
        }
        if ($this->container['assignee'] === null) {
            $invalidProperties[] = "'assignee' can't be null";
        }
        if ($this->container['assigner'] === null) {
            $invalidProperties[] = "'assigner' can't be null";
        }
        if ($this->container['milestone'] === null) {
            $invalidProperties[] = "'milestone' can't be null";
        }
        if ($this->container['rename'] === null) {
            $invalidProperties[] = "'rename' can't be null";
        }
        if ($this->container['review_requester'] === null) {
            $invalidProperties[] = "'review_requester' can't be null";
        }
        if ($this->container['dismissed_review'] === null) {
            $invalidProperties[] = "'dismissed_review' can't be null";
        }
        if ($this->container['lock_reason'] === null) {
            $invalidProperties[] = "'lock_reason' can't be null";
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
     * Gets id
     *
     * @return int
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param int $id id
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
     * Gets node_id
     *
     * @return string
     */
    public function getNodeId()
    {
        return $this->container['node_id'];
    }

    /**
     * Sets node_id
     *
     * @param string $node_id node_id
     *
     * @return self
     */
    public function setNodeId($node_id)
    {
        if (is_null($node_id)) {
            throw new \InvalidArgumentException('non-nullable node_id cannot be null');
        }
        $this->container['node_id'] = $node_id;

        return $this;
    }

    /**
     * Gets url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string $url url
     *
     * @return self
     */
    public function setUrl($url)
    {
        if (is_null($url)) {
            throw new \InvalidArgumentException('non-nullable url cannot be null');
        }
        $this->container['url'] = $url;

        return $this;
    }

    /**
     * Gets actor
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser
     */
    public function getActor()
    {
        return $this->container['actor'];
    }

    /**
     * Sets actor
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser $actor actor
     *
     * @return self
     */
    public function setActor($actor)
    {
        if (is_null($actor)) {
            throw new \InvalidArgumentException('non-nullable actor cannot be null');
        }
        $this->container['actor'] = $actor;

        return $this;
    }

    /**
     * Gets event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->container['event'];
    }

    /**
     * Sets event
     *
     * @param string $event event
     *
     * @return self
     */
    public function setEvent($event)
    {
        if (is_null($event)) {
            throw new \InvalidArgumentException('non-nullable event cannot be null');
        }
        $this->container['event'] = $event;

        return $this;
    }

    /**
     * Gets commit_id
     *
     * @return string
     */
    public function getCommitId()
    {
        return $this->container['commit_id'];
    }

    /**
     * Sets commit_id
     *
     * @param string $commit_id commit_id
     *
     * @return self
     */
    public function setCommitId($commit_id)
    {
        if (is_null($commit_id)) {
            array_push($this->openAPINullablesSetToNull, 'commit_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('commit_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['commit_id'] = $commit_id;

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
     * @param string $commit_url commit_url
     *
     * @return self
     */
    public function setCommitUrl($commit_url)
    {
        if (is_null($commit_url)) {
            array_push($this->openAPINullablesSetToNull, 'commit_url');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('commit_url', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['commit_url'] = $commit_url;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param string $created_at created_at
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
     * Gets performed_via_github_app
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Integration
     */
    public function getPerformedViaGithubApp()
    {
        return $this->container['performed_via_github_app'];
    }

    /**
     * Sets performed_via_github_app
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Integration $performed_via_github_app performed_via_github_app
     *
     * @return self
     */
    public function setPerformedViaGithubApp($performed_via_github_app)
    {
        if (is_null($performed_via_github_app)) {
            array_push($this->openAPINullablesSetToNull, 'performed_via_github_app');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('performed_via_github_app', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['performed_via_github_app'] = $performed_via_github_app;

        return $this;
    }

    /**
     * Gets label
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\LabeledIssueEventLabel
     */
    public function getLabel()
    {
        return $this->container['label'];
    }

    /**
     * Sets label
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\LabeledIssueEventLabel $label label
     *
     * @return self
     */
    public function setLabel($label)
    {
        if (is_null($label)) {
            throw new \InvalidArgumentException('non-nullable label cannot be null');
        }
        $this->container['label'] = $label;

        return $this;
    }

    /**
     * Gets assignee
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser
     */
    public function getAssignee()
    {
        return $this->container['assignee'];
    }

    /**
     * Sets assignee
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser $assignee assignee
     *
     * @return self
     */
    public function setAssignee($assignee)
    {
        if (is_null($assignee)) {
            throw new \InvalidArgumentException('non-nullable assignee cannot be null');
        }
        $this->container['assignee'] = $assignee;

        return $this;
    }

    /**
     * Gets assigner
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser
     */
    public function getAssigner()
    {
        return $this->container['assigner'];
    }

    /**
     * Sets assigner
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser $assigner assigner
     *
     * @return self
     */
    public function setAssigner($assigner)
    {
        if (is_null($assigner)) {
            throw new \InvalidArgumentException('non-nullable assigner cannot be null');
        }
        $this->container['assigner'] = $assigner;

        return $this;
    }

    /**
     * Gets milestone
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\MilestonedIssueEventMilestone
     */
    public function getMilestone()
    {
        return $this->container['milestone'];
    }

    /**
     * Sets milestone
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\MilestonedIssueEventMilestone $milestone milestone
     *
     * @return self
     */
    public function setMilestone($milestone)
    {
        if (is_null($milestone)) {
            throw new \InvalidArgumentException('non-nullable milestone cannot be null');
        }
        $this->container['milestone'] = $milestone;

        return $this;
    }

    /**
     * Gets rename
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RenamedIssueEventRename
     */
    public function getRename()
    {
        return $this->container['rename'];
    }

    /**
     * Sets rename
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\RenamedIssueEventRename $rename rename
     *
     * @return self
     */
    public function setRename($rename)
    {
        if (is_null($rename)) {
            throw new \InvalidArgumentException('non-nullable rename cannot be null');
        }
        $this->container['rename'] = $rename;

        return $this;
    }

    /**
     * Gets review_requester
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser
     */
    public function getReviewRequester()
    {
        return $this->container['review_requester'];
    }

    /**
     * Sets review_requester
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser $review_requester review_requester
     *
     * @return self
     */
    public function setReviewRequester($review_requester)
    {
        if (is_null($review_requester)) {
            throw new \InvalidArgumentException('non-nullable review_requester cannot be null');
        }
        $this->container['review_requester'] = $review_requester;

        return $this;
    }

    /**
     * Gets requested_team
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Team|null
     */
    public function getRequestedTeam()
    {
        return $this->container['requested_team'];
    }

    /**
     * Sets requested_team
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Team|null $requested_team requested_team
     *
     * @return self
     */
    public function setRequestedTeam($requested_team)
    {
        if (is_null($requested_team)) {
            throw new \InvalidArgumentException('non-nullable requested_team cannot be null');
        }
        $this->container['requested_team'] = $requested_team;

        return $this;
    }

    /**
     * Gets requested_reviewer
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser|null
     */
    public function getRequestedReviewer()
    {
        return $this->container['requested_reviewer'];
    }

    /**
     * Sets requested_reviewer
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUser|null $requested_reviewer requested_reviewer
     *
     * @return self
     */
    public function setRequestedReviewer($requested_reviewer)
    {
        if (is_null($requested_reviewer)) {
            throw new \InvalidArgumentException('non-nullable requested_reviewer cannot be null');
        }
        $this->container['requested_reviewer'] = $requested_reviewer;

        return $this;
    }

    /**
     * Gets dismissed_review
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReviewDismissedIssueEventDismissedReview
     */
    public function getDismissedReview()
    {
        return $this->container['dismissed_review'];
    }

    /**
     * Sets dismissed_review
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\ReviewDismissedIssueEventDismissedReview $dismissed_review dismissed_review
     *
     * @return self
     */
    public function setDismissedReview($dismissed_review)
    {
        if (is_null($dismissed_review)) {
            throw new \InvalidArgumentException('non-nullable dismissed_review cannot be null');
        }
        $this->container['dismissed_review'] = $dismissed_review;

        return $this;
    }

    /**
     * Gets lock_reason
     *
     * @return string
     */
    public function getLockReason()
    {
        return $this->container['lock_reason'];
    }

    /**
     * Sets lock_reason
     *
     * @param string $lock_reason lock_reason
     *
     * @return self
     */
    public function setLockReason($lock_reason)
    {
        if (is_null($lock_reason)) {
            array_push($this->openAPINullablesSetToNull, 'lock_reason');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('lock_reason', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['lock_reason'] = $lock_reason;

        return $this;
    }

    /**
     * Gets project_card
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\AddedToProjectIssueEventProjectCard|null
     */
    public function getProjectCard()
    {
        return $this->container['project_card'];
    }

    /**
     * Sets project_card
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\AddedToProjectIssueEventProjectCard|null $project_card project_card
     *
     * @return self
     */
    public function setProjectCard($project_card)
    {
        if (is_null($project_card)) {
            throw new \InvalidArgumentException('non-nullable project_card cannot be null');
        }
        $this->container['project_card'] = $project_card;

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


