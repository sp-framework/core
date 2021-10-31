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
                $data['email_to'] = 'no-reply@' . $this->domains->getIdDomain($data['domain_id'])['name'];
            } else {
                $data['email_to'] = 'no-reply@' . $this->domains->domains[0]['name'];
            }
        }

        $data['email_to'] = explode(',', $data['email_to']);

        foreach ($data['email_to'] as &$email) {
            $email = trim($email);
        }

        $data['fields'] = Json::decode($data['fields'], true);

        if (isset($data['fields']['data']) && is_array($data['fields']['data'])) {
            $data['fields'] = $data['fields']['data'];
        }

        if ($this->add($data)) {
            $task = $this->basepackages->workers->tasks->findByParameter("export", 'process');

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
        if (!checkCtype($data['name'])) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Importexport name cannot have special characters';

            return false;

        } else {
            $brand = $this->getById($data['id']);

            if ($this->update($data)) {

                $this->basepackages->storages->changeOrphanStatus($data['logo'], $brand['logo']);

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' brand';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error updating brand.';
            }
        }
    }

    public function removeImportexport(array $data)
    {
        $brand = $this->getById($data['id']);

        if ($brand['product_count'] && (int) $brand['product_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Importexport is assigned to ' . $brand['product_count'] . ' products. Error removing brand.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->basepackages->storages->changeOrphanStatus(null, $brand['logo']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed brand';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing brand.';
        }
    }

    public function getPackageFields($id, $structure = false)
    {
        $component = $this->modules->components->getComponentById($id);

        if ($component) {
            $component['settings'] = Json::decode($component['settings'], true);

            $package = $this->modules->packages->getNamePackage($component['settings']['importexportPackage']);

            $packageObj = $this->usePackage($package['class']);

            if ($packageObj) {
                $modelToUse = $packageObj->getModelToUse();

                $model = new $modelToUse;

                $packageFields = $packageObj->getModelsColumnMap();

                if ($packageFields && isset($packageFields['columns'])) {
                    $fields = [];

                    foreach ($packageFields['columns'] as $column) {
                        if ($structure) {
                            $fields[$column] = $column;
                        } else {
                            $fields[$column] = ucfirst(str_replace('_', ' ', $column));
                        }
                    }

                    $this->packagesData->fields = $fields;

                    return $fields;
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

        $component = $this->modules->components->getComponentById($export['component_id']);

        if ($component) {
            $component['settings'] = Json::decode($component['settings'], true);

            $package = $this->modules->packages->getNamePackage($component['settings']['importexportPackage']);

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

                        $this->basepackages->notifications->addNotification(
                            'Export request ID:' . $export['id'] . ' execution complete.',
                            null,
                            $export['app_id'],
                            $export['account_id'],
                            0,
                            'ImportExport',
                            $export['id'],
                            0
                        );

                        $emailToAddressesArr = Json::decode($export['email_to'], true);
                        $emailToAddresses = [];

                        if (count($emailToAddressesArr) > 0) {
                            foreach ($emailToAddressesArr as $emailToAddress) {
                                array_push($emailToAddresses, trim($emailToAddress));
                            }
                        } else {
                            array_push($emailToAddresses, 'no-reply@' . $this->domains->domains[0]['name']);
                        }

                        $emailData['app_id'] = $export['app_id'];
                        $emailData['domain_id'] = $export['domain_id'];
                        $emailData['status'] = 1;
                        $emailData['priority'] = 1;
                        $emailData['confidential'] = 0;
                        $emailData['to_addresses'] = Json::encode($emailToAddresses);
                        $emailData['subject'] = 'Export request complete.';

                        if ($export['app_id'] != 0) {
                            $route = $this->apps->getIdApp($export['app_id'])['route'];
                        } else {
                            $route = 'admin';
                        }

                        if ($export['domain_id'] != 0) {
                            $domain = $this->domains->getIdDomain($export['domain_id'])['name'];
                        } else {
                            $domain = $this->domains->domains[0]['name'];
                        }

                        $url = Str::reduceSlashes('https://' . $domain . '/' . $route . '/' . 'system/tools/importexport/q/id/' . $export['id']);

                        $emailData['body'] =
                            'Export request ID:' . $export['id'] . ' execution complete. To download file, click ' .
                            '<a href="' . $url . '">here</a>';

                        $this->basepackages->emailqueue->addToQueue($emailData);

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

        $component = $this->modules->components->getComponentById($data['component_id']);

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

    public function readFile(array $data)
    {
        if (!isset($data['uuid']) || !isset($data['component_id'])) {
            $this->addResponse('File UUID/Component ID missing', 1, []);

            return false;
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

            if ($headers !== $this->getPackageFields($data['component_id'], true)) {
                $this->addResponse('File header does not match component structure, please download structure and upload file again', 1, []);

                return false;
            }

            $records[0][0] = 1;
            $records[0] = $headers;

            $row = 2;

            foreach ($csv as $record) {
                $record[0] = $row;

                array_push($records, $record);

                $row++;
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

    public function processFile($data)
    {
        $rows = $this->readFile($data);

        if (!$rows) {
            return false;
        }

        if (isset($data['row_index'])) {

            $this->addResponse('Ok', 0, []);

            $this->addResponse('Row has errors', 1, ['errors' => ['Duplicate name', 'Required permissions']]);

            return;
        }

        foreach ($rows as $rowKey => $row) {

        }
        var_dump($rows);die();
        $this->addResponse('Ok', 0, []);

        $this->addResponse('Row has errors', 1, ['errors' => ['Duplicate name', 'Required permissions']]);
        return;
        var_dump($rows);die();
    }

    protected function processRow($data, $row)
    {
        //
    }
}