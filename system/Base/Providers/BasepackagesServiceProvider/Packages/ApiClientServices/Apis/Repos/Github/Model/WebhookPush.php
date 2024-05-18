<?php
/**
 * WebhookPush
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
 * WebhookPush Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class WebhookPush implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'webhook-push';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'after' => 'string',
        'base_ref' => 'string',
        'before' => 'string',
        'commits' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Commit[]',
        'compare' => 'string',
        'created' => 'bool',
        'deleted' => 'bool',
        'enterprise' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\EnterpriseWebhooks',
        'forced' => 'bool',
        'head_commit' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Commit1',
        'installation' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleInstallation',
        'organization' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OrganizationSimpleWebhooks',
        'pusher' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Committer1',
        'ref' => 'string',
        'repository' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Repository2',
        'sender' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUserWebhooks'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'after' => null,
        'base_ref' => null,
        'before' => null,
        'commits' => null,
        'compare' => null,
        'created' => null,
        'deleted' => null,
        'enterprise' => null,
        'forced' => null,
        'head_commit' => null,
        'installation' => null,
        'organization' => null,
        'pusher' => null,
        'ref' => null,
        'repository' => null,
        'sender' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'after' => false,
        'base_ref' => true,
        'before' => false,
        'commits' => false,
        'compare' => false,
        'created' => false,
        'deleted' => false,
        'enterprise' => false,
        'forced' => false,
        'head_commit' => true,
        'installation' => false,
        'organization' => false,
        'pusher' => false,
        'ref' => false,
        'repository' => false,
        'sender' => false
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
        'after' => 'after',
        'base_ref' => 'base_ref',
        'before' => 'before',
        'commits' => 'commits',
        'compare' => 'compare',
        'created' => 'created',
        'deleted' => 'deleted',
        'enterprise' => 'enterprise',
        'forced' => 'forced',
        'head_commit' => 'head_commit',
        'installation' => 'installation',
        'organization' => 'organization',
        'pusher' => 'pusher',
        'ref' => 'ref',
        'repository' => 'repository',
        'sender' => 'sender'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'after' => 'setAfter',
        'base_ref' => 'setBaseRef',
        'before' => 'setBefore',
        'commits' => 'setCommits',
        'compare' => 'setCompare',
        'created' => 'setCreated',
        'deleted' => 'setDeleted',
        'enterprise' => 'setEnterprise',
        'forced' => 'setForced',
        'head_commit' => 'setHeadCommit',
        'installation' => 'setInstallation',
        'organization' => 'setOrganization',
        'pusher' => 'setPusher',
        'ref' => 'setRef',
        'repository' => 'setRepository',
        'sender' => 'setSender'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'after' => 'getAfter',
        'base_ref' => 'getBaseRef',
        'before' => 'getBefore',
        'commits' => 'getCommits',
        'compare' => 'getCompare',
        'created' => 'getCreated',
        'deleted' => 'getDeleted',
        'enterprise' => 'getEnterprise',
        'forced' => 'getForced',
        'head_commit' => 'getHeadCommit',
        'installation' => 'getInstallation',
        'organization' => 'getOrganization',
        'pusher' => 'getPusher',
        'ref' => 'getRef',
        'repository' => 'getRepository',
        'sender' => 'getSender'
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
        $this->setIfExists('after', $data ?? [], null);
        $this->setIfExists('base_ref', $data ?? [], null);
        $this->setIfExists('before', $data ?? [], null);
        $this->setIfExists('commits', $data ?? [], null);
        $this->setIfExists('compare', $data ?? [], null);
        $this->setIfExists('created', $data ?? [], null);
        $this->setIfExists('deleted', $data ?? [], null);
        $this->setIfExists('enterprise', $data ?? [], null);
        $this->setIfExists('forced', $data ?? [], null);
        $this->setIfExists('head_commit', $data ?? [], null);
        $this->setIfExists('installation', $data ?? [], null);
        $this->setIfExists('organization', $data ?? [], null);
        $this->setIfExists('pusher', $data ?? [], null);
        $this->setIfExists('ref', $data ?? [], null);
        $this->setIfExists('repository', $data ?? [], null);
        $this->setIfExists('sender', $data ?? [], null);
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

        if ($this->container['after'] === null) {
            $invalidProperties[] = "'after' can't be null";
        }
        if ($this->container['base_ref'] === null) {
            $invalidProperties[] = "'base_ref' can't be null";
        }
        if ($this->container['before'] === null) {
            $invalidProperties[] = "'before' can't be null";
        }
        if ($this->container['commits'] === null) {
            $invalidProperties[] = "'commits' can't be null";
        }
        if ($this->container['compare'] === null) {
            $invalidProperties[] = "'compare' can't be null";
        }
        if ($this->container['created'] === null) {
            $invalidProperties[] = "'created' can't be null";
        }
        if ($this->container['deleted'] === null) {
            $invalidProperties[] = "'deleted' can't be null";
        }
        if ($this->container['forced'] === null) {
            $invalidProperties[] = "'forced' can't be null";
        }
        if ($this->container['head_commit'] === null) {
            $invalidProperties[] = "'head_commit' can't be null";
        }
        if ($this->container['pusher'] === null) {
            $invalidProperties[] = "'pusher' can't be null";
        }
        if ($this->container['ref'] === null) {
            $invalidProperties[] = "'ref' can't be null";
        }
        if ($this->container['repository'] === null) {
            $invalidProperties[] = "'repository' can't be null";
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
     * Gets after
     *
     * @return string
     */
    public function getAfter()
    {
        return $this->container['after'];
    }

    /**
     * Sets after
     *
     * @param string $after The SHA of the most recent commit on `ref` after the push.
     *
     * @return self
     */
    public function setAfter($after)
    {
        if (is_null($after)) {
            throw new \InvalidArgumentException('non-nullable after cannot be null');
        }
        $this->container['after'] = $after;

        return $this;
    }

    /**
     * Gets base_ref
     *
     * @return string
     */
    public function getBaseRef()
    {
        return $this->container['base_ref'];
    }

    /**
     * Sets base_ref
     *
     * @param string $base_ref base_ref
     *
     * @return self
     */
    public function setBaseRef($base_ref)
    {
        if (is_null($base_ref)) {
            array_push($this->openAPINullablesSetToNull, 'base_ref');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('base_ref', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['base_ref'] = $base_ref;

        return $this;
    }

    /**
     * Gets before
     *
     * @return string
     */
    public function getBefore()
    {
        return $this->container['before'];
    }

    /**
     * Sets before
     *
     * @param string $before The SHA of the most recent commit on `ref` before the push.
     *
     * @return self
     */
    public function setBefore($before)
    {
        if (is_null($before)) {
            throw new \InvalidArgumentException('non-nullable before cannot be null');
        }
        $this->container['before'] = $before;

        return $this;
    }

    /**
     * Gets commits
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Commit[]
     */
    public function getCommits()
    {
        return $this->container['commits'];
    }

    /**
     * Sets commits
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Commit[] $commits An array of commit objects describing the pushed commits. (Pushed commits are all commits that are included in the `compare` between the `before` commit and the `after` commit.) The array includes a maximum of 2048 commits. If necessary, you can use the [Commits API](https://docs.github.com/rest/commits) to fetch additional commits.
     *
     * @return self
     */
    public function setCommits($commits)
    {
        if (is_null($commits)) {
            throw new \InvalidArgumentException('non-nullable commits cannot be null');
        }
        $this->container['commits'] = $commits;

        return $this;
    }

    /**
     * Gets compare
     *
     * @return string
     */
    public function getCompare()
    {
        return $this->container['compare'];
    }

    /**
     * Sets compare
     *
     * @param string $compare URL that shows the changes in this `ref` update, from the `before` commit to the `after` commit. For a newly created `ref` that is directly based on the default branch, this is the comparison between the head of the default branch and the `after` commit. Otherwise, this shows all commits until the `after` commit.
     *
     * @return self
     */
    public function setCompare($compare)
    {
        if (is_null($compare)) {
            throw new \InvalidArgumentException('non-nullable compare cannot be null');
        }
        $this->container['compare'] = $compare;

        return $this;
    }

    /**
     * Gets created
     *
     * @return bool
     */
    public function getCreated()
    {
        return $this->container['created'];
    }

    /**
     * Sets created
     *
     * @param bool $created Whether this push created the `ref`.
     *
     * @return self
     */
    public function setCreated($created)
    {
        if (is_null($created)) {
            throw new \InvalidArgumentException('non-nullable created cannot be null');
        }
        $this->container['created'] = $created;

        return $this;
    }

    /**
     * Gets deleted
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->container['deleted'];
    }

    /**
     * Sets deleted
     *
     * @param bool $deleted Whether this push deleted the `ref`.
     *
     * @return self
     */
    public function setDeleted($deleted)
    {
        if (is_null($deleted)) {
            throw new \InvalidArgumentException('non-nullable deleted cannot be null');
        }
        $this->container['deleted'] = $deleted;

        return $this;
    }

    /**
     * Gets enterprise
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\EnterpriseWebhooks|null
     */
    public function getEnterprise()
    {
        return $this->container['enterprise'];
    }

    /**
     * Sets enterprise
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\EnterpriseWebhooks|null $enterprise enterprise
     *
     * @return self
     */
    public function setEnterprise($enterprise)
    {
        if (is_null($enterprise)) {
            throw new \InvalidArgumentException('non-nullable enterprise cannot be null');
        }
        $this->container['enterprise'] = $enterprise;

        return $this;
    }

    /**
     * Gets forced
     *
     * @return bool
     */
    public function getForced()
    {
        return $this->container['forced'];
    }

    /**
     * Sets forced
     *
     * @param bool $forced Whether this push was a force push of the `ref`.
     *
     * @return self
     */
    public function setForced($forced)
    {
        if (is_null($forced)) {
            throw new \InvalidArgumentException('non-nullable forced cannot be null');
        }
        $this->container['forced'] = $forced;

        return $this;
    }

    /**
     * Gets head_commit
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Commit1
     */
    public function getHeadCommit()
    {
        return $this->container['head_commit'];
    }

    /**
     * Sets head_commit
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Commit1 $head_commit head_commit
     *
     * @return self
     */
    public function setHeadCommit($head_commit)
    {
        if (is_null($head_commit)) {
            array_push($this->openAPINullablesSetToNull, 'head_commit');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('head_commit', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['head_commit'] = $head_commit;

        return $this;
    }

    /**
     * Gets installation
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleInstallation|null
     */
    public function getInstallation()
    {
        return $this->container['installation'];
    }

    /**
     * Sets installation
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleInstallation|null $installation installation
     *
     * @return self
     */
    public function setInstallation($installation)
    {
        if (is_null($installation)) {
            throw new \InvalidArgumentException('non-nullable installation cannot be null');
        }
        $this->container['installation'] = $installation;

        return $this;
    }

    /**
     * Gets organization
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OrganizationSimpleWebhooks|null
     */
    public function getOrganization()
    {
        return $this->container['organization'];
    }

    /**
     * Sets organization
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OrganizationSimpleWebhooks|null $organization organization
     *
     * @return self
     */
    public function setOrganization($organization)
    {
        if (is_null($organization)) {
            throw new \InvalidArgumentException('non-nullable organization cannot be null');
        }
        $this->container['organization'] = $organization;

        return $this;
    }

    /**
     * Gets pusher
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Committer1
     */
    public function getPusher()
    {
        return $this->container['pusher'];
    }

    /**
     * Sets pusher
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Committer1 $pusher pusher
     *
     * @return self
     */
    public function setPusher($pusher)
    {
        if (is_null($pusher)) {
            throw new \InvalidArgumentException('non-nullable pusher cannot be null');
        }
        $this->container['pusher'] = $pusher;

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
     * @param string $ref The full git ref that was pushed. Example: `refs/heads/main` or `refs/tags/v3.14.1`.
     *
     * @return self
     */
    public function setRef($ref)
    {
        if (is_null($ref)) {
            throw new \InvalidArgumentException('non-nullable ref cannot be null');
        }
        $this->container['ref'] = $ref;

        return $this;
    }

    /**
     * Gets repository
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Repository2
     */
    public function getRepository()
    {
        return $this->container['repository'];
    }

    /**
     * Sets repository
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\Repository2 $repository repository
     *
     * @return self
     */
    public function setRepository($repository)
    {
        if (is_null($repository)) {
            throw new \InvalidArgumentException('non-nullable repository cannot be null');
        }
        $this->container['repository'] = $repository;

        return $this;
    }

    /**
     * Gets sender
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUserWebhooks|null
     */
    public function getSender()
    {
        return $this->container['sender'];
    }

    /**
     * Sets sender
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\SimpleUserWebhooks|null $sender sender
     *
     * @return self
     */
    public function setSender($sender)
    {
        if (is_null($sender)) {
            throw new \InvalidArgumentException('non-nullable sender cannot be null');
        }
        $this->container['sender'] = $sender;

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


