<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup\Write
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Packages\Setup\Write;

use Codeception\Test\Unit;
use System\Base\Installer\Packages\Setup\Write\Configs;

/**
 * Unit test suite for Framework Configuration Writer.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup\Write
 */
class ConfigsTest extends Unit
{
    protected array $writtenFiles = [];

    protected function _before(): void
    {
        $this->writtenFiles = [];
    }

    /**
     * Creates mock DI container for Configs testing.
     *
     * @return array<string, object>
     */
    protected function createMockContainer(): array
    {
        $writtenFiles = &$this->writtenFiles;

        $mockLocalContent = new class ($writtenFiles) {
            public $written;
            public function __construct(&$written) { $this->written = &$written; }
            public function write(string $path, string $content, array $config = []): void
            {
                $this->written[$path] = $content;
            }
        };

        $mockSecurity = new class {
            public function hash(string $password, array $options = []): string
            {
                return password_hash($password, PASSWORD_BCRYPT, $options);
            }
        };

        $mockRandom = new class {
            public function base58(int $len): string
            {
                return 'mockDbKey58';
            }
        };

        $mockCrypt = new class {
            public function encryptBase64(string $text, string $key): string
            {
                return base64_encode($text . ':' . $key);
            }
        };

        $mockHelper = new class {
            public function encode(mixed $data): string
            {
                return (string) json_encode($data);
            }
        };

        return [
            'localContent' => $mockLocalContent,
            'security'     => $mockSecurity,
            'random'       => $mockRandom,
            'crypt'        => $mockCrypt,
            'helper'       => $mockHelper,
        ];
    }

    /**
     * Tests write method in hybrid mode.
     *
     * @return void
     */
    public function testWriteHybridMode(): void
    {
        $container = $this->createMockContainer();
        $postData = [
            'databasetype'        => 'hybrid',
            'host'                => '127.0.0.1',
            'port'                => '3306',
            'dbname'              => 'testdb',
            'charset'             => 'utf8mb4',
            'collation'           => 'utf8mb4_unicode_ci',
            'username'            => 'testuser',
            'password'            => 'secret123',
            'dev'                 => 'true',
            'country'             => 'USA',
            'timezone'            => 'America/New_York',
            'auto-encrypt-level'  => 'false',
            'pwf'                 => '10',
            'cwf'                 => '8',
        ];

        $coreJson = [
            'settings' => [
                'debug' => 'true',
                'cache' => ['enabled' => 'false', 'timeout' => 60, 'service' => 'streamCache'],
                'logs'  => ['enabled' => 'true', 'exceptions' => 'true', 'level' => 'DEBUG', 'emergencyLogsEmail' => 'false', 'emergencyLogsEmailAddresses' => ''],
                'dev'   => 'true',
                'auto_off_debug' => '86400',
                'ffs'   => ['sp' => ['databaseDir' => 'sp/']],
                'websocket' => ['protocol' => 'tcp', 'host' => 'localhost', 'port' => 5555],
                'timeout' => ['cookies' => 86400, 'session_idle' => 86400, 'session_absolute' => 2592000],
            ]
        ];

        $writer = new Configs($container, $postData, $coreJson);
        $updatedJson = $writer->write(true);

        $this->assertIsArray($updatedJson);
        $this->assertArrayHasKey('/system/Configs/Base.php', $this->writtenFiles);
        $this->assertArrayHasKey('system/.dbkeys', $this->writtenFiles);

        $baseContent = $this->writtenFiles['/system/Configs/Base.php'];
        $this->assertStringContainsString('"databasetype"      => "hybrid"', $baseContent);
        $this->assertStringContainsString('"dbname"        => "testdb"', $baseContent);
        $this->assertStringContainsString('"country_iso3"  => "USA"', $baseContent);
    }

    /**
     * Tests write method in flatfile (ff) mode.
     *
     * @return void
     */
    public function testWriteFlatfileMode(): void
    {
        $container = $this->createMockContainer();
        $postData = [
            'databasetype'       => 'ff',
            'dev'                => 'false',
            'country'            => 'GBR',
            'timezone'           => 'Europe/London',
            'auto-encrypt-level' => 'false',
            'pwf'                => '12',
            'cwf'                => '10',
        ];

        $coreJson = [
            'settings' => [
                'databasetype' => 'ff',
                'ffs'          => ['sp' => ['databaseDir' => 'sp/']],
                'security'     => ['passwordWorkFactor' => 12, 'cookiesWorkFactor' => 10],
            ]
        ];

        $writer = new Configs($container, $postData, $coreJson);
        $writer->write(true);

        $this->assertArrayHasKey('/system/Configs/Base.php', $this->writtenFiles);
        $baseContent = $this->writtenFiles['/system/Configs/Base.php'];
        $this->assertStringContainsString('"databasetype"      => "ff"', $baseContent);
        $this->assertStringNotContainsString('"db"                => [', $baseContent);
    }

    /**
     * Tests revert method resets Base.php into uninstalled setup mode.
     *
     * @return void
     */
    public function testRevertResetsToSetup(): void
    {
        $container = $this->createMockContainer();
        $writer = new Configs($container, []);
        $writer->revert();

        $this->assertArrayHasKey('/system/Configs/Base.php', $this->writtenFiles);
        $baseContent = $this->writtenFiles['/system/Configs/Base.php'];
        $this->assertStringContainsString('"setup"             => true', $baseContent);
    }
}
