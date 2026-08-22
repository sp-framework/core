<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Write
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Write;

use ErrorException;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToWriteFile;

/**
 * Generates and writes framework configuration files (Base.php, .dbkeys).
 *
 * @package System\Base\Installer\Packages\Setup\Write
 */
class Configs
{
    /**
     * Dependency injection container or services array.
     *
     * @var mixed
     */
    protected mixed $container;

    /**
     * POST parameters received from setup wizard.
     *
     * @var array<string, mixed>
     */
    protected array $postData;

    /**
     * Decoded core package.json content array.
     *
     * @var array<string, mixed>|null
     */
    protected ?array $coreJson;

    /**
     * Rendered PHP code for system/Configs/Base.php.
     *
     * @var string|null
     */
    protected ?string $baseFileContent = null;

    /**
     * Configs constructor.
     *
     * @param mixed                      $container DI container.
     * @param array<string, mixed>       $postData Setup wizard payload.
     * @param array<string, mixed>|null  $coreJson Core package metadata.
     */
    public function __construct(mixed $container, array $postData = [], ?array $coreJson = null)
    {
        $this->container = $container;
        $this->postData = $postData;
        $this->coreJson = $coreJson;
    }

    /**
     * Writes configuration and returns updated core JSON array.
     *
     * @param bool $writeBaseFile Whether to write the base file immediately.
     *
     * @return array<string, mixed>|null Updated core metadata array.
     */
    public function write(bool $writeBaseFile = false): ?array
    {
        if ($writeBaseFile) {
            $this->writeBaseFile();

            return $this->coreJson;
        }

        $this->writeBaseConfig();

        return $this->coreJson;
    }

    /**
     * Reverts system/Configs/Base.php to uninstalled setup state.
     *
     * @return array<string, mixed>|null
     */
    public function revert(): ?array
    {
        return $this->writeBaseConfig(true);
    }

