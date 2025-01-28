<?php

namespace System\Base\Providers\CoreServiceProvider\Install;

use System\Base\BasePackage;
use System\Base\Installer\Packages\Setup\Schema;

class Package extends BasePackage
{
    protected $installer;

    public function install($installer)
    {
        $this->installer = $installer;

        $databases = (new Schema)->getSchema();

        //Do version specific update for any future version upgrades.
        // if ($this->core->core['version'] === 'x.x.x') {
            //Do this
        // }
        // trace(varsToDump : [$this->core->core], object: true);

        if (isset($this->config['databasetype']) && $this->config['databasetype'] !== 'ff') {
            $dbTablesList = $this->describe();

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

                // $dbTableIndexes = $this->describe($tableName, true);
                // var_dump($dbTableIndexes);
                // if (method_exists($tableClass['schema'], 'indexes')) {
                //     $this->addIndex($tableName, $tableClass['schema']->indexes());
                // }
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
}