<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Carbon\Carbon;
use Ifsnop\Mysqldump\Mysqldump;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToReadFile;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class BackupRestore extends BasePackage
{
    protected $zip;

    protected $backupInfo;

    protected $backupLocation = '.backups/';

    protected $now;

    protected $backupProgressMethods;

    protected $restoreProgressMethods;

    public function init($process = 'backup')
    {
        $this->zip = new \ZipArchive;

        $this->backupInfo = [];

        if (!$this->localContent->fileExists($this->backupLocation)) {
            $this->localContent->createDirectory($this->backupLocation);
        }

        $this->basepackages->progress->deleteProgressFile();

        if ($process === 'backup') {
            $this->registerBackupProgressMethods();
        } else if ($process === 'restore') {
            $this->registerRestoreProgressMethods();
        }

        return $this;
    }

    protected function withProgress($method, $arguments)
    {
        if (method_exists($this, $method)) {
            if (is_array($arguments)) {
                $arguments = [$arguments];
            }

            $this->basepackages->progress->updateProgress($method, null, false);

            $call = call_user_func_array([$this, $method], $arguments);

            $this->basepackages->progress->updateProgress($method, $call, false);

            return $call;
        }
    }

    public function backup(array $data)
    {
        set_time_limit(300);//5 mins
        $tokenkey = array_search($this->security->getRequestToken(), $data);
        if ($tokenkey) {
            unset($data[$tokenkey]);
        }

        $noOptions = true;
        foreach ($data as $key => $value) {
            if ($value == 'true') {
                $noOptions = false;
            }
        }
        if ($noOptions) {
            $this->addResponse('Nothing to backup!', 1, []);

            $this->basepackages->progress->preCheckComplete(false);

            $this->basepackages->progress->resetProgress();

            return false;
        }

        $this->now = Carbon::now();
        $this->backupInfo['request'] = $data;
        $this->backupInfo['takenAt'] = $this->now->format('Y-m-d H:i:s');
        $this->backupInfo['createdBy'] = $this->auth->account() ? $this->auth->account()['email'] : 'System';
        $this->backupInfo['backupName'] = 'backup-' . $this->now->getTimestamp() . '.zip';
        $this->backupInfo['dbs'] = [];
        $this->backupInfo['dirs'] = [];
        $this->backupInfo['files'] = [];

        if (isset($this->backupInfo['request']['keys']) && $this->backupInfo['request']['keys'] == 'true' ||
            isset($this->backupInfo['request']['database']) && $this->backupInfo['request']['database'] == 'true'
        ) {
            if (isset($this->backupInfo['request']['keys']) && $this->backupInfo['request']['keys'] == 'true') {
                $this->backupInfo['request']['systems_dir'] = 'true';
            }

            if (!isset($this->backupInfo['request']['password_protect']) ||
                (isset($this->backupInfo['request']['password_protect']) &&
                $this->backupInfo['request']['password_protect'] === '')
            ) {
                $this->addResponse('Protect password missing!', 1, []);

                $this->basepackages->progress->preCheckComplete(false);

                $this->basepackages->progress->resetProgress();

                return false;
            }
        }

        $this->basepackages->progress->preCheckComplete();

        foreach ($this->backupProgressMethods as $method) {
            if ($method['method'] === 'performDbBackup') {
                if (!isset($this->backupInfo['request']['database']) ||
                    (isset($this->backupInfo['request']['database']) && $this->backupInfo['request']['database'] != 'true')
                ) {
                    continue;
                }
            }

            if ($this->withProgress($method['method'], $data) === false) return false;

            usleep(500);
        }

        return true;
    }

    protected function generateStructure(array $data)
    {
        $this->zip->open(base_path($this->backupLocation . $this->backupInfo['backupName']), $this->zip::CREATE);

        if (isset($this->backupInfo['request']['apps_dir']) && $this->backupInfo['request']['apps_dir'] == 'true') {
            $this->getContent($this->basepackages->utils->scanDir('apps/'));
        }
        if (isset($this->backupInfo['request']['systems_dir']) && $this->backupInfo['request']['systems_dir'] == 'true') {
            $this->getContent($this->basepackages->utils->scanDir('system/'));
        }
        if (isset($this->backupInfo['request']['public_dir']) && $this->backupInfo['request']['public_dir'] == 'true') {
            $this->getContent($this->basepackages->utils->scanDir('public/'));
        }
        if (isset($this->backupInfo['request']['private_dir']) && $this->backupInfo['request']['private_dir'] == 'true') {
            $this->getContent($this->basepackages->utils->scanDir('private/'));
        }
        if (isset($this->backupInfo['request']['var_dir']) && $this->backupInfo['request']['var_dir'] == 'true') {
            $this->getContent($this->basepackages->utils->scanDir('var/'));
        }
        if (isset($this->backupInfo['request']['external_dir']) && $this->backupInfo['request']['external_dir'] == 'true') {
            if (isset($this->backupInfo['request']['external_vendor_dir']) && $this->backupInfo['request']['external_vendor_dir'] == 'true') {
                $this->getContent($this->basepackages->utils->scanDir('external/'));
            } else {
                $this->getContent($this->basepackages->utils->scanDir('external/', false));
            }
        }
        if (isset($this->backupInfo['request']['old_backups']) && $this->backupInfo['request']['old_backups'] == 'true') {
            $this->getContent($this->basepackages->utils->scanDir('.backups/'));
        }

        foreach ($this->backupInfo['files'] as $file) {
            if (!$this->addToZip(base_path($file), $file)) {
                return false;
            }
        }

        return true;
    }

    protected function zipBackupFiles(array $data)
    {
        if (isset($this->backupInfo['request']['password_protect']) && $this->backupInfo['request']['password_protect'] !== '') {
            $this->backupInfo['request']['password_protect'] =
                $this->secTools->hashPassword($this->backupInfo['request']['password_protect'], 4);
        }

        try {
            $this->localContent->write('var/tmp/backupInfo.json' , Json::encode($this->backupInfo));
        } catch (FilesystemException | UnableToWriteFile $exception) {
            throw $exception;
        }

        //Dont Encrypt info file as we need to know if encryption is applied or not during restore. We can encrypt the content in case we want to hide something (like we are encrypting the password_protect password)
        if (!$this->addToZip(base_path('var/tmp/backupInfo.json'), 'backupInfo.json', false)) {
            return false;
        }

        $this->zip->close();

        return true;
    }

    protected function performDbBackup(array $data)
    {
        foreach ($this->core->core['settings']['dbs'] as $dbKey => $db) {
            try {
                $db['password'] = $this->crypt->decryptBase64($db['password'], $this->getDbKey($db));

                $dumper =
                    new Mysqldump(
                        'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
                        $db['username'],
                        $db['password'],
                        ['default-character-set' => Mysqldump::UTF8MB4]
                    );

                $fileName = 'db' . $db['dbname'] . $this->now->getTimestamp() . '.sql';

                $dumper->start(base_path('var/tmp/' . $fileName));

                if (!$this->addToZip(base_path('var/tmp/' . $fileName), 'dbs/' . $fileName)) {
                    return false;
                }

                $db['password'] = $this->crypt->encryptBase64($db['password'], $this->getDbKey($db));

                $this->backupInfo['dbs'][$db['dbname']] = $db;
                $this->backupInfo['dbs'][$db['dbname']]['file'] = 'dbs/' . $fileName;
            } catch (\Exception $e) {
                $this->addResponse('Backup Error: ' . $e->getMessage(), 1);

                $this->basepackages->progress->resetProgress();

                return false;
            }
        }

        $this->addToZip(base_path('system/.dbkeys'), 'system/.dbkeys');

        return true;
    }

    protected function finishBackup(array $data)
    {
        if ($this->basepackages->storages->storeFile(
                'private',
                '.backups',
                null,
                $this->backupInfo['backupName'],
                filesize(base_path('.backups/' . $this->backupInfo['backupName'])),
                'application/zip',
                true
            )
        ) {
            $this->basepackages->storages->changeOrphanStatus(
                null,
                null,
                false,
                null,
                $this->backupInfo['backupName']
            );

            $this->addResponse('Generated backup ' . $this->backupInfo['backupName'] . '.',
                               0,
                               ['filename'  => $this->backupInfo['backupName'],
                                'uuid'      => $this->basepackages->storages->packagesData->responseData['uuid'],
                                'request'   => $data
                               ]
            );

            return true;
        }

        return false;
    }

    protected function addToZip($absolutePath, $relativePath, $encrypt = true, $passwordProtectOrg = null)
    {
        if (isset($this->backupInfo['request']['password_protect']) &&
            $this->backupInfo['request']['password_protect'] !== '' &&
            $encrypt
        ) {
            if (!$passwordProtectOrg) {
                $passwordProtectOrg = $this->backupInfo['request']['password_protect'];
            }

            $this->zip->addFile($absolutePath, $relativePath);

            if (!$this->zip->setEncryptionName($relativePath, \ZipArchive::EM_AES_256, $passwordProtectOrg)) {
                $name = $this->zip->getNameIndex($this->zip->numFiles - 1);

                if ($relativePath === $name) {
                    $this->zip->deleteIndex($this->zip->numFiles - 1);
                }

                $this->zip->close();

                $this->addResponse('Could not set provided password for file ' . $name, 1, []);

                $this->basepackages->progress->resetProgress();

                return false;
            }
        } else {
            if (!$this->zip->addFile($absolutePath, $relativePath)) {
                $name = $this->zip->getNameIndex($this->zip->numFiles - 1);

                $this->addResponse('Could not zip file: ' . $name, 1, []);

                $this->basepackages->progress->resetProgress();

                return false;
            }
        }

        return true;
    }

    protected function restore(array $data)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Please provide backup file id', 1, []);

            $this->basepackages->progress->resetProgress();

            return false;
        }

        $backupInfo = $this->analyseBackinfoFile($data['id']);

        if (!$backupInfo) {
            return false;
        }

        if (isset($backupInfo['request']['password_protect']) && $backupInfo['request']['password_protect'] !== '') {
            if (!isset($data['password'])) {
                $this->addResponse('Please provide backup file password', 1, []);

                $this->basepackages->progress->resetProgress();

                return false;
            }

            if (!$this->security->checkHash($data['password'], $backupInfo['request']['password_protect'])) {
                $this->addResponse('Backup password incorrect! Please provide correct password', 1, []);

                $this->basepackages->progress->resetProgress();

                return false;
            }

            $this->zip->setPassword($data['password']);
        }

        $fileNameLocation = explode('.zip', $backupInfo['backupName'])[0];

        if (!$this->zip->extractTo(base_path('var/tmp/backups/' . $fileNameLocation))) {
            $this->addResponse('Error unzipping backup file. Please upload backup again.', 1);

            $this->basepackages->progress->resetProgress();

            return false;
        }

        $fileNameDate = str_replace('backup-', '', $fileNameLocation);

        if (isset($data['dbs'])) {
            if (!isset($data['root_username']) || !isset($data['root_username'])) {
                $this->addResponse('Root Username & Password required to restore databases.', 1);

                $this->basepackages->progress->resetProgress();

                return false;
            }

            if (is_array($data['dbs']) && count($data['dbs']) > 0) {
                foreach ($data['dbs'] as $dbs) {
                    if (!isset($backupInfo['dbs'][$dbs])) {
                        $this->addResponse('Database ' . $dbs . ' is not in the backup file uploaded.', 1);

                        $this->basepackages->progress->resetProgress();

                        return false;
                    }
                }

                $dbConfig['username'] = $data['root_username'];
                $dbConfig['password'] = $data['root_password'];
                $dbConfig['dbname'] = 'mysql';

                try {
                    $dbkeys = Json::decode($this->localContent->read('var/tmp/backups/' . $fileNameLocation . '/system/.dbkeys'), true);

                    foreach ($data['dbs'] as $dbs) {
                        $dbConfig['username'] = $data['root_username'];
                        $dbConfig['password'] = $data['root_password'];
                        $dbConfig['dbname'] = 'mysql';
                        $this->db = new Mysql($dbConfig);

                        $this->executeSQL(
                            "CREATE DATABASE IF NOT EXISTS " . $backupInfo['dbs'][$dbs]['dbname'] . " CHARACTER SET " . $backupInfo['dbs'][$dbs]['charset'] . " COLLATE " . $backupInfo['dbs'][$dbs]['collation']
                        );

                        $backupInfo['dbs'][$dbs]['password'] = $this->crypt->decryptBase64($backupInfo['dbs'][$dbs]['password'], $dbkeys[$dbs]);

                        $checkUser = $this->executeSQL("SELECT * FROM `user` WHERE `User` LIKE ?", [$backupInfo['dbs'][$dbs]['username']]);

                        if ($checkUser->numRows() === 0) {
                            $this->executeSQL("CREATE USER ?@'%' IDENTIFIED WITH mysql_native_password BY ?;", [$backupInfo['dbs'][$dbs]['username'], $backupInfo['dbs'][$dbs]['password']]);
                        }

                        $this->executeSQL("GRANT ALL PRIVILEGES ON " . $backupInfo['dbs'][$dbs]['dbname'] . ".* TO ?@'%' WITH GRANT OPTION;", [$backupInfo['dbs'][$dbs]['username']]);


                        $allTables = $this->db->listTables($backupInfo['dbs'][$dbs]['dbname']);

                        if (count($allTables) > 0) {
                            $dbConfig['dbname'] = $backupInfo['dbs'][$dbs]['dbname'];
                            $this->db = new Mysql($dbConfig);

                            foreach ($allTables as $tableKey => $tableValue) {
                                $this->db->dropTable($tableValue);
                            }
                        }

                        $dumper =
                            new Mysqldump(
                                'mysql:host=' . $backupInfo['dbs'][$dbs]['host'] . ';dbname=' . $backupInfo['dbs'][$dbs]['dbname'],
                                $backupInfo['dbs'][$dbs]['username'],
                                $backupInfo['dbs'][$dbs]['password']
                            );

                        $dumper->restore(base_path('var/tmp/backups/' . $fileNameLocation . '/dbs/db' . $dbs . $fileNameDate . '.sql'));
                    }
                } catch (\Exception $e) {
                    $this->addResponse('Restore Error: ' . $e->getMessage(), 1);

                    $this->basepackages->progress->resetProgress();

                    return false;
                }
            } else {
                $this->addResponse('Please provide database(s) to restore.', 1);

                $this->basepackages->progress->resetProgress();

                return false;
            }
        }

        try {
            $backupInfo = Json::decode($this->localContent->read('var/tmp/backups/' . $fileNameLocation . '/backupInfo.json'), true);
        } catch (\ErrorException | FilesystemException | UnableToReadFile | InvalidArgumentException $exception) {
            $this->addResponse('Error reading/accessing backupInfo.json file. Please upload backup again with correct file.', 1);

            $this->basepackages->progress->resetProgress();

            return false;
        }

        if (isset($data['restore_structure'])) {
            if (isset($data['restore_structure']['folders'])) {
                foreach ($data['restore_structure']['folders'] as $restoreStructureFolders) {
                    if (!isset($backupInfo['dirs'][$restoreStructureFolders])) {
                        $this->addResponse('Folder with ID: ' . $restoreStructureFolders . ' does not exist in the backup zip file.', 1);

                        $this->basepackages->progress->resetProgress();

                        return false;
                    }
                }
            }

            if (isset($data['restore_structure']['files'])) {
                foreach ($data['restore_structure']['files'] as $restoreStructureFiles) {
                    if (!isset($backupInfo['files'][$restoreStructureFiles])) {
                        $this->addResponse('File with ID: ' . $restoreStructureFiles . ' does not exist in the backup zip file.', 1);

                        $this->basepackages->progress->resetProgress();

                        return false;
                    }
                }
            }

            if (isset($data['restore_structure']['folders'])) {
                foreach ($data['restore_structure']['folders'] as $restoreStructureFolders) {
                    try {
                        $this->localContent->createDirectory($backupInfo['dirs'][$restoreStructureFolders]);
                    } catch (FilesystemException | UnableToCreateDirectory $exception) {
                        $this->addResponse('Filed to create directory with ID: ' . $restoreStructureFolders, 1);

                        $this->basepackages->progress->resetProgress();

                        return false;
                    }
                }
            }

            if (isset($data['restore_structure']['files'])) {
                foreach ($data['restore_structure']['files'] as $restoreStructureFiles) {
                    try {
                        $this->localContent->copy(
                            'var/tmp/backups/' . $fileNameLocation . '/' . $backupInfo['files'][$restoreStructureFiles],
                            $backupInfo['files'][$restoreStructureFiles]
                        );
                    } catch (FilesystemException | UnableToCopyFile $exception) {
                        $this->addResponse('Filed to copy file with ID: ' . $restoreStructureFiles, 1);

                        $this->basepackages->progress->resetProgress();

                        return false;
                    }
                }
            }
        } else {
            foreach ($backupInfo['dirs'] as $dirKey => $dir) {
                try {
                    $this->localContent->createDirectory($dir);
                } catch (FilesystemException | UnableToCreateDirectory $exception) {
                    $this->addResponse('Filed to create directory with ID: ' . $dirKey, 1);

                    $this->basepackages->progress->resetProgress();

                    return false;
                }
            }

            foreach ($backupInfo['files'] as $fileKey => $file) {
                try {
                    $this->localContent->copy(
                        'var/tmp/backups/' . $fileNameLocation . '/' . $file,
                        $file
                    );
                } catch (FilesystemException | UnableToCopyFile $exception) {
                    $this->addResponse('Filed to copy file with ID: ' . $fileKey, 1);

                    $this->basepackages->progress->resetProgress();

                    return false;
                }
            }
        }

        $this->addResponse('Backup restored. Refresh page!', 0);

        return true;
    }

    public function analyseBackinfoFile($id)
    {
        $fileInfo = $this->basepackages->storages->getFileInfo($id);

        if ($fileInfo) {
            if ($this->zip->open(base_path($fileInfo['uuid_location'] . $fileInfo['org_file_name']))) {
                $backupInfo = $this->zip->getFromName('backupInfo.json');

                if (!$backupInfo) {
                    $this->addResponse('Error reading backupInfo.json file. Please upload backup again.', 1);

                    $this->basepackages->progress->resetProgress();

                    return false;
                }

                try {
                    $this->backupInfo = Json::decode($backupInfo, true);
                } catch (\InvalidArgumentException $exception) {
                    $this->addResponse('Error reading contents of backupInfo.json file. Please check if file is in correct Json format.', 1);

                    $this->basepackages->progress->resetProgress();

                    return false;
                }

                $this->backupInfo['structure'] = [];

                foreach ($this->backupInfo['dirs'] as $dirKey => $dirValue) {
                    $this->addToStructure($dirValue, $dirKey);
                }
                foreach ($this->backupInfo['files'] as $fileKey => $fileValue) {
                    $this->addToStructure($fileValue, $fileKey, true);
                }

                return $this->backupInfo;
            } else {
                $this->addResponse('Error opening backup zip file. Please upload backup again.', 1);

                $this->basepackages->progress->resetProgress();
            }
        } else {
            $this->addResponse('Backup file not found on server. Please upload backup again.', 1);

            $this->basepackages->progress->resetProgress();
        }

        return false;
    }

    protected function getContent($localContent)
    {
        foreach ($localContent['dirs'] as $key => $dir) {
            if (isset($this->backupInfo['request']['html_compiled']) && $this->backupInfo['request']['html_compiled'] == 'true') {
                $this->backupInfo['dirs'] = array_merge($this->backupInfo['dirs'], ['fo' . $key => $dir]);
            } else {
                if (strpos($dir, 'Html_compiled') === false) {
                    $this->backupInfo['dirs'] = array_merge($this->backupInfo['dirs'], ['fo' . $key => $dir]);
                }
            }
        }

        foreach ($localContent['files'] as $key => $file) {
            if (isset($this->backupInfo['request']['html_compiled']) && $this->backupInfo['request']['html_compiled'] == 'true') {
                $this->backupInfo['files'] = array_merge($this->backupInfo['files'], ['fi' . $key => $file]);
            } else {
                if (strpos($file, 'Html_compiled') === false) {
                    if ($file === 'system/.keys' || $file === 'system/.dbkeys') {
                        if (isset($this->backupInfo['request']['keys']) && $this->backupInfo['request']['keys'] == 'true') {
                            $this->backupInfo['files'] = array_merge($this->backupInfo['files'], ['fi' . $key => $file]);
                        }
                    } else {
                        $this->backupInfo['files'] = array_merge($this->backupInfo['files'], ['fi' . $key => $file]);
                    }
                }
            }
        }
    }

    protected function addToStructure($path, $pathKey, $files = false)
    {
        $pathArr = explode('/', $path);

        if ($files) {
            $structure = ['id' => strtolower(Arr::last($pathArr)), 'title' => Arr::last($pathArr), 'data' => ['type' => 'file', 'pathId' => $pathKey]];
        } else {
            $structure = ['id' => strtolower(Arr::last($pathArr)), 'title' => Arr::last($pathArr), 'data' => ['type' => 'folder', 'pathId' => $pathKey]];
        }

        while ($key = array_pop($pathArr)) {
            if (count($pathArr) === 0) {
                $structure = [$key => ['id' => strtolower($key), 'title' => $key, 'childs' => $structure['childs']]];
            } else {
                $structure = ['childs' => [$key => $structure]];
            }
        }

        $this->backupInfo['structure'] = array_replace_recursive($this->backupInfo['structure'], $structure);
    }

    private function getDbKey($dbConfig = null)
    {
        try {
            $keys = $this->localContent->read('system/.dbkeys');

            if ($dbConfig) {
                return Json::decode($keys, true)[$dbConfig['dbname']];
            }

            return Json::decode($keys, true);
        } catch (\ErrorException | FilesystemException | UnableToReadFile $exception) {
            return false;
        }
    }

    public function checkPwStrength(string $pass)
    {
        $checkingTool = new \ZxcvbnPhp\Zxcvbn();

        $result = $checkingTool->passwordStrength($pass);

        if ($result && is_array($result) && isset($result['score'])) {
            $this->addResponse('Checking Password Strength Success', 0, ['result' => $result['score']]);

            return $result['score'];
        }

        $this->addResponse('Error Checking Password Strength', 1);

        return false;
    }

    public function generateNewPw()
    {
        $this->addResponse('Password Generate Successfully', 0, ['password' => $this->secTools->random->base62(12)]);
    }

    protected function registerBackupProgressMethods()
    {
        $this->backupProgressMethods =
            [
                [
                    'method'    => 'generateStructure',
                    'text'      => 'Generate backup file structure...'
                ],
                [
                    'method'    => 'performDbBackup',
                    'text'      => 'Performing Database Backup...'
                ],
                [
                    'method'    => 'zipBackupFiles',
                    'text'      => 'Generate backup zip file...'
                ],
                [
                    'method'    => 'finishBackup',
                    'text'      => 'Fnishing up...'
                ]
            ];

        $this->basepackages->progress->registerMethods($this->backupProgressMethods);
    }

    protected function registerRestoreProgressMethods()
    {
        $this->restoreProgressMethods =
            [
                [
                    'method'    => 'createNewDb',
                    'text'      => 'Creating new database...'
                ]
            ];

        $this->basepackages->progress->registerMethods($this->restoreProgressMethods);
    }
}