    /**
     * Compiles PHP array structure for system/Configs/Base.php.
     *
     * @param bool $revert If true, writes uninstalled configuration state.
     *
     * @return array<string, mixed>|null
     */
    protected function writeBaseConfig(bool $revert = false): ?array
    {
        if ($revert) {
            $this->baseFileContent =
'<?php

declare(strict_types=1);

return [
    "setup"             => true,
    "databasetype"      => "hybrid",
    "db"                => [],
    "ff"                => [
        "databaseDir"                   => "sp/"
    ],
    "cache"             => [
        "enabled"                       => false,
        "timeout"                       => 60,
        "service"                       => "streamCache"
    ],
    "security"          => [
        "sso"                           => false,
        "passwordPolicy"                => false
    ],
    "logs"              => [
        "enabled"                       => true,
        "exceptions"                    => true,
        "level"                         => "DEBUG",
        "service"                       => "streamLogs",
        "emergencyLogsEmail"            => false,
        "emergencyLogsEmailAddresses"   => ""
    ],
    "websocket"         => [
        "protocol"                      => "tcp",
        "host"                          => "localhost",
        "port"                          => 5555
    ],
    "timeout"           => [
        "cookies"                       => 86400,
        "session_idle"                  => 86400,
        "session_absolute"              => 2592000
    ],
    "locale"            => [
        "country_iso3"                  => "",
        "timezone"                      => ""
    ]
];';
            $this->writeBaseFile();

            return $this->coreJson;
        }

        if (isset($this->postData['pwf'], $this->postData['cwf']) &&
            isset($this->postData['auto-encrypt-level']) &&
            $this->postData['auto-encrypt-level'] === 'false'
        ) {
            $pwf = (int) $this->postData['pwf'];
            $cwf = (int) $this->postData['cwf'];
        } elseif (
            isset($this->coreJson['settings']['security']['passwordWorkFactor'], $this->coreJson['settings']['security']['cookiesWorkFactor']) &&
            is_int($this->coreJson['settings']['security']['passwordWorkFactor']) &&
            is_int($this->coreJson['settings']['security']['cookiesWorkFactor'])
        ) {
            $pwf = (int) $this->coreJson['settings']['security']['passwordWorkFactor'];
            $cwf = (int) $this->coreJson['settings']['security']['cookiesWorkFactor'];
        } else {
            $workFactor = (int) $this->getWorkFactor();
            $pwf = (int) floor($workFactor * 2);
            $cwf = (int) floor($workFactor);
        }

        if (isset($this->postData['dev']) && $this->postData['dev'] === 'false') {
            $debug = 'false';
            $cache = 'true';
            $logsEnabled = 'true';
            $logsExceptions = 'true';
            $logLevel = 'INFO';
            $logsEmail = 'false';
            $dev = 'false';
        } elseif (isset($this->postData['dev']) && $this->postData['dev'] === 'true') {
            $debug = 'true';
            $cache = 'false';
            $logsEnabled = 'true';
            $logsExceptions = 'false';
            $logLevel = 'DEBUG';
            $logsEmail = 'true';
            $dev = 'true';
        } else {
            $debug = (isset($this->coreJson['settings']['debug']) && (string) $this->coreJson['settings']['debug'] === 'true') ? 'true' : 'false';
            $cache = (isset($this->coreJson['settings']['cache']['enabled']) && (string) $this->coreJson['settings']['cache']['enabled'] === 'true') ? 'true' : 'false';
            $logsEnabled = (isset($this->coreJson['settings']['logs']['enabled']) && (string) $this->coreJson['settings']['logs']['enabled'] === 'true') ? 'true' : 'false';
            $logsExceptions = (isset($this->coreJson['settings']['logs']['exceptions']) && (string) $this->coreJson['settings']['logs']['exceptions'] === 'true') ? 'true' : 'false';
            $logLevel = $this->coreJson['settings']['logs']['level'] ?? 'DEBUG';
            $logsEmail = (isset($this->coreJson['settings']['logs']['emergencyLogsEmail']) && (string) $this->coreJson['settings']['logs']['emergencyLogsEmail'] === 'true') ? 'true' : 'false';
            $dev = (isset($this->coreJson['settings']['dev']) && (string) $this->coreJson['settings']['dev'] === 'true') ? 'true' : 'false';
        }

        $setup = 'false';
        $sso = 'false';
        $passwordPolicy = 'false';

        $this->coreJson['settings']['setup'] = $setup == 'true' ? true : false;
        $this->coreJson['settings']['debug'] = $debug == 'true' ? true : false;
        $this->coreJson['settings']['cache']['enabled'] = $cache == 'true' ? true : false;
        $this->coreJson['settings']['dev'] = $dev == 'true' ? true : false;
        $this->coreJson['settings']['databasetype'] = $this->postData['databasetype'] ?? ($this->coreJson['settings']['databasetype'] ?? 'hybrid');

        if ($this->coreJson['settings']['databasetype'] !== 'ff') {
            $dbname = (string) $this->postData['dbname'];
            $this->coreJson['settings']['dbs'][$dbname]['active'] = true;
            $this->coreJson['settings']['dbs'][$dbname]['host'] = $this->postData['host'] ?? 'localhost';
            $this->coreJson['settings']['dbs'][$dbname]['dbname'] = $dbname;
            $this->coreJson['settings']['dbs'][$dbname]['username'] = $this->postData['username'] ?? '';
            $password = isset($this->postData['password']) ? (string) $this->postData['password'] : '';
            $encryptedPassword = $this->getContainerService('crypt')?->encryptBase64($password, $this->createDbKey()) ?? $password;
            $this->coreJson['settings']['dbs'][$dbname]['password'] = $this->postData['password'] = $encryptedPassword;
            $this->coreJson['settings']['dbs'][$dbname]['port'] = $this->postData['port'] ?? 3306;
            $this->coreJson['settings']['dbs'][$dbname]['charset'] = $this->postData['charset'] ?? 'utf8mb4';
            $this->coreJson['settings']['dbs'][$dbname]['collation'] = $this->postData['collation'] ?? 'utf8mb4_unicode_ci';
        }

        $this->coreJson['settings']['logs']['level'] = $logLevel;
        $this->coreJson['settings']['security']['sso'] = $sso == 'true' ? true : false;
        $this->coreJson['settings']['security']['passwordWorkFactor'] = $pwf;
        $this->coreJson['settings']['security']['cookiesWorkFactor'] = $cwf;
        $this->coreJson['settings']['security']['passwordPolicy'] = $passwordPolicy == 'true' ? true : false;

        $dbType = $this->postData['databasetype'] ?? ($this->coreJson['settings']['databasetype'] ?? 'hybrid');
        $autoOffDebug = isset($this->coreJson['settings']['auto_off_debug']) ? (string) $this->coreJson['settings']['auto_off_debug'] : '86400';
        $ffDir = $this->coreJson['settings']['ffs']['sp']['databaseDir'] ?? 'sp/';

        $this->baseFileContent =
'<?php

declare(strict_types=1);

return [
    "setup"             => ' . $setup . ',
    "dev"               => ' . $dev . ',
    "debug"             => ' . $debug . ',
    "auto_off_debug"    => ' . $autoOffDebug . ',
    "databasetype"      => "' . $dbType . '",';

        if ($dbType === 'hybrid') {
            $this->baseFileContent .= '
    "db"                => [
        "host"          => "' . ($this->postData['host'] ?? 'localhost') . '",
        "port"          => "' . ($this->postData['port'] ?? '3306') . '",
        "dbname"        => "' . ($this->postData['dbname'] ?? 'sp') . '",
        "charset"       => "' . ($this->postData['charset'] ?? 'utf8mb4') . '",
        "collation"     => "' . ($this->postData['collation'] ?? 'utf8mb4_unicode_ci') . '",
        "username"      => "' . ($this->postData['username'] ?? 'root') . '",
        "password"      => "' . ($this->postData['password'] ?? '') . '"
    ],
    "ff"                => [
        "databaseDir"   => "' . $ffDir . '"
    ],';
        } elseif ($dbType === 'ff') {
            $this->baseFileContent .= '
    "ff"                => [
        "databaseDir"   => "' . $ffDir . '"
    ],';
        } elseif ($dbType === 'db') {
            $this->baseFileContent .= '
    "db"                => [
        "host"          => "' . ($this->postData['host'] ?? 'localhost') . '",
        "port"          => "' . ($this->postData['port'] ?? '3306') . '",
        "dbname"        => "' . ($this->postData['dbname'] ?? 'sp') . '",
        "charset"       => "' . ($this->postData['charset'] ?? 'utf8mb4') . '",
        "collation"     => "' . ($this->postData['collation'] ?? 'utf8mb4_unicode_ci') . '",
        "username"      => "' . ($this->postData['username'] ?? 'root') . '",
        "password"      => "' . ($this->postData['password'] ?? '') . '"
    ],';
        }

        $cacheTimeout = $this->coreJson['settings']['cache']['timeout'] ?? 60;
        $cacheService = $this->coreJson['settings']['cache']['service'] ?? 'streamCache';
        $emergencyEmailAddresses = $this->coreJson['settings']['logs']['emergencyLogsEmailAddresses'] ?? '';
        $wsProtocol = $this->coreJson['settings']['websocket']['protocol'] ?? 'tcp';
        $wsHost = $this->coreJson['settings']['websocket']['host'] ?? 'localhost';
        $wsPort = $this->coreJson['settings']['websocket']['port'] ?? 5555;
        $timeoutCookies = $this->coreJson['settings']['timeout']['cookies'] ?? 86400;
        $timeoutSessionIdle = $this->coreJson['settings']['timeout']['session_idle'] ?? 86400;
        $timeoutSessionAbs = $this->coreJson['settings']['timeout']['session_absolute'] ?? 2592000;
        $country = $this->postData['country'] ?? '';
        $timezone = $this->postData['timezone'] ?? 'UTC';

        $this->baseFileContent .= '
    "cache"             => [
        "enabled"       => ' . $cache . ',
        "timeout"       => ' . $cacheTimeout . ',
        "service"       => "' . $cacheService . '"
    ],
    "security"          => [
        "sso"                   => ' . $sso . ',
        "passwordWorkFactor"    => ' . $pwf . ',
        "cookiesWorkFactor"     => ' . $cwf . ',
        "passwordPolicy"        => ' . $passwordPolicy . '
    ],
    "logs"              => [
        "enabled"                       => ' . $logsEnabled . ',
        "exceptions"                    => ' . $logsExceptions . ',
        "level"                         => "' . $logLevel . '",
        "service"                       => "streamLogs",
        "emergencyLogsEmail"            => ' . $logsEmail . ',
        "emergencyLogsEmailAddresses"   => "' . $emergencyEmailAddresses . '"
    ],
    "websocket"         => [
        "protocol"      => "' . $wsProtocol . '",
        "host"          => "' . $wsHost . '",
        "port"          => ' . $wsPort . '
    ],
    "timeout"           => [
        "cookies"           => ' . $timeoutCookies . ',
        "session_idle"      => ' . $timeoutSessionIdle . ',
        "session_absolute"  => ' . $timeoutSessionAbs . '
    ],
    "locale"            => [
        "country_iso3"  => "' . $country . '",
        "timezone"      => "' . $timezone . '"
    ]
];';

        return $this->coreJson;
    }

