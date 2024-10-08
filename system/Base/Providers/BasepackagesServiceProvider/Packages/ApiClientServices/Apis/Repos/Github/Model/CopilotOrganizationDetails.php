<?php
/**
 * CopilotOrganizationDetails
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
 * CopilotOrganizationDetails Class Doc Comment
 *
 * @category Class
 * @description Information about the seat breakdown and policies set for an organization with a Copilot Business subscription.
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class CopilotOrganizationDetails implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'copilot-organization-details';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'seat_breakdown' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CopilotSeatBreakdown',
        'public_code_suggestions' => 'string',
        'ide_chat' => 'string',
        'platform_chat' => 'string',
        'cli' => 'string',
        'seat_management_setting' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'seat_breakdown' => null,
        'public_code_suggestions' => null,
        'ide_chat' => null,
        'platform_chat' => null,
        'cli' => null,
        'seat_management_setting' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'seat_breakdown' => false,
        'public_code_suggestions' => false,
        'ide_chat' => false,
        'platform_chat' => false,
        'cli' => false,
        'seat_management_setting' => false
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
        'seat_breakdown' => 'seat_breakdown',
        'public_code_suggestions' => 'public_code_suggestions',
        'ide_chat' => 'ide_chat',
        'platform_chat' => 'platform_chat',
        'cli' => 'cli',
        'seat_management_setting' => 'seat_management_setting'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'seat_breakdown' => 'setSeatBreakdown',
        'public_code_suggestions' => 'setPublicCodeSuggestions',
        'ide_chat' => 'setIdeChat',
        'platform_chat' => 'setPlatformChat',
        'cli' => 'setCli',
        'seat_management_setting' => 'setSeatManagementSetting'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'seat_breakdown' => 'getSeatBreakdown',
        'public_code_suggestions' => 'getPublicCodeSuggestions',
        'ide_chat' => 'getIdeChat',
        'platform_chat' => 'getPlatformChat',
        'cli' => 'getCli',
        'seat_management_setting' => 'getSeatManagementSetting'
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

    public const PUBLIC_CODE_SUGGESTIONS_ALLOW = 'allow';
    public const PUBLIC_CODE_SUGGESTIONS_BLOCK = 'block';
    public const PUBLIC_CODE_SUGGESTIONS_UNCONFIGURED = 'unconfigured';
    public const PUBLIC_CODE_SUGGESTIONS_UNKNOWN = 'unknown';
    public const IDE_CHAT_ENABLED = 'enabled';
    public const IDE_CHAT_DISABLED = 'disabled';
    public const IDE_CHAT_UNCONFIGURED = 'unconfigured';
    public const PLATFORM_CHAT_ENABLED = 'enabled';
    public const PLATFORM_CHAT_DISABLED = 'disabled';
    public const PLATFORM_CHAT_UNCONFIGURED = 'unconfigured';
    public const CLI_ENABLED = 'enabled';
    public const CLI_DISABLED = 'disabled';
    public const CLI_UNCONFIGURED = 'unconfigured';
    public const SEAT_MANAGEMENT_SETTING_ASSIGN_ALL = 'assign_all';
    public const SEAT_MANAGEMENT_SETTING_ASSIGN_SELECTED = 'assign_selected';
    public const SEAT_MANAGEMENT_SETTING_DISABLED = 'disabled';
    public const SEAT_MANAGEMENT_SETTING_UNCONFIGURED = 'unconfigured';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPublicCodeSuggestionsAllowableValues()
    {
        return [
            self::PUBLIC_CODE_SUGGESTIONS_ALLOW,
            self::PUBLIC_CODE_SUGGESTIONS_BLOCK,
            self::PUBLIC_CODE_SUGGESTIONS_UNCONFIGURED,
            self::PUBLIC_CODE_SUGGESTIONS_UNKNOWN,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getIdeChatAllowableValues()
    {
        return [
            self::IDE_CHAT_ENABLED,
            self::IDE_CHAT_DISABLED,
            self::IDE_CHAT_UNCONFIGURED,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPlatformChatAllowableValues()
    {
        return [
            self::PLATFORM_CHAT_ENABLED,
            self::PLATFORM_CHAT_DISABLED,
            self::PLATFORM_CHAT_UNCONFIGURED,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getCliAllowableValues()
    {
        return [
            self::CLI_ENABLED,
            self::CLI_DISABLED,
            self::CLI_UNCONFIGURED,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getSeatManagementSettingAllowableValues()
    {
        return [
            self::SEAT_MANAGEMENT_SETTING_ASSIGN_ALL,
            self::SEAT_MANAGEMENT_SETTING_ASSIGN_SELECTED,
            self::SEAT_MANAGEMENT_SETTING_DISABLED,
            self::SEAT_MANAGEMENT_SETTING_UNCONFIGURED,
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
        $this->setIfExists('seat_breakdown', $data ?? [], null);
        $this->setIfExists('public_code_suggestions', $data ?? [], null);
        $this->setIfExists('ide_chat', $data ?? [], null);
        $this->setIfExists('platform_chat', $data ?? [], null);
        $this->setIfExists('cli', $data ?? [], null);
        $this->setIfExists('seat_management_setting', $data ?? [], null);
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

        if ($this->container['seat_breakdown'] === null) {
            $invalidProperties[] = "'seat_breakdown' can't be null";
        }
        if ($this->container['public_code_suggestions'] === null) {
            $invalidProperties[] = "'public_code_suggestions' can't be null";
        }
        $allowedValues = $this->getPublicCodeSuggestionsAllowableValues();
        if (!is_null($this->container['public_code_suggestions']) && !in_array($this->container['public_code_suggestions'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'public_code_suggestions', must be one of '%s'",
                $this->container['public_code_suggestions'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getIdeChatAllowableValues();
        if (!is_null($this->container['ide_chat']) && !in_array($this->container['ide_chat'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'ide_chat', must be one of '%s'",
                $this->container['ide_chat'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getPlatformChatAllowableValues();
        if (!is_null($this->container['platform_chat']) && !in_array($this->container['platform_chat'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'platform_chat', must be one of '%s'",
                $this->container['platform_chat'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getCliAllowableValues();
        if (!is_null($this->container['cli']) && !in_array($this->container['cli'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'cli', must be one of '%s'",
                $this->container['cli'],
                implode("', '", $allowedValues)
            );
        }

        if ($this->container['seat_management_setting'] === null) {
            $invalidProperties[] = "'seat_management_setting' can't be null";
        }
        $allowedValues = $this->getSeatManagementSettingAllowableValues();
        if (!is_null($this->container['seat_management_setting']) && !in_array($this->container['seat_management_setting'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'seat_management_setting', must be one of '%s'",
                $this->container['seat_management_setting'],
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
     * Gets seat_breakdown
     *
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CopilotSeatBreakdown
     */
    public function getSeatBreakdown()
    {
        return $this->container['seat_breakdown'];
    }

    /**
     * Sets seat_breakdown
     *
     * @param \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\CopilotSeatBreakdown $seat_breakdown seat_breakdown
     *
     * @return self
     */
    public function setSeatBreakdown($seat_breakdown)
    {
        if (is_null($seat_breakdown)) {
            throw new \InvalidArgumentException('non-nullable seat_breakdown cannot be null');
        }
        $this->container['seat_breakdown'] = $seat_breakdown;

        return $this;
    }

    /**
     * Gets public_code_suggestions
     *
     * @return string
     */
    public function getPublicCodeSuggestions()
    {
        return $this->container['public_code_suggestions'];
    }

    /**
     * Sets public_code_suggestions
     *
     * @param string $public_code_suggestions The organization policy for allowing or disallowing Copilot to make suggestions that match public code.
     *
     * @return self
     */
    public function setPublicCodeSuggestions($public_code_suggestions)
    {
        if (is_null($public_code_suggestions)) {
            throw new \InvalidArgumentException('non-nullable public_code_suggestions cannot be null');
        }
        $allowedValues = $this->getPublicCodeSuggestionsAllowableValues();
        if (!in_array($public_code_suggestions, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'public_code_suggestions', must be one of '%s'",
                    $public_code_suggestions,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['public_code_suggestions'] = $public_code_suggestions;

        return $this;
    }

    /**
     * Gets ide_chat
     *
     * @return string|null
     */
    public function getIdeChat()
    {
        return $this->container['ide_chat'];
    }

    /**
     * Sets ide_chat
     *
     * @param string|null $ide_chat The organization policy for allowing or disallowing organization members to use Copilot Chat within their editor.
     *
     * @return self
     */
    public function setIdeChat($ide_chat)
    {
        if (is_null($ide_chat)) {
            throw new \InvalidArgumentException('non-nullable ide_chat cannot be null');
        }
        $allowedValues = $this->getIdeChatAllowableValues();
        if (!in_array($ide_chat, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'ide_chat', must be one of '%s'",
                    $ide_chat,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['ide_chat'] = $ide_chat;

        return $this;
    }

    /**
     * Gets platform_chat
     *
     * @return string|null
     */
    public function getPlatformChat()
    {
        return $this->container['platform_chat'];
    }

    /**
     * Sets platform_chat
     *
     * @param string|null $platform_chat The organization policy for allowing or disallowing organization members to use Copilot features within github.com.
     *
     * @return self
     */
    public function setPlatformChat($platform_chat)
    {
        if (is_null($platform_chat)) {
            throw new \InvalidArgumentException('non-nullable platform_chat cannot be null');
        }
        $allowedValues = $this->getPlatformChatAllowableValues();
        if (!in_array($platform_chat, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'platform_chat', must be one of '%s'",
                    $platform_chat,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['platform_chat'] = $platform_chat;

        return $this;
    }

    /**
     * Gets cli
     *
     * @return string|null
     */
    public function getCli()
    {
        return $this->container['cli'];
    }

    /**
     * Sets cli
     *
     * @param string|null $cli The organization policy for allowing or disallowing organization members to use Copilot within their CLI.
     *
     * @return self
     */
    public function setCli($cli)
    {
        if (is_null($cli)) {
            throw new \InvalidArgumentException('non-nullable cli cannot be null');
        }
        $allowedValues = $this->getCliAllowableValues();
        if (!in_array($cli, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'cli', must be one of '%s'",
                    $cli,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['cli'] = $cli;

        return $this;
    }

    /**
     * Gets seat_management_setting
     *
     * @return string
     */
    public function getSeatManagementSetting()
    {
        return $this->container['seat_management_setting'];
    }

    /**
     * Sets seat_management_setting
     *
     * @param string $seat_management_setting The mode of assigning new seats.
     *
     * @return self
     */
    public function setSeatManagementSetting($seat_management_setting)
    {
        if (is_null($seat_management_setting)) {
            throw new \InvalidArgumentException('non-nullable seat_management_setting cannot be null');
        }
        $allowedValues = $this->getSeatManagementSettingAllowableValues();
        if (!in_array($seat_management_setting, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'seat_management_setting', must be one of '%s'",
                    $seat_management_setting,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['seat_management_setting'] = $seat_management_setting;

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


