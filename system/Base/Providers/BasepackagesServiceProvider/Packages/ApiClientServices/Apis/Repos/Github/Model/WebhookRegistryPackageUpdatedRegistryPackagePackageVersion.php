<?php
/**
 * WebhookRegistryPackageUpdatedRegistryPackagePackageVersion
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
 * WebhookRegistryPackageUpdatedRegistryPackagePackageVersion Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class WebhookRegistryPackageUpdatedRegistryPackagePackageVersion implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'webhook_registry_package_updated_registry_package_package_version';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'author' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackagePublishedRegistryPackageOwner',
        'body' => 'string',
        'body_html' => 'string',
        'created_at' => 'string',
        'description' => 'string',
        'docker_metadata' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionDockerMetadataInner[]',
        'draft' => 'bool',
        'html_url' => 'string',
        'id' => 'int',
        'installation_command' => 'string',
        'manifest' => 'string',
        'metadata' => 'array<string,mixed>[]',
        'name' => 'string',
        'package_files' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionPackageFilesInner[]',
        'package_url' => 'string',
        'prerelease' => 'bool',
        'release' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionRelease',
        'rubygems_metadata' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRubygemsMetadata[]',
        'summary' => 'string',
        'tag_name' => 'string',
        'target_commitish' => 'string',
        'target_oid' => 'string',
        'updated_at' => 'string',
        'version' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'author' => null,
        'body' => null,
        'body_html' => null,
        'created_at' => null,
        'description' => null,
        'docker_metadata' => null,
        'draft' => null,
        'html_url' => null,
        'id' => null,
        'installation_command' => null,
        'manifest' => null,
        'metadata' => null,
        'name' => null,
        'package_files' => null,
        'package_url' => null,
        'prerelease' => null,
        'release' => null,
        'rubygems_metadata' => null,
        'summary' => null,
        'tag_name' => null,
        'target_commitish' => null,
        'target_oid' => null,
        'updated_at' => null,
        'version' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'author' => false,
        'body' => false,
        'body_html' => false,
        'created_at' => false,
        'description' => false,
        'docker_metadata' => false,
        'draft' => false,
        'html_url' => false,
        'id' => false,
        'installation_command' => false,
        'manifest' => false,
        'metadata' => false,
        'name' => false,
        'package_files' => false,
        'package_url' => false,
        'prerelease' => false,
        'release' => false,
        'rubygems_metadata' => false,
        'summary' => false,
        'tag_name' => false,
        'target_commitish' => false,
        'target_oid' => false,
        'updated_at' => false,
        'version' => false
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
        'author' => 'author',
        'body' => 'body',
        'body_html' => 'body_html',
        'created_at' => 'created_at',
        'description' => 'description',
        'docker_metadata' => 'docker_metadata',
        'draft' => 'draft',
        'html_url' => 'html_url',
        'id' => 'id',
        'installation_command' => 'installation_command',
        'manifest' => 'manifest',
        'metadata' => 'metadata',
        'name' => 'name',
        'package_files' => 'package_files',
        'package_url' => 'package_url',
        'prerelease' => 'prerelease',
        'release' => 'release',
        'rubygems_metadata' => 'rubygems_metadata',
        'summary' => 'summary',
        'tag_name' => 'tag_name',
        'target_commitish' => 'target_commitish',
        'target_oid' => 'target_oid',
        'updated_at' => 'updated_at',
        'version' => 'version'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'author' => 'setAuthor',
        'body' => 'setBody',
        'body_html' => 'setBodyHtml',
        'created_at' => 'setCreatedAt',
        'description' => 'setDescription',
        'docker_metadata' => 'setDockerMetadata',
        'draft' => 'setDraft',
        'html_url' => 'setHtmlUrl',
        'id' => 'setId',
        'installation_command' => 'setInstallationCommand',
        'manifest' => 'setManifest',
        'metadata' => 'setMetadata',
        'name' => 'setName',
        'package_files' => 'setPackageFiles',
        'package_url' => 'setPackageUrl',
        'prerelease' => 'setPrerelease',
        'release' => 'setRelease',
        'rubygems_metadata' => 'setRubygemsMetadata',
        'summary' => 'setSummary',
        'tag_name' => 'setTagName',
        'target_commitish' => 'setTargetCommitish',
        'target_oid' => 'setTargetOid',
        'updated_at' => 'setUpdatedAt',
        'version' => 'setVersion'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'author' => 'getAuthor',
        'body' => 'getBody',
        'body_html' => 'getBodyHtml',
        'created_at' => 'getCreatedAt',
        'description' => 'getDescription',
        'docker_metadata' => 'getDockerMetadata',
        'draft' => 'getDraft',
        'html_url' => 'getHtmlUrl',
        'id' => 'getId',
        'installation_command' => 'getInstallationCommand',
        'manifest' => 'getManifest',
        'metadata' => 'getMetadata',
        'name' => 'getName',
        'package_files' => 'getPackageFiles',
        'package_url' => 'getPackageUrl',
        'prerelease' => 'getPrerelease',
        'release' => 'getRelease',
        'rubygems_metadata' => 'getRubygemsMetadata',
        'summary' => 'getSummary',
        'tag_name' => 'getTagName',
        'target_commitish' => 'getTargetCommitish',
        'target_oid' => 'getTargetOid',
        'updated_at' => 'getUpdatedAt',
        'version' => 'getVersion'
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
        $this->setIfExists('author', $data ?? [], null);
        $this->setIfExists('body', $data ?? [], null);
        $this->setIfExists('body_html', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('docker_metadata', $data ?? [], null);
        $this->setIfExists('draft', $data ?? [], null);
        $this->setIfExists('html_url', $data ?? [], null);
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('installation_command', $data ?? [], null);
        $this->setIfExists('manifest', $data ?? [], null);
        $this->setIfExists('metadata', $data ?? [], null);
        $this->setIfExists('name', $data ?? [], null);
        $this->setIfExists('package_files', $data ?? [], null);
        $this->setIfExists('package_url', $data ?? [], null);
        $this->setIfExists('prerelease', $data ?? [], null);
        $this->setIfExists('release', $data ?? [], null);
        $this->setIfExists('rubygems_metadata', $data ?? [], null);
        $this->setIfExists('summary', $data ?? [], null);
        $this->setIfExists('tag_name', $data ?? [], null);
        $this->setIfExists('target_commitish', $data ?? [], null);
        $this->setIfExists('target_oid', $data ?? [], null);
        $this->setIfExists('updated_at', $data ?? [], null);
        $this->setIfExists('version', $data ?? [], null);
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

        if ($this->container['author'] === null) {
            $invalidProperties[] = "'author' can't be null";
        }
        if ($this->container['body'] === null) {
            $invalidProperties[] = "'body' can't be null";
        }
        if ($this->container['body_html'] === null) {
            $invalidProperties[] = "'body_html' can't be null";
        }
        if ($this->container['created_at'] === null) {
            $invalidProperties[] = "'created_at' can't be null";
        }
        if ($this->container['description'] === null) {
            $invalidProperties[] = "'description' can't be null";
        }
        if ($this->container['html_url'] === null) {
            $invalidProperties[] = "'html_url' can't be null";
        }
        if ($this->container['id'] === null) {
            $invalidProperties[] = "'id' can't be null";
        }
        if ($this->container['installation_command'] === null) {
            $invalidProperties[] = "'installation_command' can't be null";
        }
        if ($this->container['metadata'] === null) {
            $invalidProperties[] = "'metadata' can't be null";
        }
        if ($this->container['name'] === null) {
            $invalidProperties[] = "'name' can't be null";
        }
        if ($this->container['package_files'] === null) {
            $invalidProperties[] = "'package_files' can't be null";
        }
        if ($this->container['package_url'] === null) {
            $invalidProperties[] = "'package_url' can't be null";
        }
        if ($this->container['summary'] === null) {
            $invalidProperties[] = "'summary' can't be null";
        }
        if ($this->container['target_commitish'] === null) {
            $invalidProperties[] = "'target_commitish' can't be null";
        }
        if ($this->container['target_oid'] === null) {
            $invalidProperties[] = "'target_oid' can't be null";
        }
        if ($this->container['updated_at'] === null) {
            $invalidProperties[] = "'updated_at' can't be null";
        }
        if ($this->container['version'] === null) {
            $invalidProperties[] = "'version' can't be null";
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
     * Gets author
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackagePublishedRegistryPackageOwner
     */
    public function getAuthor()
    {
        return $this->container['author'];
    }

    /**
     * Sets author
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackagePublishedRegistryPackageOwner $author author
     *
     * @return self
     */
    public function setAuthor($author)
    {
        if (is_null($author)) {
            throw new \InvalidArgumentException('non-nullable author cannot be null');
        }
        $this->container['author'] = $author;

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
     * Gets body_html
     *
     * @return string
     */
    public function getBodyHtml()
    {
        return $this->container['body_html'];
    }

    /**
     * Sets body_html
     *
     * @param string $body_html body_html
     *
     * @return self
     */
    public function setBodyHtml($body_html)
    {
        if (is_null($body_html)) {
            throw new \InvalidArgumentException('non-nullable body_html cannot be null');
        }
        $this->container['body_html'] = $body_html;

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
     * @return self
     */
    public function setDescription($description)
    {
        if (is_null($description)) {
            throw new \InvalidArgumentException('non-nullable description cannot be null');
        }
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * Gets docker_metadata
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionDockerMetadataInner[]|null
     */
    public function getDockerMetadata()
    {
        return $this->container['docker_metadata'];
    }

    /**
     * Sets docker_metadata
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionDockerMetadataInner[]|null $docker_metadata docker_metadata
     *
     * @return self
     */
    public function setDockerMetadata($docker_metadata)
    {
        if (is_null($docker_metadata)) {
            throw new \InvalidArgumentException('non-nullable docker_metadata cannot be null');
        }
        $this->container['docker_metadata'] = $docker_metadata;

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
     * @param bool|null $draft draft
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
     * Gets html_url
     *
     * @return string
     */
    public function getHtmlUrl()
    {
        return $this->container['html_url'];
    }

    /**
     * Sets html_url
     *
     * @param string $html_url html_url
     *
     * @return self
     */
    public function setHtmlUrl($html_url)
    {
        if (is_null($html_url)) {
            throw new \InvalidArgumentException('non-nullable html_url cannot be null');
        }
        $this->container['html_url'] = $html_url;

        return $this;
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
     * Gets installation_command
     *
     * @return string
     */
    public function getInstallationCommand()
    {
        return $this->container['installation_command'];
    }

    /**
     * Sets installation_command
     *
     * @param string $installation_command installation_command
     *
     * @return self
     */
    public function setInstallationCommand($installation_command)
    {
        if (is_null($installation_command)) {
            throw new \InvalidArgumentException('non-nullable installation_command cannot be null');
        }
        $this->container['installation_command'] = $installation_command;

        return $this;
    }

    /**
     * Gets manifest
     *
     * @return string|null
     */
    public function getManifest()
    {
        return $this->container['manifest'];
    }

    /**
     * Sets manifest
     *
     * @param string|null $manifest manifest
     *
     * @return self
     */
    public function setManifest($manifest)
    {
        if (is_null($manifest)) {
            throw new \InvalidArgumentException('non-nullable manifest cannot be null');
        }
        $this->container['manifest'] = $manifest;

        return $this;
    }

    /**
     * Gets metadata
     *
     * @return array<string,mixed>[]
     */
    public function getMetadata()
    {
        return $this->container['metadata'];
    }

    /**
     * Sets metadata
     *
     * @param array<string,mixed>[] $metadata metadata
     *
     * @return self
     */
    public function setMetadata($metadata)
    {
        if (is_null($metadata)) {
            throw new \InvalidArgumentException('non-nullable metadata cannot be null');
        }
        $this->container['metadata'] = $metadata;

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
     * Gets package_files
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionPackageFilesInner[]
     */
    public function getPackageFiles()
    {
        return $this->container['package_files'];
    }

    /**
     * Sets package_files
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionPackageFilesInner[] $package_files package_files
     *
     * @return self
     */
    public function setPackageFiles($package_files)
    {
        if (is_null($package_files)) {
            throw new \InvalidArgumentException('non-nullable package_files cannot be null');
        }
        $this->container['package_files'] = $package_files;

        return $this;
    }

    /**
     * Gets package_url
     *
     * @return string
     */
    public function getPackageUrl()
    {
        return $this->container['package_url'];
    }

    /**
     * Sets package_url
     *
     * @param string $package_url package_url
     *
     * @return self
     */
    public function setPackageUrl($package_url)
    {
        if (is_null($package_url)) {
            throw new \InvalidArgumentException('non-nullable package_url cannot be null');
        }
        $this->container['package_url'] = $package_url;

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
     * @param bool|null $prerelease prerelease
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
     * Gets release
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionRelease|null
     */
    public function getRelease()
    {
        return $this->container['release'];
    }

    /**
     * Sets release
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRegistryPackageUpdatedRegistryPackagePackageVersionRelease|null $release release
     *
     * @return self
     */
    public function setRelease($release)
    {
        if (is_null($release)) {
            throw new \InvalidArgumentException('non-nullable release cannot be null');
        }
        $this->container['release'] = $release;

        return $this;
    }

    /**
     * Gets rubygems_metadata
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRubygemsMetadata[]|null
     */
    public function getRubygemsMetadata()
    {
        return $this->container['rubygems_metadata'];
    }

    /**
     * Sets rubygems_metadata
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\WebhookRubygemsMetadata[]|null $rubygems_metadata rubygems_metadata
     *
     * @return self
     */
    public function setRubygemsMetadata($rubygems_metadata)
    {
        if (is_null($rubygems_metadata)) {
            throw new \InvalidArgumentException('non-nullable rubygems_metadata cannot be null');
        }
        $this->container['rubygems_metadata'] = $rubygems_metadata;

        return $this;
    }

    /**
     * Gets summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->container['summary'];
    }

    /**
     * Sets summary
     *
     * @param string $summary summary
     *
     * @return self
     */
    public function setSummary($summary)
    {
        if (is_null($summary)) {
            throw new \InvalidArgumentException('non-nullable summary cannot be null');
        }
        $this->container['summary'] = $summary;

        return $this;
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
     * @param string|null $tag_name tag_name
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
     * @return string
     */
    public function getTargetCommitish()
    {
        return $this->container['target_commitish'];
    }

    /**
     * Sets target_commitish
     *
     * @param string $target_commitish target_commitish
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
     * Gets target_oid
     *
     * @return string
     */
    public function getTargetOid()
    {
        return $this->container['target_oid'];
    }

    /**
     * Sets target_oid
     *
     * @param string $target_oid target_oid
     *
     * @return self
     */
    public function setTargetOid($target_oid)
    {
        if (is_null($target_oid)) {
            throw new \InvalidArgumentException('non-nullable target_oid cannot be null');
        }
        $this->container['target_oid'] = $target_oid;

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->container['updated_at'];
    }

    /**
     * Sets updated_at
     *
     * @param string $updated_at updated_at
     *
     * @return self
     */
    public function setUpdatedAt($updated_at)
    {
        if (is_null($updated_at)) {
            throw new \InvalidArgumentException('non-nullable updated_at cannot be null');
        }
        $this->container['updated_at'] = $updated_at;

        return $this;
    }

    /**
     * Gets version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->container['version'];
    }

    /**
     * Sets version
     *
     * @param string $version version
     *
     * @return self
     */
    public function setVersion($version)
    {
        if (is_null($version)) {
            throw new \InvalidArgumentException('non-nullable version cannot be null');
        }
        $this->container['version'] = $version;

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