    /**
     * Persists the compiled base configuration into /system/Configs/Base.php.
     *
     * @throws ErrorException|FilesystemException|UnableToWriteFile If file write fails.
     *
     * @return void
     */
    protected function writeBaseFile(): void
    {
        if (!$this->baseFileContent) {
            $this->writeBaseConfig();
        }

        $localContent = $this->getContainerService('localContent');
        if ($localContent && is_object($localContent) && method_exists($localContent, 'write')) {
            $localContent->write('/system/Configs/Base.php', (string) $this->baseFileContent);
        }
    }

    /**
     * Benchmarks and selects appropriate bcrypt work factor cost for server CPU.
     *
     * @return int Computed work factor.
     */
    protected function getWorkFactor(): int
    {
        $security = $this->getContainerService('security');

        for ($workFactor = 4; $workFactor <= 16; $workFactor++) {
            $timeStart = $this->microtimeFloat();

            if ($security && method_exists($security, 'hash')) {
                $security->hash((string) rand(), ['cost' => $workFactor]);
            }

            $timeEnd = $this->microtimeFloat();
            $time = $timeEnd - $timeStart;

            if ($time < 0.100) {
                return $workFactor;
            }
        }

        return 10;
    }

    /**
     * Returns current microtime as float.
     *
     * @return float
     */
    protected function microtimeFloat(): float
    {
        list($usec, $sec) = explode(' ', microtime());

        return (float) $usec + (float) $sec;
    }

    /**
     * Generates and writes database encryption key into system/.dbkeys.
     *
     * @return string Generated encryption key.
     *
     * @throws Filesystem|UnableToWriteFile If write fails
     */
    private function createDbKey(): string
    {
        $keys[$this->postData['dbname']] = $this->container['random']->base58(4);

        try {
            $this->container['localContent']->write('system/.dbkeys', $this->container['helper']->encode($keys), ['visibility' => 'private']);
        } catch (\Throwable | FilesystemException | UnableToWriteFile $exception) {
            throw $exception;
        }

        return $keys[$this->postData['dbname']];
    }

    /**
     * Resolves service from DI container whether array or object.
     *
     * @param string $name Service name.
     *
     * @return mixed Service instance or null.
     */
    protected function getContainerService(string $name): mixed
    {
        if (is_array($this->container) && isset($this->container[$name])) {
            return $this->container[$name];
        }

        if (is_object($this->container)) {
            if (method_exists($this->container, 'getShared')) {
                return $this->container->getShared($name);
            }
            if (method_exists($this->container, 'get')) {
                return $this->container->get($name);
            }
            if (isset($this->container->{$name})) {
                return $this->container->{$name};
            }
        }

        return null;
    }
}