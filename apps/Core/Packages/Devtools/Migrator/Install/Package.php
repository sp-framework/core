<?php

namespace Apps\Core\Packages\Devtools\Migrator\Install;

use Apps\Core\Packages\Devtools\Migrator\Install\Schema\Issues;
use Apps\Core\Packages\Devtools\Migrator\Model\DevtoolsMigrator;
use System\Base\BasePackage;

class Package extends BasePackage
{
    protected $dbConfig;

    public function install($redoDB = false)
    {
        $this->dbConfig = $this->core->getDb();

        $database =
            [
                'devtools_migrator_issues'                     => [
                        'schema'    => new Issues,
                        'model'     => new DevtoolsMigrator,
                ]
            ];

        try {
            if ($this->config->databasetype !== 'ff') {
                foreach ($database as $tableName => $tableClass) {
                    if ($redoDB) {
                        $this->db->dropTable($tableName);
                    }
                    if (method_exists($tableClass['schema'], 'columns')) {
                        $this->db->createTable($tableName, $this->dbConfig['dbname'], $tableClass['schema']->columns());
                    }
                    if (method_exists($tableClass['schema'], 'indexes')) {
                        $this->addIndex($tableName, $tableClass['schema']->indexes());
                    }
                }
            }
            if ($this->config->databasetype !== 'db') {
                foreach ($database as $tableName => $tableClass) {
                    if ($tableClass['model'] && $tableClass['model']->getSource()) {
                        $tableName = $tableClass['model']->getSource();
                    }

                    $config = $this->ff->generateConfig($tableName, $tableClass['schema'], $tableClass['model'], $this->db);
                    $schema = $this->ff->generateSchema($tableName, $tableClass['schema'], $tableClass['model']);

                    if ($redoDB) {
                        $this->ff->store($tableName, $config, $schema, $this->ff)->deleteStore();
                    }

                    $this->ff->store($tableName, $config, $schema, $this->ff);

                    if (method_exists($tableClass['schema'], 'indexes')) {
                        array_push($this->storesToIndex, $tableName);
                    }
                }
            }
        } catch (\throwable $e) {
            var_dump($e);die();
        }

        return true;
    }
}