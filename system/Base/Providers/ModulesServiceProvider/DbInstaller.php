<?php

namespace System\Base\Providers\ModulesServiceProvider;

use System\Base\BasePackage;

class DbInstaller extends BasePackage
{
    public function installDb($databases)
    {
        if (isset($this->config['databasetype']) && $this->config['databasetype'] !== 'ff') {
            foreach ($databases as $tableName => $tableClass) {
                if ($tableClass['model'] && $tableClass['model']->getSource()) {
                    $tableName = $tableClass['model']->getSource();
                }

                if (method_exists($tableClass['schema'], 'columns')) {
                    if ($this->tableExists($tableName)) {
                        $dbTableColumns = $this->describe($tableName);

                        $dbTableColumnsList = [];

                        if ($dbTableColumns && is_array($dbTableColumns) && count($dbTableColumns) > 0) {
                            foreach ($dbTableColumns as $tableColumn) {
                                $dbTableColumnsList[$tableColumn->getName()] = $tableColumn;
                            }
                        }

                        if (isset($tableClass['schema']->columns()['columns']) && count($tableClass['schema']->columns()['columns']) > 0) {
                            foreach ($tableClass['schema']->columns()['columns'] as $schemaKey => $schemaColumn) {
                                if (isset($dbTableColumnsList[$schemaColumn->getName()])) {
                                    $this->alterTable('modify', $tableName, [$schemaColumn]);
                                } else {
                                    $this->alterTable('add', $tableName, [$schemaColumn]);
                                }
                                unset($dbTableColumnsList[$schemaColumn->getName()]);
                            }

                            if (count($dbTableColumnsList) > 0) {
                                foreach ($dbTableColumnsList as $dbTableColumn) {
                                    $this->alterTable('drop', $tableName, $dbTableColumn->getName());
                                }
                            }
                        }
                    } else {
                        $this->db->createTable($tableName, $this->config['db']['dbname'], $tableClass['schema']->columns());
                    }
                }

                $indexList = [];
                if (isset($tableClass['schema']->columns()['indexes'])) {
                    $indexList = array_merge($indexList, $tableClass['schema']->columns()['indexes']);
                }

                if (method_exists($tableClass['schema'], 'indexes')) {
                    $indexList = array_merge($indexList, $tableClass['schema']->indexes());
                }

                if (count($indexList) > 0) {
                    $dbTableIndexes = $this->describe($tableName, true);

                    //New Indexes
                    foreach ($indexList as $indexListKey => $index) {
                        $indexList[$index->getName()] = $index;

                        if (!isset($dbTableIndexes[$index->getName()])) {
                            try {
                                $this->addIndex($tableName, [$index]);
                            } catch (\throwable $e) {
                                throw $e;
                            }
                        } else {
                            $dbIndexColumns = $dbTableIndexes[$index->getName()]->getColumns();
                            $newIndexColumns = $index->getColumns();

                            if (count(array_diff($newIndexColumns, $dbIndexColumns)) > 0 ||
                                count(array_diff($dbIndexColumns, $newIndexColumns)) > 0
                            ) {
                                try {
                                    $this->dropIndex($tableName, $index->getName());
                                    $this->addIndex($tableName, [$index]);
                                } catch (\throwable $e) {
                                    throw $e;
                                }
                            }
                        }

                        unset($indexList[$indexListKey]);
                    }

                    $dbTableIndexes = $this->describe($tableName, true);

                    //Drop any indexes that are removed
                    foreach ($dbTableIndexes as $dbTableIndexKey => $dbTableIndex) {
                        if (strtolower($dbTableIndexKey) === 'primary') {
                            continue;
                        }

                        if (!isset($indexList[$dbTableIndexKey])) {
                            try {
                                $this->dropIndex($tableName, $dbTableIndexKey);
                            } catch (\throwable $e) {
                                throw $e;
                            }
                        }
                    }
                }
            }
        }

        if (isset($this->config['databasetype']) && $this->config['databasetype'] !== 'db') {
            $storesToIndex = [];

            foreach ($databases as $tableName => $tableClass) {
                if ($tableClass['model'] && $tableClass['model']->getSource()) {
                    $tableName = $tableClass['model']->getSource();
                }

                $config = $this->ff->generateConfig($tableName, $tableClass['schema'], $tableClass['model']);
                $schema = $this->ff->generateSchema($tableName, $tableClass['schema'], $tableClass['model']);

                $this->ff->store($tableName, $config, $schema, $this->ff);

                if (method_exists($tableClass['schema'], 'indexes')) {
                    array_push($storesToIndex, $tableName);
                }
            }

            if (count($storesToIndex) > 0) {
                foreach ($storesToIndex as $storeToIndex) {
                    ($this->ff->store($storeToIndex))->reIndexStore();
                }
            }
        }

        return true;
    }

    public function truncate(array $databases)
    {
        $this->uninstallDb($databases);

        $this->installDb($databases);

        return true;
    }

    public function uninstallDb(array $databases)
    {
        if (isset($this->config['databasetype']) && $this->config['databasetype'] !== 'ff') {
            foreach ($databases as $tableName => $tableClass) {
                if ($tableClass['model'] && $tableClass['model']->getSource()) {
                    $tableName = $tableClass['model']->getSource();

                    $this->dropTable($tableName);
                }
            }

        }

        if (isset($this->config['databasetype']) && $this->config['databasetype'] !== 'db') {
            foreach ($databases as $tableName => $tableClass) {
                if ($tableClass['model'] && $tableClass['model']->getSource()) {
                    $tableName = $tableClass['model']->getSource();

                    $this->ff->store($tableName)->deleteStore();
                }
            }
        }

        return true;
    }
}