<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Csv\Reader;
use League\Csv\Writer;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesImportExport;

class ImportExport extends BasePackage
{
    protected $modelToUse = BasepackagesImportExport::class;

    protected $packageName = 'importexport';

    public $importexport;

    public function addImportexport(array $data)
    {
        $data['app_id'] = 0;
        $data['domain_id'] = 0;
        $data['account_id'] = 0;
        $data['status'] = 0;

        if ($this->app) {
            $data['app_id'] = $this->app['id'];
        }

        if ($this->domains->getDomain()) {
            $data['domain_id'] = $this->domains->getDomain()['id'];
        }

        $account = $this->auth->account();

        if ($account) {
            $data['account_id'] = $account['id'];

            if ($data['email_to'] !== '') {
                $data['email_to'] = $account['email'] . ',' . $data['email_to'];
            } else {
                $data['email_to'] = $account['email'];
            }
        } else {
            if ($data['domain_id'] != 0) {
                $data['email_to'] = 'no-reply@' . $this->domains->get(['id' => $data['domain_id']])['name'];
            } else {
                $data['email_to'] = 'no-reply@' . $this->domains->domains[0]['name'];
            }
        }

        $data['email_to'] = explode(',', $data['email_to']);

        foreach ($data['email_to'] as &$email) {
            $email = trim($email);
        }

        if (isset($data['fields'])) {
            $data['fields'] = Json::decode($data['fields'], true);
        }

        if (isset($data['fields']['data']) && is_array($data['fields']['data'])) {
            $data['fields'] = $data['fields']['data'];
        }

        if (isset($data['upload']) && $data['upload'] !== '') {
            $data['file'] = $data['upload'];
        }

        if ($this->add($data)) {
            if (isset($data['file'])) {
                $this->basepackages->storages->changeOrphanStatus($data['file'], null, true);
            }

            $task = $this->basepackages->workers->tasks->findByParameter($data['type'], 'process');

            if ($task && $task['force_next_run'] === null) {
                $this->basepackages->workers->tasks->forceNextRun(['id' => $task['id']]);
            }

            $this->addResponse('Added ' . $data['type'] . ' request');
        } else {
            $this->addResponse('Error adding new request.', 1);
        }
    }

    public function updateImportexport(array $data)
    {
        //
    }

    public function removeImportexport(array $data)
    {
        //
    }

    public function getPackageFields($id, $structure = false, $getPackageFields = false)
    {
        $component = $this->modules->components->get(['id' => $id]);

        if ($component) {
            $component['settings'] = Json::decode($component['settings'], true);

            $package = $this->modules->packages->get(['name' => $component['settings']['importexportPackage']]);

            $packageObj = $this->usePackage($package['class']);

            if ($packageObj) {
                $modelToUse = $packageObj->getModelToUse();

                $model = new $modelToUse;

                $packageFields = $packageObj->getModelsColumnMap();

                if ($packageFields && isset($packageFields['columns'])) {
                    $columns = [];
                    $requiredFields = [];

                    foreach ($packageFields['columns'] as $column) {
                        if ($structure) {
                            $columns[$column] = $column;
                        } else {
                            $columns[$column] = ucfirst(str_replace('_', ' ', $column));
                        }
                    }

                    if (isset($packageFields['required']) && is_array($packageFields['required']) && count($packageFields['required']) > 0) {
                        foreach ($packageFields['required'] as &$required) {
                            if ($structure) {
                                $requiredFields[$required] = $required;
                            }
                        }
                    }

                    if ($structure && $getPackageFields) {
                        $packageFields['columns'] = $columns;
                        $packageFields['required'] = $requiredFields;

                        return $packageFields;
                    } else {
                        $this->packagesData->fields = $columns;

                        return $columns;
                    }
                }


                $this->addResponse('Component package fields not found', 1);

                return false;
            }

            $this->addResponse('Component package not found', 1);

            return false;
        }

        $this->addResponse('Component not found', 1);

        return false;
    }

    public function processExports($jobId)
    {
        $exports = $this->getByParams(
            [
                'conditions'    => 'type = :type: AND status = :status:',
                'bind'          =>
                    [
                        'type'  => 'export',
                        'status'=> '0'
                    ]
            ]
        );

        if ($exports && count($exports) > 0) {
            foreach ($exports as $exportKey => $export) {
                if (!$this->processExport($export, $jobId)) {
                    $export['status'] = 3;//Error
                    $export['job_id'] = $jobId;

                    $this->update($export);
                }
            }
        }
    }

