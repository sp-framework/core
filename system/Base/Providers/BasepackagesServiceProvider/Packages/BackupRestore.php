<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Carbon\Carbon;
use Ifsnop\Mysqldump\Mysqldump;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class BackupRestore extends BasePackage
{
    protected $zip;

    protected $backupInfo;

    protected $backupLocation = '.backups/';

    public function init()
    {
        $this->zip = new \ZipArchive;

        $this->backupInfo = [];

        if (!$this->localContent->fileExists($this->backupLocation)) {
            $this->localContent->createDirectory($this->backupLocation);
        }

        return $this;
    }

    public function backup(array $data)
    {
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

            return;
        }

        $now = Carbon::now();
        $this->backupInfo['takenAt'] = $now->format('Y-m-d H:i:s');
        $this->backupInfo['createdBy'] = $this->auth->account() ? $this->auth->account()['email'] : 'System';
        $this->backupInfo['backupName'] = 'backup-' . $now->getTimestamp() . '.zip';
        $this->backupInfo['request'] = $data;
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
                $this->addResponse('Password missing!', 1, []);

                return;
            }
        }

        $this->zip->open(base_path($this->backupLocation . $this->backupInfo['backupName']), $this->zip::CREATE);

        if (isset($this->backupInfo['request']['apps_dir']) && $this->backupInfo['request']['apps_dir'] == 'true') {
            $this->getContent($this->getInstalledFiles('apps/'));
        }
        if (isset($this->backupInfo['request']['systems_dir']) && $this->backupInfo['request']['systems_dir'] == 'true') {
            $this->getContent($this->getInstalledFiles('system/'));
        }
        if (isset($this->backupInfo['request']['public_dir']) && $this->backupInfo['request']['public_dir'] == 'true') {
            $this->getContent($this->getInstalledFiles('public/'));
        }
        if (isset($this->backupInfo['request']['private_dir']) && $this->backupInfo['request']['private_dir'] == 'true') {
            $this->getContent($this->getInstalledFiles('private/'));
        }
        if (isset($this->backupInfo['request']['var_dir']) && $this->backupInfo['request']['var_dir'] == 'true') {
            $this->getContent($this->getInstalledFiles('var/'));
        }
        if (isset($this->backupInfo['request']['external_dir']) && $this->backupInfo['request']['external_dir'] == 'true') {
            if (isset($this->backupInfo['request']['external_vendor_dir']) && $this->backupInfo['request']['external_vendor_dir'] == 'true') {
                $this->getContent($this->getInstalledFiles('external/'));
            } else {
                $this->getContent($this->getInstalledFiles('external/', false));
            }
        }
        if (isset($this->backupInfo['request']['old_backups']) && $this->backupInfo['request']['old_backups'] == 'true') {
            $this->getContent($this->getInstalledFiles('.backups/'));
        }

        foreach ($this->backupInfo['files'] as $file) {
            if (!$this->addToZip(base_path($file), $file)) {
                return false;
            }
        }

        if (isset($this->backupInfo['request']['database']) && $this->backupInfo['request']['database'] == 'true') {
            foreach ($this->core->core['settings']['dbs'] as $dbKey => $db) {
                try {
                    $db['password'] = $this->crypt->decryptBase64($db['password'], $this->getDbKey($db));

                    $dumper =
                        new Mysqldump(
                            'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
                            $db['username'],
                            $db['password'],
                            ['compress' => Mysqldump::GZIP, 'default-character-set' => Mysqldump::UTF8MB4]
                        );

                    $fileName = 'db' . $db['dbname'] . $now->getTimestamp() . '.gz';

                    $dumper->start(base_path('var/tmp/' . $fileName));

                    if (!$this->addToZip(base_path('var/tmp/' . $fileName), 'dbs/' . $fileName)) {
                        return false;
                    }

                    array_push($this->backupInfo['dbs'], [$db['dbname'] => 'dbs/' . $fileName]);
                } catch (\Exception $e) {
                    $this->addResponse('Backup Error: ' . $e->getMessage(), 1);

                    return false;
                }
            }
        }

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

                return false;
            }
        } else {
            if (!$this->zip->addFile($absolutePath, $relativePath)) {
                $name = $this->zip->getNameIndex($this->zip->numFiles - 1);

                $this->addResponse('Could not zip file: ' . $name, 1, []);

                return false;
            }
        }

        return true;
    }

    public function restore(array $data)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Please provide backup file id', 1, []);

            return false;
        }

        $backupInfo = $this->analyseBackinfoFile($data['id']);

        if (!$backupInfo) {
            return false;
        }

        if (isset($backupInfo['request']['password_protect']) && $backupInfo['request']['password_protect'] !== '') {
            if (!isset($data['password'])) {
                $this->addResponse('Please provide backup file password', 1, []);

                return false;
            }

            if (!$this->security->checkHash($data['password'], $backupInfo['request']['password_protect'])) {
                $this->addResponse('Backup password incorrect! Please provide correct password', 1, []);

                return false;
            }

            $this->zip->setPassword($data['password']);
        }

        $fileNameLocation = explode('.zip', $backupInfo['backupName'])[0];

        if (!$this->zip->extractTo(base_path('var/tmp/backups/' . $fileNameLocation))) {
            $this->addResponse('Error unzipping backup file. Please upload backup again.', 1);

            return false;
        }

        var_dump($backupInfo['request']['password_protect']);die();


        $backupInfo = $this->basepackages->storages->getFileInfo(null, $data['filename']);

        if ($backupInfo) {
            try {
                if (isset($data['dbname'])) {
                    if (checkCtype($data['dbname'], 'alnum', []) === false) {
                        $this->addResponse('Database cannot have special characters', 1, []);

                        return false;
                    }

                    $newDbName = $data['dbname'];
                } else {
                    $newDbName = str_replace('.gz', '', $backupInfo['org_file_name']);
                }

                $file = $this->basepackages->storages->getFile(['uuid' => $backupInfo['uuid'], 'headers' => false]);

                $file = gzdecode($file);

                try {
                    $this->localContent->write('var/tmp/' . $newDbName . '.sql' , $file);
                } catch (FilesystemException | UnableToWriteFile $exception) {
                    throw $exception;
                }

                if (isset($data['username'])) {
                    if (checkCtype($data['username'], 'alnum', []) === false) {
                        $this->addResponse('Username cannot have special characters', 1, []);

                        return false;
                    }

                    $newDbUserName = $data['username'];
                } else {
                    $newDbUserName = $newDbName . 'User';
                }

                $dbConfig = $this->getDb(true);

                if ($this->config->dev === false) {
                    $checkPwStrength = $this->checkPwStrength($data['password']);

                    if ($checkPwStrength !== false && $checkPwStrength < 4) {
                        $this->addResponse('Password strength is too low.' , 1);

                        return false;
                    }
                }

                $newDbUserPassword = $data['password'];

                $dbConfig['username'] = $data['root_username'];
                $dbConfig['password'] = $data['root_password'];
                $dbConfig['dbname'] = 'mysql';
                $this->db = new Mysql($dbConfig);

                $checkUser = $this->executeSQL("SELECT * FROM `user` WHERE `User` LIKE ?", [$newDbUserName]);

                if ($checkUser->numRows() === 0) {
                    $this->executeSQL("CREATE USER ?@'%' IDENTIFIED WITH mysql_native_password BY ?;", [$newDbUserName, $newDbUserPassword]);
                } else {
                    $dbConfig['username'] = $data['username'];
                    $dbConfig['password'] = $data['password'];
                    $dbConfig['dbname'] = 'mysql';

                    try {
                        $this->db = new Mysql($dbConfig);
                    } catch (\Exception $e) {
                        //1045 is password incorrect
                        //1044 user does not exist
                        if ($e->getCode() === 1045) {
                            $this->addResponse('Incorrect password for user ' . $newDbUserName, 1);

                            return false;
                        }
                    }
                }

                $dbConfig['username'] = $data['root_username'];
                $dbConfig['password'] = $data['root_password'];
                $dbConfig['dbname'] = 'mysql';
                $this->db = new Mysql($dbConfig);

                $this->executeSQL(
                    "CREATE DATABASE IF NOT EXISTS " . $newDbName . " CHARACTER SET " . $data['charset'] . " COLLATE " . $data['collation']
                );

                $this->executeSQL("GRANT ALL PRIVILEGES ON " . $newDbName . ".* TO ?@'%' WITH GRANT OPTION;", [$newDbUserName]);

                $dbConfig['dbname'] = $newDbName;

                $this->db = new Mysql($dbConfig);

                $allTables = $this->db->listTables($newDbName);

                if (count($allTables) > 0) {
                    if ($data['drop'] === 'false') {
                        $this->addResponse('Restore Error: Database not empty. Select Drop If Exists checkbox and try again.', 1, []);

                        return false;
                    } else {
                        foreach ($allTables as $tableKey => $tableValue) {
                            $this->db->dropTable($tableValue);
                        }
                    }
                }

                $dumper =
                    new Mysqldump(
                        'mysql:host=' . $dbConfig['host'] . ';dbname=' . $newDbName,
                        $newDbUserName,
                        $newDbUserPassword
                    );

                $dumper->restore(base_path('var/tmp/' . $newDbName . '.sql'));

                try {
                    $file = $this->localContent->delete('var/tmp/' . $newDbName . '.sql');
                } catch (FilesystemException | UnableToDeleteFile | \Exception $exception) {
                    throw $exception;
                }

                $newDb['active'] = false;
                $newDb['host'] = $dbConfig['host'];
                $newDb['dbname'] = $newDbName;
                $newDb['username'] = $newDbUserName;
                $newDb['password'] = $this->crypt->encryptBase64($newDbUserPassword, $this->createDbKey($newDbName));
                $newDb['port'] = $dbConfig['port'];
                $newDb['charset'] = $dbConfig['charset'];
                $newDb['collation'] = $dbConfig['collation'];

                $this->core['settings']['dbs'][$newDbName] = $newDb;

                $this->update($this->core);

                $this->addResponse($data['filename'] . ' restored!', 0, ['newDb' => $newDb]);

                return true;
            } catch (\Exception $e) {
                $this->addResponse('Restore Error: ' . $e->getMessage(), 1);

                return false;
            }
        }

        $this->addResponse('File ' . $data['filename'] . ' not found on system!', 1);

        return false;
    }

    public function analyseBackinfoFile($id)
    {
        $fileInfo = $this->basepackages->storages->getFileInfo($id);

        if ($fileInfo) {
            if ($this->zip->open(base_path($fileInfo['uuid_location'] . $fileInfo['org_file_name']))) {
                $backupInfo = $this->zip->getFromName('backupInfo.json');

                if (!$backupInfo) {
                    $this->addResponse('Error reading backupInfo.json file. Please upload backup again.', 1);

                    return false;
                }

                try {
                    $this->backupInfo = Json::decode($backupInfo, true);
                } catch (\InvalidArgumentException $exception) {
                    $this->addResponse('Error reading contents of backupInfo.json file. Please check if file is in correct Json format.', 1);

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
            }
        } else {
            $this->addResponse('Backup file not found on server. Please upload backup again.', 1);
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
}