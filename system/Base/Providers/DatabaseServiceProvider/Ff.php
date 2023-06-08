<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use System\Base\Providers\DatabaseServiceProvider\Ff\Query;
use System\Base\Providers\DatabaseServiceProvider\Ff\Store;

class Ff
{
    protected $ff;

    protected $databaseDir;

    protected $config = [];

    protected $cacheConfig = [];

    protected $request;

    protected $store;

    public function __construct($cacheConfig, $request)
    {
        $this->cacheConfig['auto_cache'] = $cacheConfig->enabled;

        $this->cacheConfig['cache_lifetime'] = $cacheConfig->timeout;

        $this->request = $request;
    }

    public function init()
    {
        $this->databaseDir = base_path('.ff/');

        $this->checkDatabasePath();

        return $this;
    }

    public function store($file, $config = [], $schema = [])
    {
        $this->config = [];

        if (count($config) > 0) {
            $this->config = array_merge($this->config, $this->cacheConfig);

            $this->config = array_replace_recursive($this->config, $config);
        }

        $this->store = new Store($file, $this->databaseDir, $this->config, $schema);

        return $this->store;
    }

    public function generateSchema($tableName, $tableClass)
    {
        $schema = [];

        if (!method_exists($tableClass, 'columns')) {
            return $schema;
        }

        if (!isset($tableClass->columns()['columns'])) {
            return $schema;
        }

        if (count($tableClass->columns()['columns']) === 0) {
            return $schema;
        }

        $contants = [];
        $contants['string'] = [5,2,15,16,10,12,11,13,25,6,23,24,20,17,1,4,19,18];
        $contants['number'] = [3,9,7];
        $contants['integer'] = [26,22,21,0,14];

        $schema['$schema'] = 'https://json-schema.org/draft/2020-12/schema';
        $schema['$id'] = 'https://' . $this->request->getHttpHost() . '/schemas/' . strtolower($tableName) . '.json';
        $schema['type'] = 'object';
        $schema['properties'] = [];
        $schema['required'] = [];

        foreach ($tableClass->columns()['columns'] as $column) {
            $schema['properties'][$column->getName()] = [];

            if ($column->isNotNull() === true) {
                array_push($schema['required'], $column->getName());
            } if ($column->isNotNull() === false) {
                $schema['properties'][$column->getName()]['default'] = null;
                $schema['properties'][$column->getName()]['type'] = ['null'];
            }

            if (in_array($column->getType(), $contants['string'])) {
                $type = 'string';

                if ($column->getSize() > 0) {
                    if ($column->isNotNull() === true) {
                        $schema['properties'][$column->getName()]['minLength'] = 1;
                    }

                    $schema['properties'][$column->getName()]['maxLength'] = $column->getSize();
                }

                if ($column->getType() === 1) {
                    $schema['properties'][$column->getName()]['format'] = 'date';
                } else if ($column->getType() === 20) {
                    $schema['properties'][$column->getName()]['format'] = 'time';
                } else if ($column->getType() === 4 || $column->getType() === 17) {//if format is date-time and is required add timestamp
                    $schema['properties'][$column->getName()]['format'] = 'date-time';
                    if ($column->getDefault() &&
                        $column->getDefault() === 'CURRENT_TIMESTAMP'
                    ) {
                        if (!in_array($column->getName(), $schema['required'])) {
                            array_push($schema['required'], $column->getName());
                        }
                    }
                } else if ($column->getType() === 15 || $column->getType() === 16) {//json format
                    $schema['properties'][$column->getName()]['format'] = 'json';
                }
            } else if (in_array($column->getType(), $contants['number'])) {
                $type = 'number';
            } else if (in_array($column->getType(), $contants['integer'])) {
                $type = 'integer';
            } else if ($column->getType() === 8) {//Boolean
                $type = 'boolean';
            }

            if (isset($schema['properties'][$column->getName()]['type']) &&
                is_array($schema['properties'][$column->getName()]['type'])
            ) {
                array_push($schema['properties'][$column->getName()]['type'], $type);
            } else {
                $schema['properties'][$column->getName()]['type'] = $type;
            }

            if ($column->getComment()) {
                $schema['properties'][$column->getName()]['description'] = $column->getComment();
            }
        }

        return $schema;
    }

    public function generateConfig($tableName, $tableClass)
    {
        $config = [];

        if (!method_exists($tableClass, 'columns')) {
            return $config;
        }

        if (!isset($tableClass->columns()['indexes']) && !method_exists($tableClass, 'indexes')) {
            return $config;
        }

        if ((isset($tableClass->columns()['indexes']) && count($tableClass->columns()['indexes']) === 0) ||
            (method_exists($tableClass, 'indexes') && count($tableClass->indexes()) === 0)
        ) {
            return $config;
        }

        if (isset($tableClass->columns()['indexes'])) {
            foreach ($tableClass->columns()['indexes'] as $index) {
                if ($index->getType() === 'UNIQUE' && $index->getColumns() && count($index->getColumns()) > 0) {
                    $config['uniqueFields'] = $index->getColumns();
                }
            }
        }

        if (method_exists($tableClass, 'indexes')) {
            foreach ($tableClass->indexes() as $index) {
                if ($index->getType() === 'UNIQUE' && $index->getColumns() && count($index->getColumns()) > 0) {
                    if (isset($config['uniqueFields']) && count($config['uniqueFields']) > 0) {
                        $config['uniqueFields'] = array_merge($config['uniqueFields'], $index->getColumns());
                    } else {
                        $config['uniqueFields'] = $index->getColumns();
                    }
                }

                if ($index->getType() === 'INDEX' && $index->getColumns() && count($index->getColumns()) > 0) {
                    if (isset($config['indexes']) && count($config['indexes']) > 0) {
                        $config['indexes'] = array_merge($config['indexes'], $index->getColumns());
                    } else {
                        $config['indexes'] = $index->getColumns();
                    }
                }
            }
        }

        if (!isset($tableClass->columns()['columns'])) {
            return $config;
        }

        if (is_array($tableClass->columns()['columns']) && count($tableClass->columns()['columns']) === 0) {
            return $config;
        }

        return $config;
    }

    protected function checkDatabasePath()
    {
        if (!is_dir($this->databaseDir)) {
            if (!mkdir($this->databaseDir, 0777, true)) {
                return false;
            }
        }

        return true;
    }
}