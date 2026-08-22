<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup;

use Exception;
use PDOException;

/**
 * Manages database provisioning, schema creation, database user provisioning, and raw SQL queries.
 */
class DatabaseProvisioner
{
    /**
     * Database connection adapter.
     *
     * @var mixed
     */
    protected mixed $db;

    /**
     * Setup post payload.
     *
     * @var array<string, mixed>
     */
    protected array $postData;

    /**
     * Password checker instance.
     *
     * @var PasswordChecker|null
     */
    protected ?PasswordChecker $passwordChecker = null;

    /**
     * DatabaseProvisioner constructor.
     *
     * @param mixed                $db                  Database connection adapter.
     * @param array<string, mixed> $postData            Setup post payload.
     * @param PasswordChecker|null $passwordChecker     Password strength checker.
     */
    public function __construct(mixed $db, array $postData = [], ?PasswordChecker $passwordChecker = null)
    {
        $this->db = $db;
        $this->postData = $postData;
        $this->passwordChecker = $passwordChecker;
    }

    /**
     * Checks if database is empty or drops tables if requested.
     *
     * @return bool True if empty or dropped, false if non-empty and drop=false.
     */
    public function checkDbEmpty(): bool
    {
        if (!$this->db) {
            return true;
        }

        $dbname = (string) ($this->postData['dbname'] ?? '');
        $allTables = method_exists($this->db, 'listTables') ? $this->db->listTables($dbname) : [];

        if (is_array($allTables) && count($allTables) > 0) {
            if (isset($this->postData['drop']) && (string) $this->postData['drop'] === 'false') {
                return false;
            }

            foreach ($allTables as $tableValue) {
                if (method_exists($this->db, 'dropTable')) {
                    $this->db->dropTable($tableValue);
                }
            }

            return true;
        }

        return true;
    }

    /**
     * Creates new database schema if it does not exist.
     *
     * @return bool True on success.
     */
    public function createNewDb(): bool
    {
        $dbname = $this->postData['dbname'] ?? 'sp';
        $charset = $this->postData['charset'] ?? 'utf8mb4';
        $collation = $this->postData['collation'] ?? 'utf8mb4_unicode_ci';

        $this->executeSQL(
            'CREATE DATABASE IF NOT EXISTS ' . $dbname . ' CHARACTER SET ' . $charset . ' COLLATE ' . $collation
        );

        return true;
    }

    /**
     * Provisions new MySQL user and grants privileges.
     *
     * @throws Exception If user creation fails or password is weak.
     *
     * @return bool True on success.
     */
    public function createNewUser(): bool
    {
        $username = $this->postData['username'] ?? '';
        $dbname = $this->postData['dbname'] ?? 'sp';
        $password = $this->postData['password'] ?? '';
        $dev = ($this->postData['dev'] ?? 'true') === 'true';

        $checkUser = $this->executeSQL('SELECT * FROM `user` WHERE `User` LIKE ?', [$username]);

        $numRows = (is_object($checkUser) && method_exists($checkUser, 'numRows')) ? $checkUser->numRows() : 0;

        if ($numRows === 0) {
            if (!isset($this->postData['create-username'], $this->postData['create-password'])) {
                throw new Exception('User ' . $username . ' does not exist. Please enable create new user/database.');
            }

            if (!$dev) {
                $passStrength = $this->passwordChecker ? $this->passwordChecker->checkPwStrength($password) : 3;
                if ($passStrength !== false && $passStrength <= 2) {
                    throw new Exception('DB Password strength is weak!');
                }
            }

            $this->executeSQL("CREATE USER ?@'%' IDENTIFIED WITH caching_sha2_password BY ?;", [$username, $password]);
        }

        $this->executeSQL("GRANT ALL PRIVILEGES ON " . $dbname . ".* TO ?@'%' WITH GRANT OPTION;", [$username]);

        return true;
    }

    /**
     * Executes SQL statement against connected database.
     *
     * @param string            $sql  SQL query string.
     * @param array<int, mixed> $data Prepared statement parameters.
     *
     * @throws Exception If SQL execution fails.
     *
     * @return mixed Query result.
     */
    public function executeSQL(string $sql, array $data = []): mixed
    {
        try {
            return $this->db?->query($sql, $data);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Adds indexes to an SQL database table.
     *
     * @param string             $table Table name.
     * @param array<int, object> $index Array of Index objects.
     *
     * @return void
     */
    public function addIndex(string $table, array $index): void
    {
        foreach ($index as $idx) {
            if (!method_exists($idx, 'getColumns')) {
                continue;
            }

            $columnsArr = $idx->getColumns();

            if (count($columnsArr) > 1) {
                $columns = '';
                $lastKey = is_array($columnsArr) ? array_key_last($columnsArr) : null;

                foreach ($columnsArr as $columnsArrKey => $column) {
                    $columns .= '`' . $column . '`';
                    if ($columnsArrKey !== $lastKey) {
                        $columns .= ',';
                    }
                }
            } else {
                $columns = '`' . ($columnsArr[0] ?? '') . '`';
            }

            $idxType = method_exists($idx, 'getType') ? strtoupper($idx->getType()) : 'INDEX';
            $idxName = method_exists($idx, 'getName') ? $idx->getName() : 'idx';

            $this->executeSQL('ALTER TABLE `' . $table . '` ADD ' . $idxType . ' `' . $idxName . '` (' . $columns . ')');
        }
    }
}