    protected function processExport($export, $jobId)
    {
        $export['job_id'] = $jobId;

        $header = Json::decode($export['fields']);

        $component = $this->modules->components->get(['id' => $export['component_id']]);

        if ($component) {
            $component['settings'] = Json::decode($component['settings'], true);

            $package = $this->modules->packages->get(['name' => $component['settings']['importexportPackage']]);

            if ($package) {
                $packageObj = $this->usePackage($package['class']);

                if ($packageObj) {
                    if ($export['filter_id'] && $export['filter_id'] !== '') {
                        $filter = $this->basepackages->filters->getById($export['filter_id']);

                        $conditions = $filter['conditions'];
                    } else {
                        $conditions = '';
                    }

                    $records = $packageObj->getDataWithConditions(
                        [
                            'columns'       => $header
                        ],
                        $conditions
                    );

                    foreach ($records as &$record) {
                        if (is_array($record)) {
                            $record = $this->jsonData($record);
                        }
                    }

                    if ($records) {
                        $csv = Writer::createFromString();

                        $csv->insertOne($header);

                        $csv->insertAll($records);
                    }

                    $file = $this->writeCSVFile($export, $csv);

                    if ($file) {
                        $export['file'] = $file;

                        $this->notifyEmail($export);

                        $export['status'] = 2;//Complete

                        $this->update($export);

                        $this->addResponse('Export request ID:' . $export['id'] . ' execution complete.');
                    }

                    return true;
                }
            }
            //Error Package not found
            return false;
        }
        //Error component not found
        return false;
    }

    protected function writeCSVFile($data, $csv)
    {
        $csvString = $csv->toString();

        if (function_exists('mb_strlen')) {
            $size = mb_strlen($csvString, '8bit');
        } else {
            $size = strlen($csvString);
        }

        if (isset($data['id'])) {
            $name = 'export_' . $data['id'] . '.csv';
        } else if (isset($data['component_id'])) {
            $name = 'structure_component_' . $data['component_id'] . '.csv';

            $file = $this->basepackages->storages->getFileInfo(null, 'structure_component_' . $data['component_id'] . '.csv');

            if ($file) {
                return $file['uuid'];
            }
        } else {
            $name = 'name_missing';
        }

        if ($this->basepackages->storages->storeFile(
                'private',
                'importexport',
                $csvString,
                $name,
                $size,
                'text/csv'
            )
        ) {
            $this->basepackages->storages->changeOrphanStatus($this->basepackages->storages->packagesData->storageData['uuid']);

            return $this->basepackages->storages->packagesData->storageData['uuid'];
        }

        return false;
    }

    public function getStructureFileLink($data)
    {
        if (!isset($data['component_id'])) {
            $this->addResponse('Component Id missing', 1);

            return;
        }

        $component = $this->modules->components->get(['id' => $data['component_id']]);

        if ($component) {
            $component['settings'] = Json::decode($component['settings'], true);

            if (isset($component['settings']['importexportSample'])) {
                $file = $this->writeStructureCSVFile($data, $component['settings']['importexportSample']);
            } else {
                $file = $this->writeStructureCSVFile($data);
            }
        }

        if (isset($file)) {
            $this->addResponse('structure file generated', 0, ['link' => $this->links->url('system/storages/q/uuid/' . $file)]);

            return true;
        } else {
            $this->addResponse('error generating structure file', 1, []);
        }
    }

    protected function writeStructureCSVFile($data, array $structure = null)
    {
        if ($structure) {
            $csv = Writer::createFromString();

            $csv->insertOne($structure[0]);

            unset($structure[0]);

            $csv->insertAll($structure);

            return $this->writeCSVFile($data, $csv);
        } else {
            $fields = $this->getPackageFields($data['component_id'], true);

            if ($fields) {
                $csv = Writer::createFromString();

                $csv->insertOne($fields);

                return $this->writeCSVFile($data, $csv);
            }

            return false;
        }
    }

    //Can be used after install/upgrade package
    public function removeAllStructureFiles()
    {
        $files = $this->basepackages->storages->getFileInfo(null, 'structure_component', true);

        if ($files && count($files) > 0) {
            foreach ($files as $file) {
                if (isset($file['uuid'])) {
                    $this->basepackages->storages->removeFile($file['uuid'], 'private', true);
                }
            }
        }
    }

