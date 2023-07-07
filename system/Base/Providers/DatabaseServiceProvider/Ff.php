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

        $this->config = array_merge($this->config, $this->cacheConfig);

        if (count($config) > 0) {
            $this->config = array_replace_recursive($this->config, $config);
        }

        $this->store = new Store($file, $this->databaseDir, $this->config, $schema);

        return $this->store;
    }

    public function generateSchema($tableName, $tableClass, $tableModel)
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
                $schema['properties'][$column->getName()]['relation'] = $column->getComment();
            }
        }

        if ($tableModel) {
            $relations = $tableModel->getModelRelations();

            if ($relations && is_array($relations) && count($relations) > 0) {
                foreach ($relations as $relationKey => $relation) {
                    $intermediate = false;

                    if (isset($relation['relationObj'])) {
                        if ($relation['relationObj']->getReferencedModel() &&
                            is_string($relation['relationObj']->getReferencedModel())
                        ) {
                            if (isset($relation['relationObj']->getOptions()['alias'])) {
                                $references[$relationKey]['alias'] = $relation['relationObj']->getOptions()['alias'];
                            }

                            switch ($relation['relationObj']->getType()) {
                                case '1':
                                    $references[$relationKey]['type'] = 'hasOne';
                                    break;
                                case '2':
                                    $references[$relationKey]['type'] = 'hasMany';
                                    break;
                                case '3':
                                    $intermediate = true;
                                    $references[$relationKey]['type'] = 'hasOneThrough';
                                    break;
                                case '4':
                                    $intermediate = true;
                                    $references[$relationKey]['type'] = 'hasManyThrough';
                                    break;
                            }

                            if (!$intermediate) {
                                $model = $relation['relationObj']->getReferencedModel();
                                $references[$relationKey]['table'] = (new $model)->getSource();
                                $references[$relationKey]['model'] = $model;
                                $fields = $relation['relationObj']->getFields();
                                $referencedFields = $relation['relationObj']->getReferencedFields();

                                if (is_array($fields) && is_array($referencedFields)) {
                                    $references[$relationKey]['fields'] = array_merge_recursive($fields, $referencedFields);
                                } else if (is_string($fields) && is_string($referencedFields)) {
                                    $references[$relationKey]['fields'] = [$fields, $referencedFields];
                                }
                            } else {
                                $model = $relation['relationObj']->getIntermediateModel();
                                $references[$relationKey][0]['table'] = (new $model)->getSource();
                                $references[$relationKey][0]['model'] = $model;
                                $fields = $relation['relationObj']->getFields();
                                $intermediateFields = $relation['relationObj']->getIntermediateFields();
                                if (is_array($fields) && is_array($intermediateFields)) {
                                    $references[$relationKey][0]['fields'] = array_merge_recursive($fields, $intermediateFields);
                                } else if (is_string($fields) && is_string($intermediateFields)) {
                                    $references[$relationKey][0]['fields'] = [$fields, $intermediateFields];
                                }

                                $model = $relation['relationObj']->getReferencedModel();
                                $references[$relationKey][1]['table'] = (new $model)->getSource();
                                $references[$relationKey][1]['model'] = $model;
                                $fields = $relation['relationObj']->getIntermediateReferencedFields();
                                $referencedFields = $relation['relationObj']->getReferencedFields();
                                if (is_array($fields) && is_array($referencedFields)) {
                                    $references[$relationKey][1]['fields'] = array_merge_recursive($fields, $referencedFields);
                                } else if (is_string($fields) && is_string($referencedFields)) {
                                    $references[$relationKey][1]['fields'] = [$fields, $referencedFields];
                                }
                            }

                            if (isset($relation['relationObj']->getOptions()['params'])) {
                                $references[$relationKey]['params'] = 'hasParams';
                            }
                        }
                    }
                }
            }

            if (isset($references) && is_array($references)) {
                foreach ($references as $key => $reference) {
                    if (isset($reference['alias'])) {
                        $schema['properties'][$reference['alias']] = [];
                        $schema['properties'][$reference['alias']]['type'] = ['null','array'];

                        if (isset($reference[0]) && isset($reference[1])) {
                            if (isset($reference[0]['fields']) && count($reference[0]['fields']) > 0) {
                                $reference[0]['fields'] = join(':', $reference[0]['fields']);
                            }
                            if (isset($reference[1]['fields']) && count($reference[1]['fields']) > 0) {
                                $reference[1]['fields'] = join(':', $reference[1]['fields']);
                            }

                            $reference[0] = join('+', $reference[0]);
                            $reference[1] = join('+', $reference[1]);
                            $schema['properties'][$reference['alias']]['relation'] = join('|', $reference);
                        } else {
                            if (isset($reference['fields']) && count($reference['fields']) > 0) {
                                $reference['fields'] = join(':', $reference['fields']);
                            }

                            $schema['properties'][$reference['alias']]['relation'] = join('|', $reference);
                        }
                    }
                }
            }
        }

        return $schema;
    }

    public function generateConfig($tableName, $tableClass, $tableModel)
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