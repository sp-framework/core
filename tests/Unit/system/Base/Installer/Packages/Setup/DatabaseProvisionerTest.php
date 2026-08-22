<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Packages\Setup;

use Codeception\Test\Unit;
use System\Base\Installer\Packages\Setup\DatabaseProvisioner;
use System\Base\Installer\Packages\Setup\PasswordChecker;

/**
 * Unit test suite for DatabaseProvisioner.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class DatabaseProvisionerTest extends Unit
{
    /**
     * Tests checkDbEmpty, createNewDb, createNewUser, and addIndex.
     *
     * @return void
     */
    public function testDatabaseProvisionerOperations(): void
    {
        $executedSql = [];
        $droppedTables = [];

        $mockDb = new class ($executedSql, $droppedTables) {
            public $sql;
            public $dropped;
            public function __construct(&$s, &$d) { $this->sql = &$s; $this->dropped = &$d; }
            public function listTables(string $name): array { return ['tbl_a', 'tbl_b']; }
            public function dropTable(string $tbl): void { $this->dropped[] = $tbl; }
            public function query(string $q, array $d = []): object
            {
                $this->sql[] = $q;
                return new class {
                    public function numRows(): int { return 0; }
                };
            }
        };

        $postData = [
            'dbname'          => 'test_db',
            'username'        => 'test_user',
            'password'        => 'SecurePass123!',
            'create-username' => 'root',
            'create-password' => 'rootpass',
            'drop'            => 'true',
            'dev'             => 'true',
        ];

        $provisioner = new DatabaseProvisioner($mockDb, $postData, new PasswordChecker());

        $this->assertTrue($provisioner->checkDbEmpty());
        $this->assertCount(2, $droppedTables);

        $this->assertTrue($provisioner->createNewDb());
        $this->assertTrue($provisioner->createNewUser());

        $mockIndex = new class {
            public function getColumns(): array { return ['email', 'domain']; }
            public function getType(): string { return 'INDEX'; }
            public function getName(): string { return 'idx_email_domain'; }
        };

        $provisioner->addIndex('users', [$mockIndex]);
        $this->assertNotEmpty($executedSql);
    }
}
