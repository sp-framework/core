<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Carbon\Carbon;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IoHelper;
use System\Base\Providers\DatabaseServiceProvider\Ff\Query;
use System\Base\Providers\DatabaseServiceProvider\Ff\Store;

class Ff
{
    protected $ff;

    protected $db;

    protected $basepackages;

    protected $databaseDir;

    protected $config = [];

    protected $cacheConfig = [];

    protected $request;

    protected $store;

    protected $syncFile;

    protected $syncEnabled = true;

    public $mode;

    public function __construct($baseConfig, $request, $db = null, $basepackages = null)
    {
        $this->baseConfig = $baseConfig;

        $this->cacheConfig['auto_cache'] = $this->baseConfig->cache->enabled;

        $this->cacheConfig['cache_lifetime'] = $this->baseConfig->cache->timeout;

        $this->mode = $this->baseConfig->databaseType;

        $this->request = $request;

        if ($db && $basepackages) {
            $this->db = $db;

            $this->basepackages = $basepackages;

            $this->init();

            $this->loadSyncFile();
        }

    }

    public function init($resetSync = false, $syncEnabled = true)
    {
        if (isset($this->baseConfig->ff) && $this->baseConfig->ff->databaseDir) {
            $this->databaseDir = base_path($this->baseConfig->ff->databaseDir);
        } else {
            $this->databaseDir = base_path('.ff/');
        }

        $this->checkDatabasePath();

        if ($resetSync) {
            $this->resetSync();
        }

        $this->syncEnabled = $syncEnabled;

        return $this;
    }

    public function setSync(bool $set)
    {
        $this->syncEnabled = $set;
    }

    public function resetSync()
    {
        IoHelper::writeContentToFile($this->databaseDir . '_sync.sdb', '{}');
    }

    public function getDatabaseDir()
    {
        return $this->databaseDir;
    }