    public function readFile(array $data, $viaImport = false)
    {
        if (!isset($data['uuid']) || !isset($data['component_id'])) {
            $this->addResponse('File UUID/Component ID missing', 1, []);

            return false;
        }

        if ($viaImport) {
            $data['uuid'] = Json::decode($data['uuid']);
            $data['uuid'] = $data['uuid'][0];
        }

        $file = $this->basepackages->storages->getFileInfo($data['uuid']);

        try {
            $csv = Reader::createFromPath(base_path('private/' . $file['storages_id'] . '/data/' . $file['uuid_location'] . $file['uuid']));

            $csv->setHeaderOffset(0);

            $headersArr = $csv->getHeader();

            $headers = [];

            foreach ($headersArr as $headerValue) {
                $headers[$headerValue] = $headerValue;
            }

            $fields = $this->getPackageFields($data['component_id'], true, true);

            if ($headers !== $fields['columns']) {
                $this->addResponse('File header does not match component structure, please download structure and upload file again', 1, []);

                return false;
            }

            if (isset($data['readonly']) && $data['readonly'] == true || $viaImport) {
                if (!$viaImport) {
                    $records[0] = $headers;

                    $recordNo = 1;
                } else {
                    $recordNo = 0;
                }

                foreach ($csv as $record) {
                    $records[$recordNo] = $record;

                    $recordNo++;
                }

                $this->addResponse('Ok', 0, ['rows' => $records]);

                return $records;
            }

            $uniqueColumnsData = [];

            if (isset($fields['columnUnique']) && count($fields['columnUnique']) > 0) {
                $component = $this->modules->components->get(['id' => $data['component_id']]);

                if ($component) {
                    $component['settings'] = Json::decode($component['settings'], true);

                    $package = $this->modules->packages->get(['name' => $component['settings']['importexportPackage']]);

                    $packageObj = $this->usePackage($package['class']);

                    if ($packageObj) {
                        $modelToUse = $packageObj->getModelToUse();

                        foreach ($fields['columnUnique'] as $columnUniqueKey => $columnUniqueValue) {
                            if (isset($fields['model'][$columnUniqueKey]) &&
                                $fields['model'][$columnUniqueKey] !== $modelToUse
                            ) {
                                unset($fields['columnUnique'][$columnUniqueKey]);
                            } else if (!isset($fields['model'][$columnUniqueKey])) {
                                unset($fields['columnUnique'][$columnUniqueKey]);
                            }
                        }

                        $uniqueColumnsDataArr = $this->getByParams(['columns'=>$fields['columnUnique'], 'conditions'=>''], true, false, $modelToUse);

                        foreach ($uniqueColumnsDataArr as $columnsData) {
                            foreach ($columnsData as $columnKey => $columnValue) {
                                if (!isset($uniqueColumnsData[$columnKey])) {
                                    $uniqueColumnsData[$columnKey] = [];
                                }

                                array_push($uniqueColumnsData[$columnKey], $columnValue);
                            }
                        }
                    }
                }
            }

            $records[0]['csvrowno'] = 'CSV ROW #';
            $records[0]['csvrowstatus'] = 'CSV ROW STATUS';
            $records[0] = array_merge($records[0], $headers);

            $row = 2;
            $recordNo = 1;

            foreach ($csv as $recordKey => $record) {
                $records[$recordNo]['csvrowno'] = $row;
                $records[$recordNo]['csvrowstatus'] = 'OK';

                foreach ($record as $recKey => $rec) {
                    $recType = isset($fields['dataTypes'][$recKey]) ? true : false;
                    $recNumber = isset($fields['number'][$recKey]) ? true : false;
                    $recRequired = isset($fields['required'][$recKey]) ? true : false;

                    //Required
                    if ($recRequired) {
                        if ($rec === '') {
                            if ($recKey === 'id' && $rec === '') {
                                $records[$recordNo][$recKey] = $rec;
                                continue;
                            }

                            $records[$recordNo]['csvrowstatus'] = 'ERROR';
                            $records[$recordNo][$recKey] = 'ERROR: Field is required.';
                            continue;
                        }
                    }

                    //Duplicates
                    if (isset($uniqueColumnsData[$recKey])) {
                        if (in_array($rec, $uniqueColumnsData[$recKey])) {
                            if (isset($record['id']) && $record['id'] === '') {
                                $records[$recordNo]['csvrowstatus'] = 'ERROR';
                                $records[$recordNo][$recKey] = 'ERROR: Duplicate field.';
                                continue;
                            }
                        }
                    }

                    //Numberic
                    if ($recNumber) {
                        if (is_numeric($rec)) {
                            $records[$recordNo][$recKey] = $rec;
                            continue;
                        } else {
                            $records[$recordNo]['csvrowstatus'] = 'ERROR';
                            $records[$recordNo][$recKey] = 'ERROR: Field should be a number.';
                            continue;
                        }
                    }

                    //TypeCheck - Check for JSON
                    if ($recType) {
                        if ($fields['dataTypes'][$recKey] === 15) {//JSON
                            try {
                                Json::decode($rec, true);
                            } catch (\Exception $e) {
                                $records[$recordNo]['csvrowstatus'] = 'ERROR';
                                $records[$recordNo][$recKey] = 'ERROR: Field should be JSON.';
                                continue;
                            }
                        }
                    }

                    $records[$recordNo][$recKey] = $rec;
                }

                $row++;
                $recordNo++;
            }

            $this->addResponse('Ok', 0, ['rows' => $records]);

            return $records;
        } catch (\Exception $e) {
            if ($e->getCode() === 2) {
                $this->addResponse('Error loading file, please upload again', 1, []);

                return false;
            }
        }
    }

