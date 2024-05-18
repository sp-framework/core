<?php
/**
 * DependabotAlertSecurityAdvisory
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
 * DependabotAlertSecurityAdvisory Class Doc Comment
 *
 * @category Class
 * @description Details for the GitHub Security Advisory.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class DependabotAlertSecurityAdvisory implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'dependabot-alert-security-advisory';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'ghsa_id' => 'string',
        'cve_id' => 'string',
        'summary' => 'string',
        'description' => 'string',
        'vulnerabilities' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityVulnerability[]',
        'severity' => 'string',
        'cvss' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryCvss',
        'cwes' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryCwesInner[]',
        'identifiers' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryIdentifiersInner[]',
        'references' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryReferencesInner[]',
        'published_at' => '\DateTime',
        'updated_at' => '\DateTime',
        'withdrawn_at' => '\DateTime'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'ghsa_id' => null,
        'cve_id' => null,
        'summary' => null,
        'description' => null,
        'vulnerabilities' => null,
        'severity' => null,
        'cvss' => null,
        'cwes' => null,
        'identifiers' => null,
        'references' => null,
        'published_at' => 'date-time',
        'updated_at' => 'date-time',
        'withdrawn_at' => 'date-time'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'ghsa_id' => false,
        'cve_id' => true,
        'summary' => false,
        'description' => false,
        'vulnerabilities' => false,
        'severity' => false,
        'cvss' => false,
        'cwes' => false,
        'identifiers' => false,
        'references' => false,
        'published_at' => false,
        'updated_at' => false,
        'withdrawn_at' => true
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
        'ghsa_id' => 'ghsa_id',
        'cve_id' => 'cve_id',
        'summary' => 'summary',
        'description' => 'description',
        'vulnerabilities' => 'vulnerabilities',
        'severity' => 'severity',
        'cvss' => 'cvss',
        'cwes' => 'cwes',
        'identifiers' => 'identifiers',
        'references' => 'references',
        'published_at' => 'published_at',
        'updated_at' => 'updated_at',
        'withdrawn_at' => 'withdrawn_at'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'ghsa_id' => 'setGhsaId',
        'cve_id' => 'setCveId',
        'summary' => 'setSummary',
        'description' => 'setDescription',
        'vulnerabilities' => 'setVulnerabilities',
        'severity' => 'setSeverity',
        'cvss' => 'setCvss',
        'cwes' => 'setCwes',
        'identifiers' => 'setIdentifiers',
        'references' => 'setReferences',
        'published_at' => 'setPublishedAt',
        'updated_at' => 'setUpdatedAt',
        'withdrawn_at' => 'setWithdrawnAt'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'ghsa_id' => 'getGhsaId',
        'cve_id' => 'getCveId',
        'summary' => 'getSummary',
        'description' => 'getDescription',
        'vulnerabilities' => 'getVulnerabilities',
        'severity' => 'getSeverity',
        'cvss' => 'getCvss',
        'cwes' => 'getCwes',
        'identifiers' => 'getIdentifiers',
        'references' => 'getReferences',
        'published_at' => 'getPublishedAt',
        'updated_at' => 'getUpdatedAt',
        'withdrawn_at' => 'getWithdrawnAt'
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

    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';
    public const SEVERITY_CRITICAL = 'critical';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getSeverityAllowableValues()
    {
        return [
            self::SEVERITY_LOW,
            self::SEVERITY_MEDIUM,
            self::SEVERITY_HIGH,
            self::SEVERITY_CRITICAL,
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
        $this->setIfExists('ghsa_id', $data ?? [], null);
        $this->setIfExists('cve_id', $data ?? [], null);
        $this->setIfExists('summary', $data ?? [], null);
        $this->setIfExists('description', $data ?? [], null);
        $this->setIfExists('vulnerabilities', $data ?? [], null);
        $this->setIfExists('severity', $data ?? [], null);
        $this->setIfExists('cvss', $data ?? [], null);
        $this->setIfExists('cwes', $data ?? [], null);
        $this->setIfExists('identifiers', $data ?? [], null);
        $this->setIfExists('references', $data ?? [], null);
        $this->setIfExists('published_at', $data ?? [], null);
        $this->setIfExists('updated_at', $data ?? [], null);
        $this->setIfExists('withdrawn_at', $data ?? [], null);
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

        if ($this->container['ghsa_id'] === null) {
            $invalidProperties[] = "'ghsa_id' can't be null";
        }
        if ($this->container['cve_id'] === null) {
            $invalidProperties[] = "'cve_id' can't be null";
        }
        if ($this->container['summary'] === null) {
            $invalidProperties[] = "'summary' can't be null";
        }
        if ((mb_strlen($this->container['summary']) > 1024)) {
            $invalidProperties[] = "invalid value for 'summary', the character length must be smaller than or equal to 1024.";
        }

        if ($this->container['description'] === null) {
            $invalidProperties[] = "'description' can't be null";
        }
        if ($this->container['vulnerabilities'] === null) {
            $invalidProperties[] = "'vulnerabilities' can't be null";
        }
        if ($this->container['severity'] === null) {
            $invalidProperties[] = "'severity' can't be null";
        }
        $allowedValues = $this->getSeverityAllowableValues();
        if (!is_null($this->container['severity']) && !in_array($this->container['severity'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'severity', must be one of '%s'",
                $this->container['severity'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['cvss'] === null) {
            $invalidProperties[] = "'cvss' can't be null";
        }
        if ($this->container['cwes'] === null) {
            $invalidProperties[] = "'cwes' can't be null";
        }
        if ($this->container['identifiers'] === null) {
            $invalidProperties[] = "'identifiers' can't be null";
        }
        if ($this->container['references'] === null) {
            $invalidProperties[] = "'references' can't be null";
        }
        if ($this->container['published_at'] === null) {
            $invalidProperties[] = "'published_at' can't be null";
        }
        if ($this->container['updated_at'] === null) {
            $invalidProperties[] = "'updated_at' can't be null";
        }
        if ($this->container['withdrawn_at'] === null) {
            $invalidProperties[] = "'withdrawn_at' can't be null";
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
     * Gets ghsa_id
     *
     * @return string
     */
    public function getGhsaId()
    {
        return $this->container['ghsa_id'];
    }

    /**
     * Sets ghsa_id
     *
     * @param string $ghsa_id The unique GitHub Security Advisory ID assigned to the advisory.
     *
     * @return self
     */
    public function setGhsaId($ghsa_id)
    {
        if (is_null($ghsa_id)) {
            throw new \InvalidArgumentException('non-nullable ghsa_id cannot be null');
        }
        $this->container['ghsa_id'] = $ghsa_id;

        return $this;
    }

    /**
     * Gets cve_id
     *
     * @return string
     */
    public function getCveId()
    {
        return $this->container['cve_id'];
    }

    /**
     * Sets cve_id
     *
     * @param string $cve_id The unique CVE ID assigned to the advisory.
     *
     * @return self
     */
    public function setCveId($cve_id)
    {
        if (is_null($cve_id)) {
            array_push($this->openAPINullablesSetToNull, 'cve_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cve_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cve_id'] = $cve_id;

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
     * @param string $summary A short, plain text summary of the advisory.
     *
     * @return self
     */
    public function setSummary($summary)
    {
        if (is_null($summary)) {
            throw new \InvalidArgumentException('non-nullable summary cannot be null');
        }
        if ((mb_strlen($summary) > 1024)) {
            throw new \InvalidArgumentException('invalid length for $summary when calling DependabotAlertSecurityAdvisory., must be smaller than or equal to 1024.');
        }

        $this->container['summary'] = $summary;

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
     * @param string $description A long-form Markdown-supported description of the advisory.
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
     * Gets vulnerabilities
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityVulnerability[]
     */
    public function getVulnerabilities()
    {
        return $this->container['vulnerabilities'];
    }

    /**
     * Sets vulnerabilities
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityVulnerability[] $vulnerabilities Vulnerable version range information for the advisory.
     *
     * @return self
     */
    public function setVulnerabilities($vulnerabilities)
    {
        if (is_null($vulnerabilities)) {
            throw new \InvalidArgumentException('non-nullable vulnerabilities cannot be null');
        }
        $this->container['vulnerabilities'] = $vulnerabilities;

        return $this;
    }

    /**
     * Gets severity
     *
     * @return string
     */
    public function getSeverity()
    {
        return $this->container['severity'];
    }

    /**
     * Sets severity
     *
     * @param string $severity The severity of the advisory.
     *
     * @return self
     */
    public function setSeverity($severity)
    {
        if (is_null($severity)) {
            throw new \InvalidArgumentException('non-nullable severity cannot be null');
        }
        $allowedValues = $this->getSeverityAllowableValues();
        if (!in_array($severity, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'severity', must be one of '%s'",
                    $severity,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['severity'] = $severity;

        return $this;
    }

    /**
     * Gets cvss
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryCvss
     */
    public function getCvss()
    {
        return $this->container['cvss'];
    }

    /**
     * Sets cvss
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryCvss $cvss cvss
     *
     * @return self
     */
    public function setCvss($cvss)
    {
        if (is_null($cvss)) {
            throw new \InvalidArgumentException('non-nullable cvss cannot be null');
        }
        $this->container['cvss'] = $cvss;

        return $this;
    }

    /**
     * Gets cwes
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryCwesInner[]
     */
    public function getCwes()
    {
        return $this->container['cwes'];
    }

    /**
     * Sets cwes
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryCwesInner[] $cwes Details for the advisory pertaining to Common Weakness Enumeration.
     *
     * @return self
     */
    public function setCwes($cwes)
    {
        if (is_null($cwes)) {
            throw new \InvalidArgumentException('non-nullable cwes cannot be null');
        }
        $this->container['cwes'] = $cwes;

        return $this;
    }

    /**
     * Gets identifiers
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryIdentifiersInner[]
     */
    public function getIdentifiers()
    {
        return $this->container['identifiers'];
    }

    /**
     * Sets identifiers
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryIdentifiersInner[] $identifiers Values that identify this advisory among security information sources.
     *
     * @return self
     */
    public function setIdentifiers($identifiers)
    {
        if (is_null($identifiers)) {
            throw new \InvalidArgumentException('non-nullable identifiers cannot be null');
        }
        $this->container['identifiers'] = $identifiers;

        return $this;
    }

    /**
     * Gets references
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryReferencesInner[]
     */
    public function getReferences()
    {
        return $this->container['references'];
    }

    /**
     * Sets references
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\DependabotAlertSecurityAdvisoryReferencesInner[] $references Links to additional advisory information.
     *
     * @return self
     */
    public function setReferences($references)
    {
        if (is_null($references)) {
            throw new \InvalidArgumentException('non-nullable references cannot be null');
        }
        $this->container['references'] = $references;

        return $this;
    }

    /**
     * Gets published_at
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->container['published_at'];
    }

    /**
     * Sets published_at
     *
     * @param \DateTime $published_at The time that the advisory was published in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setPublishedAt($published_at)
    {
        if (is_null($published_at)) {
            throw new \InvalidArgumentException('non-nullable published_at cannot be null');
        }
        $this->container['published_at'] = $published_at;

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->container['updated_at'];
    }

    /**
     * Sets updated_at
     *
     * @param \DateTime $updated_at The time that the advisory was last modified in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`.
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
     * Gets withdrawn_at
     *
     * @return \DateTime
     */
    public function getWithdrawnAt()
    {
        return $this->container['withdrawn_at'];
    }

    /**
     * Sets withdrawn_at
     *
     * @param \DateTime $withdrawn_at The time that the advisory was withdrawn in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`.
     *
     * @return self
     */
    public function setWithdrawnAt($withdrawn_at)
    {
        if (is_null($withdrawn_at)) {
            array_push($this->openAPINullablesSetToNull, 'withdrawn_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('withdrawn_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['withdrawn_at'] = $withdrawn_at;

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