    public function store($file, $config = [], $schema = [])
    {
        $this->config = [];

        $this->config = array_merge($this->config, $this->cacheConfig);

        if (count($config) > 0) {
            $this->config = array_replace_recursive($this->config, $config);
        }

        $this->store = new Store($file, $this->databaseDir, $this, $this->config, $schema);

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

        if ($tableModel) {
            $config['model'] = get_class($tableModel);
        }

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

    public function addToSync($model, $id, $task = 'add', $schedule = true)
    {
        if (!$this->syncEnabled) {
            return false;
        }

        if (!$model) {
            return false;
        }

        if (is_string($model)) {
            $model = new $model;
        }

        $ffStore = $this->store($model->getSource());
        $ffStoreName = str_replace('/', '', $ffStore->getStoreName());
        $checkEntry = $ffStore->findById($id);

        if ($checkEntry) {
            if (isset($this->syncFile[$task][$ffStoreName])) {
                if (!isset($this->syncFile[$task][$ffStoreName]['model'])) {
                    $this->syncFile[$task][$ffStoreName]['model'] = get_class($model);
                }

                if (!isset($this->syncFile[$task][$ffStoreName]['ids'])) {
                    $this->syncFile[$task][$ffStoreName]['ids'] = $id;
                } else if (isset($this->syncFile[$task][$ffStoreName]['ids']) &&
                           is_array($this->syncFile[$task][$ffStoreName]['ids'])
                ) {
                    if (!in_array($id, $this->syncFile[$task][$ffStoreName]['ids'])) {
                        array_push($this->syncFile[$task][$ffStoreName]['ids'], $id);
                    }
                }
            } else {
                $this->syncFile[$task][$ffStoreName] = [];
                $this->syncFile[$task][$ffStoreName]['model'] = get_class($model);
                $this->syncFile[$task][$ffStoreName]['ids'] = [$id];
            }

            IoHelper::writeContentToFile($this->databaseDir . '_sync.sdb', json_encode($this->syncFile));

            if ($schedule) {
                return $this->addToSchedule();
            } else {
                return $this->sync();
            }
        }

        throw new \Exception('Add to Sync failed as data with the ID provided does not exits.');
    }

    protected function addToSchedule()
    {
        $task = $this->basepackages->workers->tasks->findByFunction('processdbsync');

        if ($task) {//We have to update it manually from here as updating via task will cause a loop
            $time = Carbon::now();

            $task['force_next_run'] = 1;
            $task['status'] = 1;
            $task['next_run'] = $time->addMinute()->startOfMinute()->format('Y-m-d H:i:s');

            $this->mode = 'ff';
            $taskStore = $this->store('basepackages_workers_tasks');

            $taskStore->update($task);
            $this->mode = 'hybrid';

            return true;
        }

        throw new \Exception('Task to run sync does not exits. Please re-add task.');
    }

    public function getSyncFile(): array
    {
        return $this->loadSyncFile();
    }

    public function sync(): array
    {
        $this->loadSyncFile();

        if (!$this->syncFile) {
            array_push($this->syncFile['errors'], 'Error processing sync file.');
        }

        try {
            foreach ($this->syncFile as $task => &$tasks) {
                if (count($tasks) === 0 || $task === 'errors') {
                    continue;
                }

                foreach ($tasks as $store => &$toSync) {
                    if (!is_array($toSync) || (is_array($toSync) && count($toSync) === 0)) {
                        continue;
                    }

                    if (!isset($toSync['model'])) {
                        array_push($this->syncFile['errors'], 'Model missing in the sync file.');
                    }

                    if (isset($toSync['ids']) && count($toSync['ids']) === 0) {
                        array_push($this->syncFile['errors'], 'Ids missing in the sync file for store: ' . $store);
                    }

                    $ffStore = $this->store($store);
                    $storeSchema = $ffStore->getStoreSchema();

                    foreach ($toSync['ids'] as $idKey => &$id) {
                        $model = new $toSync['model'];

                        if ($task !== 'remove') {
                            $data = $ffStore->findById($id);

                            if (!$data) {
                                array_push($this->syncFile['warnings'], 'Data for ID: ' . $id .' missing for store: ' . $store . '. Removing ID from sync.');

                                unset($toSync['ids'][$idKey]);

                                continue;
                            }

                            $data = $this->normalizeData($data, $storeSchema);

                            $model->assign($data);
                        }

                        if ($task === 'add') {
                            $taskPerformed = $model->create();

                            if (!$taskPerformed && strpos($model->getMessages()[0]->getMessage(), 'already exists')) {
                                $taskPerformed = $model->update();
                            }
                        } else if ($task === 'update') {
                            $taskPerformed = $model->update();

                            if (!$taskPerformed && strpos($model->getMessages()[0]->getMessage(), 'does not exist')) {
                                $taskPerformed = $model->create();
                            }
                        } else if ($task === 'remove') {
                            $taskPerformed = $model->delete();
                        }

                        if ($taskPerformed) {
                            unset($toSync['ids'][$idKey]);

                            if (count($toSync['ids']) === 0) {
                                unset($tasks[$store]);
                            }

                            IoHelper::writeContentToFile($this->databaseDir . '_sync.sdb', json_encode($this->syncFile));
                        } else {
                            $transactionErrors = [];

                            foreach ($model->getMessages() as $err) {
                                if (strpos($err->getMessage(), 'already exists')) {
                                    array_push($this->syncFile['warnings'], 'Trying to ' . $task . ' db entry for ID ' . $id . ' but it already exists. Updating instead.');
                                } else if (strpos($err->getMessage(), 'does not exist')) {
                                    array_push($this->syncFile['warnings'], 'Trying to ' . $task . ' db entry for ID ' . $id . ' but it does not exists. Adding instead.');
                                } else {
                                    array_push($transactionErrors, str_replace("'", '', $err->getMessage()));
                                }
                            }

                            array_push($this->syncFile['errors'], "Could not " . $task . " data in db for store " . get_class($model) . ", for ID " . $id . ". Reasons: <br>" .
                                join(',', $transactionErrors)
                            );
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Integrity')) {
                array_push($this->syncFile['errors'], str_replace("'", '', $e->getMessage()) . '. Database Sync will happen until admin resync the whole table. Resync can be performed via settings/core/database section.');
            } else {
                array_push($this->syncFile['errors'], str_replace("'", '', $e->getMessage()));
            }
        }

        return $this->syncFile;
    }

    protected function loadSyncFile()
    {
        if (!file_exists($this->databaseDir . '_sync.sdb')) {
            IoHelper::writeContentToFile($this->databaseDir . '_sync.sdb', '{}');
        }

        $this->syncFile = json_decode(IoHelper::getFileContent($this->databaseDir . '_sync.sdb'), true);

        if (!array_key_exists('add', $this->syncFile)) {
            $this->syncFile['add'] = [];
        }
        if (!array_key_exists('update', $this->syncFile)) {
            $this->syncFile['update'] = [];
        }
        if (!array_key_exists('remove', $this->syncFile)) {
            $this->syncFile['remove'] = [];
        }

        $this->syncFile['errors'] = [];

        $this->syncFile['warnings'] = [];

        return $this->syncFile;
    }

    protected function normalizeData(array $data, $schema): array
    {
        if (is_string($schema)) {
            $schema = json_decode($schema, true);
        }

        if (isset($schema['properties']) && count($schema['properties']) > 0) {
            foreach ($schema['properties'] as $propertyKey => $property) {
                if (array_key_exists('format', $property)) {
                    if ($property['format'] === 'json') {
                        if (isset($data[$propertyKey]) && is_array($data[$propertyKey])) {
                            $data[$propertyKey] = json_encode($data[$propertyKey]);
                        }
                    }
                }

                if (array_key_exists('type', $property) && is_array($property['type'])) {
                    foreach ($property['type'] as $type) {
                        if ($type === 'boolean') {
                            $data[$propertyKey] = $data[$propertyKey] === true ? '1' : '0';
                        }
                    }
                } else if (array_key_exists('type', $property) && is_string($property['type'])) {
                    if ($property['type'] === 'boolean') {
                        $data[$propertyKey] = $data[$propertyKey] === true ? '1' : '0';
                    }
                }
            }
        }

        return $data;
    }
}