    public function processImports($jobId)
    {
        $imports = $this->getByParams(
            [
                'conditions'    => 'type = :type: AND status = :status:',
                'bind'          =>
                    [
                        'type'  => 'import',
                        'status'=> '0'
                    ]
            ]
        );

        if ($imports && count($imports) > 0) {
            foreach ($imports as $importKey => $import) {
                $import['uuid'] = $import['file'];

                if (!$this->processImport($import, $jobId)) {
                    $import['status'] = 3;//Error
                    $import['job_id'] = $jobId;

                    $this->update($import);
                }
            }
        }
    }

    public function processImport($import, $jobId)
    {
        $component = $this->modules->components->get(['id' => $import['component_id']]);

        if (!$component) {
            return false;
        }

        $rows = $this->readFile($import, true);

        if (!$rows) {
            return false;
        }

        $component['settings'] = Json::decode($component['settings'], true);

        if (!isset($component['settings']['importexportPackage'])) {
            throw new \Exception("Import package missing");
        }

        if (!isset($component['settings']['importMethod'])) {
            throw new \Exception("Component import method missing");
        }

        $package = $this->modules->packages->get(['name' => $component['settings']['importexportPackage']]);

        if ($package) {
            $packageObj = $this->usePackage($package['class']);

            if ($packageObj) {
                if (method_exists($package['class'], 'add' . $component['settings']['importMethod']) &&
                    method_exists($package['class'], 'update' . $component['settings']['importMethod'])
                ) {
                    foreach ($rows as $rowKey => $row) {
                        try {
                            if (isset($row['id']) && $row['id'] !== '' ){
                                $importMethod = 'update' . ucfirst($component['settings']['importMethod']);
                            } else {
                                $importMethod = 'add' . ucfirst($component['settings']['importMethod']);
                            }

                            $packageObj->$importMethod($row);
                        } catch (\Exception $e) {
                            $import['status'] = 3;//Complete
                            $import['job_id'] = $jobId;

                            $this->update($import);

                            throw $e;
                        }
                    }

                    $this->notifyEmail($import);

                    $import['status'] = 2;//Complete

                    $this->update($import);

                    $this->addResponse('Import request ID:' . $import['id'] . ' execution complete.');

                    return true;
                } else {
                    throw new \Exception('Package import method ' . $component['settings']['importMethod'] . ' does not exists.');
                }
            } else {
                throw new \Exception('Package ' . $package['class'] . ' does not exists.');
            }
        }
    }

    protected function notifyEmail($task)
    {
        $this->basepackages->notifications->addNotification(
            ucfirst($task['type']) . ' request ID:' . $task['id'] . ' execution complete.',
            null,
            $task['app_id'],
            $task['account_id'],
            0,
            'ImportExport',
            $task['id'],
            0
        );

        $emailToAddressesArr = Json::decode($task['email_to'], true);
        $emailToAddresses = [];

        if (count($emailToAddressesArr) > 0) {
            foreach ($emailToAddressesArr as $emailToAddress) {
                array_push($emailToAddresses, trim($emailToAddress));
            }
        } else {
            array_push($emailToAddresses, 'no-reply@' . $this->domains->domains[0]['name']);
        }

        $emailData['app_id'] = $task['app_id'];
        $emailData['domain_id'] = $task['domain_id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 2;
        $emailData['confidential'] = 0;
        $emailData['to_addresses'] = Json::encode($emailToAddresses);
        $emailData['subject'] = ucfirst($task['type']) . ' request complete.';

        if ($task['app_id'] != 0) {
            $route = $this->apps->get(['id' => $task['app_id']])['route'];
        } else {
            $route = 'admin';
        }

        if ($task['domain_id'] != 0) {
            $domain = $this->domains->get(['id' => $task['domain_id']])['name'];
        } else {
            $domain = $this->domains->domains[0]['name'];
        }

        $url = Str::reduceSlashes('https://' . $domain . '/' . $route . '/' . 'system/tools/importexport/q/id/' . $task['id']);

        if ($task['type'] === 'export') {
            $emailData['body'] =
                ucfirst($task['type']) . ' request ID:' . $task['id'] . ' execution complete. To download file, click ' .
                '<a href="' . $url . '">here</a>';
        } else if ($task['type'] === 'import') {
            $emailData['body'] = ucfirst($task['type']) . ' request ID:' . $task['id'] . ' execution complete.';
        }

        $this->basepackages->emailqueue->add($emailData);
    }
}