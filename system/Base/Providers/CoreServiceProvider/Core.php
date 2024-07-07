<?php

namespace System\Base\Providers\CoreServiceProvider;

use Carbon\Carbon;
use Ifsnop\Mysqldump\Mysqldump;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Filter\Validation\Validator\Email;
use System\Base\BasePackage;
use System\Base\Providers\CoreServiceProvider\Model\ServiceProviderCore;

class Core extends BasePackage
{
	protected $modelToUse = ServiceProviderCore::class;

	public $core;

	protected $zip;

	protected $backupInfo;

	protected $backupFfLocation = '.backupsff/';

	protected $backupDbLocation = '.backupsdb/';

	protected $now;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		$this->core = $this->core[1];

		if (is_string($this->core['settings'])) {
			$this->core['settings'] = $this->helper->decode($this->core['settings'], true);
		}

		$this->checkKeys();

		$this->zip = new \ZipArchive;

		$this->backupInfo = [];

		if (!$this->localContent->fileExists($this->backupFfLocation)) {
			$this->localContent->createDirectory($this->backupFfLocation);
		}

		if (!$this->localContent->fileExists($this->backupDbLocation)) {
			$this->localContent->createDirectory($this->backupDbLocation);
		}

		if (!$this->localContent->fileExists('var/tmp/')) {
			$this->localContent->createDirectory('var/tmp/');
		}

		return $this;
	}

	public function getVersion()
	{
		return $this->core['version'];
	}

	public function backupDb($data)
	{
		if (!isset($data['db'])) {
			$this->addResponse('Please provide db name', 1, []);

			return false;
		}

		if (!isset($this->core['settings']['dbs'][$data['db']])) {
			$this->addResponse('Db does not exist.', 1, []);

			return false;
		}

		if (!isset($data['password_protect']) ||
			(isset($data['password_protect']) &&
			$data['password_protect'] === '')
		) {
			$this->addResponse('Protect password missing!', 1, []);

			return false;
		}

		$tokenkey = array_search($this->security->getRequestToken(), $data);
		if ($tokenkey) {
			unset($data[$tokenkey]);
		}

		$this->now = Carbon::now();
		$this->backupInfo['request'] = $data;
		$this->backupInfo['takenAt'] = $this->now->format('Y-m-d H:i:s');
		$this->backupInfo['createdBy'] = $this->access->auth->account() ? $this->access->auth->account()['email'] : 'System';
		$this->backupInfo['backupName'] = 'db' . $data['db'] . $this->now->getTimestamp() . '.zip';

		$this->zip->open(base_path($this->backupDbLocation . $this->backupInfo['backupName']), $this->zip::CREATE);

		$db = $this->core['settings']['dbs'][$this->backupInfo['request']['db']];
		$db['password'] = $this->crypt->decryptBase64($db['password'], $this->getDbKey($db));

		try {
			$dumper =
				new Mysqldump(
					'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
					$db['username'],
					$db['password'],
					['default-character-set' => Mysqldump::UTF8MB4]
				);

			$fileName = 'db' . $this->backupInfo['request']['db'] . $this->now->getTimestamp() . '.sql';

			$dumper->start(base_path('var/tmp/' . $fileName));

			if (!$this->addToZip(base_path('var/tmp/' . $fileName), $fileName)) {
				return false;
			}

			$db['password'] = $this->crypt->encryptBase64($db['password'], $this->getDbKey($db));

			$this->backupInfo[$db['dbname']] = $db;
			$this->backupInfo[$db['dbname']]['file'] = $fileName;
		} catch (\Exception | \throwable $e) {
			$this->addResponse('Backup Error: ' . $e->getMessage(), 1);

			return false;
		}

		try {
			$file = $this->localContent->read('var/tmp/' . $fileName);
		} catch (FilesystemException | UnableToReadFile | \Exception $exception) {
			throw $exception;
		}

		$this->zipBackupFiles();

		if ($this->basepackages->storages->storeFile(
				'private',
				'.backupsdb',
				null,
				$this->backupInfo['backupName'],
				filesize(base_path('.backupsdb/' . $this->backupInfo['backupName'])),
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

	public function restoreDb($data)
	{
		if (!isset($data['id'])) {
			$this->addResponse('Please provide backup file id', 1, []);

			return false;
		}

		$fileInfo = $this->basepackages->storages->getFileInfo($data['id']);

		if ($fileInfo) {
			if ($this->zip->open(base_path($fileInfo['uuid_location'] . $fileInfo['org_file_name']))) {
				$backupInfo = $this->zip->getFromName('backupInfo.json');

				if (!$backupInfo) {
					$this->addResponse('Error reading backupInfo.json file. Please upload backup again.', 1);

					return false;
				}

				try {
					$this->backupInfo = $this->helper->decode($backupInfo, true);
				} catch (\InvalidArgumentException $exception) {
					$this->addResponse('Error reading contents of backupInfo.json file. Please check if file is in correct Json format.', 1);

					return false;
				}

				if (isset($this->backupInfo['request']['password_protect']) && $this->backupInfo['request']['password_protect'] !== '') {
					if (!isset($data['password_protect_restore'])) {
						$this->addResponse('Please provide backup file password', 1, []);

						return false;
					}

					if (!$this->security->checkHash($data['password_protect_restore'], $this->backupInfo['request']['password_protect'])) {
						$this->addResponse('Backup password incorrect! Please provide correct password', 1, []);

						return false;
					}

					$this->zip->setPassword($data['password_protect_restore']);
				}

				$fileNameLocation = explode('.zip', $this->backupInfo['backupName'])[0];

				if (!$this->zip->extractTo(base_path('var/tmp/' . $fileNameLocation))) {
					$this->addResponse('Error unzipping backup file. Please upload backup again.', 1);

					return false;
				}

				try {
					if (isset($data['dbname'])) {
						if (checkCtype($data['dbname'], 'alnum', []) === false) {
							$this->addResponse('Database cannot have special characters', 1, []);

							return false;
						}

						$newDbName = $data['dbname'];
					} else {
						$newDbName = str_replace('.zip', '', $fileInfo['org_file_name']);
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
						$checkPwStrength = $this->basepackages->utils->checkPwStrength($data['password']);

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

					$dumper->restore(base_path('var/tmp/' . $fileNameLocation . '/' . $fileNameLocation . '.sql'));

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

					$this->addResponse($this->backupInfo['backupName'] . ' restored!', 0, ['newDb' => $newDb]);

					return true;
				} catch (\Exception $e) {
					$this->addResponse('Restore Error: ' . $e->getMessage(), 1);

					return false;
				}
			} else {
				$this->addResponse('Error opening backup zip file. Please upload backup again.', 1);
			}
		} else {
			$this->addResponse('File ' . $data['filename'] . ' not found on system!', 1);
		}

		return false;
	}

	public function updateDb(array $data)
	{
		if (!isset($data['db']['dbname'])) {
			$this->addResponse('Please provide database name', 1, []);

			return false;
		}

		$dbConfig = $this->getDb(false, $data['db']['dbname']);

		if ($dbConfig['active'] == 'true') {
			$this->addResponse('Cannot update active database', 1, []);

			return false;
		}

		$oldPassword = $dbConfig['password'];
		$changePassword = false;

		$dbConfig = array_merge($dbConfig, $data['db']);
		if (isset($data['db']['password']) && $data['db']['password'] === '') {
			$dbConfig['password'] = $oldPassword;
			$dbConfig['password'] = $this->crypt->decryptBase64($dbConfig['password'], $this->getDbKey($dbConfig));
		} else {
			$changePassword = true;
		}

		// Try Connecting to DB with new information
		try {
			$this->db = new Mysql($dbConfig);

			$checkCharsetCollation =
				$this->executeSQL(
					"SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?",
					[$dbConfig['dbname']]
				);

			if ($checkCharsetCollation->numRows() > 0) {
				$dbCharsetOnServer = $checkCharsetCollation->fetchArray();

				if (isset($dbCharsetOnServer['DEFAULT_CHARACTER_SET_NAME']) && $dbCharsetOnServer['DEFAULT_CHARACTER_SET_NAME'] !== $data['db']['charset']) {
					throw new \Exception(
						'Database Charset is set to ' . $dbCharsetOnServer['DEFAULT_CHARACTER_SET_NAME'] . ' and provided is ' . $data['db']['charset']
					);
				}
				if (isset($dbCharsetOnServer['DEFAULT_COLLATION_NAME']) && $dbCharsetOnServer['DEFAULT_COLLATION_NAME'] !== $data['db']['collation']) {
					throw new \Exception(
						'Database Charset is set to ' . $dbCharsetOnServer['DEFAULT_COLLATION_NAME'] . ' and provided is ' . $data['db']['collation']
					);
				}

			}
		} catch (\PDOException | \Exception $e) {
			$this->addResponse('Update Error: ' . $e->getMessage(), 1);

			return false;
		}

		$dbConfig['password'] = $this->crypt->encryptBase64($dbConfig['password'], $this->getDbKey($dbConfig));

		if (isset($data['changeActive']) && $data['changeActive'] == true) {
			foreach ($this->core['settings']['dbs'] as $dbKey => &$db) {
				$db['active'] = false;//Make all active false.
			}

			$this->writeBaseConfig($dbConfig);
		}

		$this->core['settings']['dbs'][$dbConfig['dbname']] = $dbConfig;

		$this->update($this->core);

		$this->addResponse('Database updated', 0, []);
	}

	public function removeDb(array $data)
	{
		if (!isset($data['db'])) {
			$this->addResponse('Please provide database name', 1, []);

			return false;
		}

		$dbConfig = $this->getDb(false, $data['db']);

		if ($dbConfig['active'] == 'true') {
			$this->addResponse('Cannot remove active database', 1, []);

			return false;
		}

		if (!isset($data['username']) || !isset($data['password'])) {
			$this->addResponse('Please provide username and password that has delete rights on database.', 1, []);

			return false;
		}

		$dbConfig['username'] = $data['username'];
		$dbConfig['password'] = $data['password'];

		try {
			$this->db = new Mysql($dbConfig);

			$this->executeSQL("DROP DATABASE IF EXISTS `" . $dbConfig['dbname'] . "`;");

			if (isset($data['removeUser']) && $data['removeUser'] == 'true' && isset($data['userToRemove'])) {
				$this->executeSQL("DROP USER IF EXISTS `" . $data['userToRemove'] . "`;");
			}

			$this->removeDbKey($dbConfig['dbname']);
		} catch (\PDOException | \Exception$e) {
			if ($e->getCode() !== 1049) {
				$this->addResponse('Remove Error: ' . $e->getMessage(), 1);

				return false;
			}
		}

		unset($this->core['settings']['dbs'][$dbConfig['dbname']]);

		$this->update($this->core);

		$this->addResponse('Database removed', 0, []);
	}

	public function maintainFf($data)
	{
		if (!isset($data['task'])) {
			$this->addResponse('Task not set', 1);

			return false;
		}

		if ($this->config->databasetype !== 'db') {
			if (!isset($data['selectedStores'])) {
				$this->addResponse('No Stores Selected', 1);

				return false;
			}
		}

		if ($data['task'] === 'clear-cache') {
			if ($this->config->databasetype !== 'db') {
				if (is_array($data['selectedStores']) && count($data['selectedStores']) > 0) {
					foreach ($data['selectedStores'] as $store) {
						$storeToMaintain = $this->ff->store($store);

						$storeToMaintain->createQueryBuilder()->getQuery()->getCache()->deleteAll();
					}

					$this->addResponse('Cache Cleared');

					return true;
				}
			}
		} else if ($data['task'] === 're-index') {
			if ($this->config->databasetype !== 'db') {
				if (is_array($data['selectedStores']) && count($data['selectedStores']) > 0) {
					foreach ($data['selectedStores'] as $store) {
						$storeToMaintain = $this->ff->store($store);

						try {
							$storeToMaintain->reIndexStore();
						} catch (\Exception $e) {
							$this->addResponse($e->getMessage(), 1);

							return false;
						}
					}

					$this->addResponse('Stores Re-indexed');

					return true;
				}
			} else {
				$this->addResponse('Database type is RDBMS, cannot perform Flatfile tasks', 1);

				return false;
			}
		} else if ($data['task'] === 're-sync') {
			if ($this->config->databasetype !== 'db') {
				if (is_array($data['selectedStores']) && count($data['selectedStores']) > 0) {
					foreach ($data['selectedStores'] as $store) {
						try {
							$this->ff->store($store);

							$this->ff->reSync();
						} catch (\Exception $e) {
							$this->addResponse($e->getMessage(), 1);

							return false;
						}
					}

					$this->addResponse('Stores Re-synced');

					return true;
				}
			} else {
				$this->addResponse('Database type is RDBMS, cannot perform Flatfile tasks', 1);

				return false;
			}
		}
	}

	public function backupFf($data)
	{
		if (!isset($data['ff'])) {
			$this->addResponse('Please provide flat file name', 1, []);

			return false;
		}

		if (!isset($this->core['settings']['ffs'][$data['ff']])) {
			$this->addResponse('Flat File does not exist.', 1, []);

			return false;
		}

		if (!isset($data['password_protect']) ||
			(isset($data['password_protect']) &&
			$data['password_protect'] === '')
		) {
			$this->addResponse('Protect password missing!', 1, []);

			return false;
		}

		$tokenkey = array_search($this->security->getRequestToken(), $data);
		if ($tokenkey) {
			unset($data[$tokenkey]);
		}

		$this->now = Carbon::now();
		$this->backupInfo['request'] = $data;
		$this->backupInfo['takenAt'] = $this->now->format('Y-m-d H:i:s');
		$this->backupInfo['createdBy'] = $this->access->auth->account() ? $this->access->auth->account()['email'] : 'System';
		$this->backupInfo['backupName'] = 'ff' . $data['ff'] . $this->now->getTimestamp() . '.zip';

		$this->zip->open(base_path($this->backupFfLocation . $this->backupInfo['backupName']), $this->zip::CREATE);

		try {
			$files = $this->localContent->listContents('.ff/' . $data['ff'], true);

			foreach ($files as $file) {
				if ($file instanceof \League\Flysystem\FileAttributes) {
					if (!$this->addToZip(base_path($file->path()), $file->path())) {
						return false;
					}
				}
			}
		} catch (FilesystemException | \Exception $exception) {
			throw $exception;
		}

		$this->zipBackupFiles();

		if ($this->basepackages->storages->storeFile(
				'private',
				'.backupsff',
				null,
				$this->backupInfo['backupName'],
				filesize(base_path('.backupsff/' . $this->backupInfo['backupName'])),
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

	public function restoreFf($data)
	{
		if (!isset($data['id'])) {
			$this->addResponse('Please provide backup file id', 1, []);

			return false;
		}

		$fileInfo = $this->basepackages->storages->getFileInfo($data['id']);

		if ($fileInfo) {
			if ($this->zip->open(base_path($fileInfo['uuid_location'] . $fileInfo['org_file_name']))) {
				$backupInfo = $this->zip->getFromName('backupInfo.json');

				if (!$backupInfo) {
					$this->addResponse('Error reading backupInfo.json file. Please upload backup again.', 1);

					return false;
				}

				try {
					$this->backupInfo = $this->helper->decode($backupInfo, true);
				} catch (\InvalidArgumentException $exception) {
					$this->addResponse('Error reading contents of backupInfo.json file. Please check if file is in correct Json format.', 1);

					return false;
				}

				if (isset($this->backupInfo['request']['password_protect']) && $this->backupInfo['request']['password_protect'] !== '') {
					if (!isset($data['password_protect_restore'])) {
						$this->addResponse('Please provide backup file password', 1, []);

						return false;
					}

					if (!$this->security->checkHash($data['password_protect_restore'], $this->backupInfo['request']['password_protect'])) {
						$this->addResponse('Backup password incorrect! Please provide correct password', 1, []);

						return false;
					}

					$this->zip->setPassword($data['password_protect_restore']);
				}

				$fileNameLocation = explode('.zip', $this->backupInfo['backupName'])[0];

				if (!$this->zip->extractTo(base_path('var/tmp/' . $fileNameLocation))) {
					$this->addResponse('Error unzipping backup file. Please upload backup again.', 1);

					return false;
				}

				try {
					$backupInfo = $this->localContent->read('var/tmp/' . $fileNameLocation . '/backupInfo.json');

					$backupInfo = $this->helper->decode($backupInfo, true);

					$ffFolderName = $backupInfo['request']['ff'];
				} catch (FilesystemException | UnableToReadFile | \Exception $exception) {
					throw $exception;
				}

				try {
					$this->localContent->deleteDirectory('var/tmp/' . $fileNameLocation . '/.ff/' . $fileNameLocation);
					$this->localContent->deleteDirectory('.ff/' . $fileNameLocation);

					rename(base_path('var/tmp/' . $fileNameLocation . '/.ff/' . $ffFolderName), base_path('var/tmp/' . $fileNameLocation . '/.ff/' . $fileNameLocation));
					rename(base_path('var/tmp/' . $fileNameLocation . '/.ff/' . $fileNameLocation), base_path('.ff/' . $fileNameLocation));
				} catch (\Exception $e) {
					$this->addResponse('Restore Error: ' . $e->getMessage(), 1);

					return false;
				}

				$newFf['active'] = false;
				$newFf['ffname'] = $fileNameLocation;
				$newFf['databaseDir'] = $fileNameLocation . '/';

				$this->core['settings']['ffs'][$fileNameLocation] = $newFf;

				$this->update($this->core);

				$this->addResponse($this->backupInfo['backupName'] . ' restored!', 0, ['newFf' => $newFf]);
			} else {
				$this->addResponse('Error opening backup zip file. Please upload backup again.', 1);
			}
		} else {
			$this->addResponse('File ' . $data['filename'] . ' not found on system!', 1);
		}

		return false;
	}

	public function updateFf(array $data)
	{
		if (!isset($data['db']['dbname'])) {
			$this->addResponse('Please provide database name', 1, []);

			return false;
		}

		$dbConfig = $this->getDb(false, $data['db']['dbname']);

		if ($dbConfig['active'] == 'true') {
			$this->addResponse('Cannot update active database', 1, []);

			return false;
		}

		$oldPassword = $dbConfig['password'];
		$changePassword = false;

		$dbConfig = array_merge($dbConfig, $data['db']);
		if (isset($data['db']['password']) && $data['db']['password'] === '') {
			$dbConfig['password'] = $oldPassword;
			$dbConfig['password'] = $this->crypt->decryptBase64($dbConfig['password'], $this->getDbKey($dbConfig));
		} else {
			$changePassword = true;
		}

		// Try Connecting to DB with new information
		try {
			$this->db = new Mysql($dbConfig);

			$checkCharsetCollation =
				$this->executeSQL(
					"SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?",
					[$dbConfig['dbname']]
				);

			if ($checkCharsetCollation->numRows() > 0) {
				$dbCharsetOnServer = $checkCharsetCollation->fetchArray();

				if (isset($dbCharsetOnServer['DEFAULT_CHARACTER_SET_NAME']) && $dbCharsetOnServer['DEFAULT_CHARACTER_SET_NAME'] !== $data['db']['charset']) {
					throw new \Exception(
						'Database Charset is set to ' . $dbCharsetOnServer['DEFAULT_CHARACTER_SET_NAME'] . ' and provided is ' . $data['db']['charset']
					);
				}
				if (isset($dbCharsetOnServer['DEFAULT_COLLATION_NAME']) && $dbCharsetOnServer['DEFAULT_COLLATION_NAME'] !== $data['db']['collation']) {
					throw new \Exception(
						'Database Charset is set to ' . $dbCharsetOnServer['DEFAULT_COLLATION_NAME'] . ' and provided is ' . $data['db']['collation']
					);
				}

			}
		} catch (\PDOException | \Exception $e) {
			$this->addResponse('Update Error: ' . $e->getMessage(), 1);

			return false;
		}

		$dbConfig['password'] = $this->crypt->encryptBase64($dbConfig['password'], $this->getDbKey($dbConfig));

		if (isset($data['changeActive']) && $data['changeActive'] == true) {
			foreach ($this->core['settings']['dbs'] as $dbKey => &$db) {
				$db['active'] = false;//Make all active false.
			}

			$this->writeBaseConfig($dbConfig);
		}

		$this->core['settings']['dbs'][$dbConfig['dbname']] = $dbConfig;

		$this->update($this->core);

		$this->addResponse('Database updated', 0, []);
	}

	public function removeFf(array $data)
	{
		if (!isset($data['db'])) {
			$this->addResponse('Please provide database name', 1, []);

			return false;
		}

		$dbConfig = $this->getDb(false, $data['db']);

		if ($dbConfig['active'] == 'true') {
			$this->addResponse('Cannot remove active database', 1, []);

			return false;
		}

		if (!isset($data['username']) || !isset($data['password'])) {
			$this->addResponse('Please provide username and password that has delete rights on database.', 1, []);

			return false;
		}

		$dbConfig['username'] = $data['username'];
		$dbConfig['password'] = $data['password'];

		try {
			$this->db = new Mysql($dbConfig);

			$this->executeSQL("DROP DATABASE IF EXISTS `" . $dbConfig['dbname'] . "`;");

			if (isset($data['removeUser']) && $data['removeUser'] == 'true' && isset($data['userToRemove'])) {
				$this->executeSQL("DROP USER IF EXISTS `" . $data['userToRemove'] . "`;");
			}

			$this->removeDbKey($dbConfig['dbname']);
		} catch (\PDOException | \Exception$e) {
			if ($e->getCode() !== 1049) {
				$this->addResponse('Remove Error: ' . $e->getMessage(), 1);

				return false;
			}
		}

		unset($this->core['settings']['dbs'][$dbConfig['dbname']]);

		$this->update($this->core);

		$this->addResponse('Database removed', 0, []);
	}

	protected function zipBackupFiles()
	{
		if (isset($this->backupInfo['request']['password_protect']) && $this->backupInfo['request']['password_protect'] !== '') {
			$this->backupInfo['request']['password_protect'] =
				$this->secTools->hashPassword($this->backupInfo['request']['password_protect'], 4);
		}

		try {
			$this->localContent->write('var/tmp/backupInfo.json' , $this->helper->encode($this->backupInfo));
		} catch (FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}

		//Dont Encrypt info file as we need to know if encryption is applied or not during restore. We can encrypt the content in case we want to hide something (like we are encrypting the password_protect password)
		if (!$this->addToZip(base_path('var/tmp/backupInfo.json'), 'backupInfo.json', false)) {
			return false;
		}

		$this->zip::close();

		return true;
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

				$this->zip::close();

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

	public function updateCore(array $data)
	{
		if (isset($data['debug'])) {
			$this->core['settings']['debug'] = $data['debug'];
		}
		if (isset($data['auto_off_debug'])) {
			$this->core['settings']['auto_off_debug'] = $data['auto_off_debug'];
		}

		if (isset($data['databasetype'])) {
			$this->core['settings']['databasetype'] = $data['databasetype'];
		}

		if (isset($data['cache'])) {
			$this->core['settings']['cache']['enabled'] = $data['cache'];
		}
		if (isset($data['cache_timeout'])) {
			$this->core['settings']['cache']['timeout'] = $data['cache_timeout'];
		}
		if (isset($data['cache_service'])) {
			$this->core['settings']['cache']['service'] = $data['cache_service'];
		}

		if (isset($data['single_sign_on'])) {
			$this->core['settings']['security']['sso'] = $data['single_sign_on'];
		}
		if (isset($data['passwordWorkFactor'])) {
			$this->core['settings']['security']['passwordWorkFactor'] = $data['passwordWorkFactor'];
		}
		if (isset($data['cookiesWorkFactor'])) {
			$this->core['settings']['security']['cookiesWorkFactor'] = $data['cookiesWorkFactor'];
		}
		if (isset($data['password_policy'])) {
			$this->core['settings']['security']['passwordPolicy'] = $data['password_policy'];
		}
		if (isset($data['password_policy_force_relogin_after_pwreset'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyForceReloginAfterPwreset'] = $data['password_policy_force_relogin_after_pwreset'];
		}
		if (isset($data['password_policy_force_pwreset_after'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyForcePwresetAfter'] = $data['password_policy_force_pwreset_after'];
		}
		if (isset($data['password_policy_block_previous_passwords'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords'] = $data['password_policy_block_previous_passwords'];
		}
		if (isset($data['password_policy_forgotten_password_timeout'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyForgottenPasswordTimeout'] = $data['password_policy_forgotten_password_timeout'];
		}
		if (isset($data['password_check_hibp'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordCheckHibp'] = $data['password_check_hibp'];
		}
		if (isset($data['password_policy_complexity'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyComplexity'] = $data['password_policy_complexity'];
		}
		if (isset($data['password_policy_simple_acceptable_level'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicySimpleAcceptableLevel'] = $data['password_policy_simple_acceptable_level'];
		}
		if (isset($data['password_policy_length_min'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyLengthMin'] = $data['password_policy_length_min'];
		}
		if (isset($data['password_policy_length_max'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyLengthMax'] = $data['password_policy_length_max'];
		}
		if (isset($data['password_policy_uppercase'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercase'] = $data['password_policy_uppercase'];
		}
		if (isset($data['password_policy_uppercase_min_count'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseMinCount'] = $data['password_policy_uppercase_min_count'];
		}
		if (isset($data['password_policy_uppercase_max_count'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseMaxCount'] = $data['password_policy_uppercase_max_count'];
		}
		if (isset($data['password_policy_uppercase_include'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseInclude'] = $data['password_policy_uppercase_include'];
		}
		if (isset($data['password_policy_lowercase'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercase'] = $data['password_policy_lowercase'];
		}
		if (isset($data['password_policy_lowercase_min_count'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseMinCount'] = $data['password_policy_lowercase_min_count'];
		}
		if (isset($data['password_policy_lowercase_max_count'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseMaxCount'] = $data['password_policy_lowercase_max_count'];
		}
		if (isset($data['password_policy_lowercase_include'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseInclude'] = $data['password_policy_lowercase_include'];
		}
		if (isset($data['password_policy_numbers'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbers'] = $data['password_policy_numbers'];
		}
		if (isset($data['password_policy_numbers_min_count'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersMinCount'] = $data['password_policy_numbers_min_count'];
		}
		if (isset($data['password_policy_numbers_max_count'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersMaxCount'] = $data['password_policy_numbers_max_count'];
		}
		if (isset($data['password_policy_numbers_include'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersInclude'] = $data['password_policy_numbers_include'];
		}
		if (isset($data['password_policy_symbols'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbols'] = $data['password_policy_symbols'];
		}
		if (isset($data['password_policy_symbols_min_count'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsMinCount'] = $data['password_policy_symbols_min_count'];
		}
		if (isset($data['password_policy_symbols_max_count'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsMaxCount'] = $data['password_policy_symbols_max_count'];
		}
		if (isset($data['password_policy_symbols_include'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsInclude'] = $data['password_policy_symbols_include'];
		}
		if (isset($data['password_policy_avoid_similar'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyAvoidSimilar'] = $data['password_policy_avoid_similar'];
		}
		if (isset($data['password_policy_avoid_similar_characters'])) {
			$this->core['settings']['security']['passwordPolicySettings']['passwordPolicyAvoidSimilarCharacters'] = $data['password_policy_avoid_similar_characters'];
		}

		if (isset($data['twofa'])) {
			$this->core['settings']['security']['twofa'] = $data['twofa'];
		}
		if (isset($data['twofa_pwreset_need_2fa'])) {
			$this->core['settings']['security']['twofaSettings']['twofaPwresetNeed2fa'] = $data['twofa_pwreset_need_2fa'];
		}
		if (isset($data['twofa_using'])) {
			if (is_string($data['twofa_using'])) {
				$data['twofa_using'] = $this->helper->decode($data['twofa_using'], true);
			}

			if (isset($data['twofa_using']['data'])) {
				$data['twofa_using'] = $this->helper->encode($data['twofa_using']['data']);
			} else {
				$data['twofa_using'] = $this->helper->encode($data['twofa_using']);
			}

			$this->core['settings']['security']['twofaSettings']['twofaUsing'] = $data['twofa_using'];
		}

		if (isset($data['twofa_email_code_timeout']) && $data['twofa_email_code_timeout'] < 60) {
			$data['twofa_email_code_timeout'] = 60;
		}
		if (isset($data['twofa_email_code_timeout']) && $data['twofa_email_code_timeout'] > 3600) {
			$data['twofa_email_code_timeout'] = 3600;
		}
		if (isset($data['twofa_email_code_timeout'])) {
			$this->core['settings']['security']['twofaSettings']['twofaEmailCodeTimeout'] = $data['twofa_email_code_timeout'];
		}
		if (isset($data['twofa_email_code_length']) && $data['twofa_email_code_length'] < 4) {
			$data['twofa_email_code_length'] = 4;
		}
		if (isset($data['twofa_email_code_length']) && $data['twofa_email_code_length'] > 12) {
			$data['twofa_email_code_length'] = 12;
		}
		if (isset($data['twofa_email_code_length'])) {
			$this->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'] = $data['twofa_email_code_length'];
		}

		if (isset($data['twofa_otp'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtp'] = $data['twofa_otp'];
		}
		if (isset($data['twofa_otp_logo'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpLogo'] = $data['twofa_otp_logo'];
		}
		if (isset($data['twofa_otp_secret_size']) && $data['twofa_otp_secret_size'] < 8) {
			$data['twofa_otp_secret_size'] = 8;
		}
		if (isset($data['twofa_otp_secret_size']) && $data['twofa_otp_secret_size'] > 64) {
			$data['twofa_otp_secret_size'] = 64;
		}
		if (isset($data['twofa_otp_secret_size'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpSecretSize'] = $data['twofa_otp_secret_size'];
		}
		if (isset($data['twofa_otp_totp_timeout']) && $data['twofa_otp_totp_timeout'] < 30) {
			$data['twofa_otp_totp_timeout'] = 30;
		}
		if (isset($data['twofa_otp_totp_timeout']) && $data['twofa_otp_totp_timeout'] > 300) {
			$data['twofa_otp_totp_timeout'] = 300;
		}
		if (isset($data['twofa_otp_totp_timeout'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout'] = $data['twofa_otp_totp_timeout'];

			if (isset($data['twofa_otp_totp_window']) && $data['twofa_otp_totp_window'] > $data['twofa_otp_totp_timeout']) {
				$data['twofa_otp_totp_window'] = $data['twofa_otp_totp_timeout'] - 1;
			}
		}
		if (isset($data['twofa_otp_totp_window'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpTotpWindow'] = $data['twofa_otp_totp_window'];
		}
		if (isset($data['twofa_otp_hotp_counter']) && $data['twofa_otp_hotp_counter'] < 0) {
			$data['twofa_otp_hotp_counter'] = 0;
		}
		if (isset($data['twofa_otp_hotp_counter'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpHotpCounter'] = $data['twofa_otp_hotp_counter'];
		}
		if (isset($data['twofa_otp_hotp_window'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpHotpWindow'] = $data['twofa_otp_hotp_window'];
		}
		if (isset($data['twofa_otp_label'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpLabel'] = $data['twofa_otp_label'];
		}
		if (isset($data['twofa_otp_issuer'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpIssuer'] = $data['twofa_otp_issuer'];
		}
		if (!isset($data['twofa_otp_algorithm']) ||
			(isset($data['twofa_otp_algorithm']) && !isset($this->get2faAlgorithms()[$data['twofa_otp_algorithm']]))
		) {
			$data['twofa_otp_algorithm'] = 'sha1';
		}
		if (isset($data['twofa_otp_algorithm'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpAlgorithm'] = $data['twofa_otp_algorithm'];
		}
		if (isset($data['twofa_otp_digits_length'])) {
			$this->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength'] = $data['twofa_otp_digits_length'];
		}

		if (isset($data['agent_email_code_timeout']) && $data['agent_email_code_timeout'] < 60) {
			$data['agent_email_code_timeout'] = 60;
		}
		if (isset($data['agent_email_code_timeout']) && $data['agent_email_code_timeout'] > 3600) {
			$data['agent_email_code_timeout'] = 3600;
		}
		if (isset($data['agent_email_code_timeout'])) {
			$this->core['settings']['security']['agentEmailCodeTimeout'] = $data['agent_email_code_timeout'];
		}
		if (isset($data['agent_email_code_length']) && $data['agent_email_code_length'] < 4) {
			$data['agent_email_code_length'] = 4;
		}
		if (isset($data['agent_email_code_length']) && $data['agent_email_code_length'] > 12) {
			$data['agent_email_code_length'] = 12;
		}
		if (isset($data['agent_email_code_length'])) {
			$this->core['settings']['security']['agentEmailCodeLength'] = $data['agent_email_code_length'];
		}

		if (isset($data['logs'])) {
			$this->core['settings']['logs']['enabled'] = $data['logs'];
		}
		if (isset($data['logs_level'])) {
			$this->core['settings']['logs']['level'] = $data['logs_level'];
		}
		if (isset($data['logs_exceptions'])) {
			$this->core['settings']['logs']['exceptions'] = $data['logs_exceptions'];
		}
		if (isset($data['emergency_logs_email'])) {
			$this->core['settings']['logs']['emergencyLogsEmail'] = $data['emergency_logs_email'];
		}
		if (isset($data['emergency_logs_email_addresses'])) {
			if ($data['emergency_logs_email_addresses'] !== '') {
				$data['emergency_logs_email_addresses'] = explode(',', $data['emergency_logs_email_addresses']);

				if (count($data['emergency_logs_email_addresses']) > 0) {
					foreach ($data['emergency_logs_email_addresses'] as &$address) {
						$address = trim($address);

						$validateEmail = $this->validateEmail(['email' => $address]);

						if ($validateEmail !== true) {
							$this->addResponse($validateEmail, 1);

							return false;
						}
					}
				}

			}

			$this->core['settings']['logs']['emergencyLogsEmailAddresses'] = $data['emergency_logs_email_addresses'];
		}

		if (isset($data['dbs']) && $data['dbs'] !== '') {
			$data['dbs'] = $this->helper->decode($data['dbs'], true);
			$this->core['settings']['dbs'] = $data['dbs'];
		}
		if (isset($data['ffs']) && $data['ffs'] !== '') {
			$data['ffs'] = $this->helper->decode($data['ffs'], true);
			$this->core['settings']['ffs'] = $data['ffs'];
		}

		if (isset($data['websocket_protocol'])) {
			$this->core['settings']['websocket']['protocol'] = $data['websocket_protocol'];
		}
		if (isset($data['websocket_host'])) {
			$this->core['settings']['websocket']['host'] = $data['websocket_host'];
		}
		if (isset($data['websocket_port'])) {
			$this->core['settings']['websocket']['port'] = $data['websocket_port'];
		}
		if (isset($data['timeout_cookies'])) {
			$this->core['settings']['timeout']['cookies'] = $data['timeout_cookies'];
		}

		$this->update($this->core);

		$this->writeBaseConfig();
	}

	protected function checkKeys()
	{
		try {
			$fileExists = $this->localContent->fileExists('system/.keys');
		} catch (FilesystemException | UnableToRetrieveMetadata $exception) {
			throw $exception;
		}

		if (!$fileExists) {
			$this->createKeys();

			$this->getKeys();
		} else {
			$this->getKeys();
		}
	}

	public function refreshKeys()
	{
		$this->createKeys();
	}

	protected function createKeys()
	{
		$keys['sigKey'] = $this->random->base58();
		$keys['sigText'] = $this->random->base58(32);
		$keys['cookiesSig'] = $this->crypt->encryptBase64($keys['sigKey'], $keys['sigText']);

		try {
			$this->localContent->write('system/.keys', $this->helper->encode($keys), ['visibility' => 'private']);
		} catch (FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}
	}

	protected function getKeys()
	{
		try {
			$keysFile = $this->localContent->read('system/.keys');
		} catch (FilesystemException | UnableToReadFile | \Exception $exception) {
			throw $exception;
		}

		$keys = $this->helper->decode($keysFile, true);

		$this->core['settings']['sigKey'] = $keys['sigKey'];
		$this->core['settings']['sigText'] = $keys['sigText'];
		$this->core['settings']['cookiesSig'] = $keys['cookiesSig'];
	}

	public function reset()
	{
		if ($this->core['settings']['dev'] == 'true') {
			$this->writeResetConfig();

			$this->addResponse('Reset Done', 0);

			return true;
		}
	}

	private function writeBaseConfig($dbConfig = null, $ffConfig = null)
	{
		if ($this->core['settings']['databasetype'] === 'hybrid') {
			if (!$dbConfig) {
				$dbConfig = $this->getDb();//Get the active config
			}

			if (!$ffConfig) {
				$ffConfig = $this->getFf();//Get the active ff config
			}
		} else if ($this->core['settings']['databasetype'] === 'ff' && !$ffConfig) {
			$ffConfig = $this->getFf();//Get the active ff config
		} else if ($this->core['settings']['databasetype'] === 'db' && !$dbConfig) {
			$dbConfig = $this->getDb();//Get the active config
		}

		$this->core['settings']['setup'] = $this->core['settings']['setup'] == 'true' ? 'true' : 'false';
		$this->core['settings']['dev'] = $this->core['settings']['dev'] == 'true' ? 'true' : 'false';
		$this->core['settings']['debug'] = $this->core['settings']['debug'] == 'true' ? 'true' : 'false';
		$this->core['settings']['cache']['enabled'] = $this->core['settings']['cache']['enabled'] == 'true' ? 'true' : 'false';
		$this->core['settings']['security']['sso'] = $this->core['settings']['security']['sso'] == 'true' ? 'true' : 'false';
		$this->core['settings']['logs']['emergencyLogsEmailAddresses'] =
			is_array($this->core['settings']['logs']['emergencyLogsEmailAddresses']) ?
			implode(',', $this->core['settings']['logs']['emergencyLogsEmailAddresses']) :
			$this->core['settings']['logs']['emergencyLogsEmailAddresses'];

		$this->core['settings']['auto_off_debug'] =
			$this->core['settings']['auto_off_debug'] == '' ? 0 : $this->core['settings']['auto_off_debug'];
		$this->core['settings']['cache']['timeout'] =
			$this->core['settings']['cache']['timeout'] == '' ? 60 : $this->core['settings']['cache']['timeout'];
		$this->core['settings']['websocket']['port'] =
			$this->core['settings']['websocket']['port'] == '' ? 5555 : $this->core['settings']['websocket']['port'];

		$baseFileContent =
'<?php

return
	[
		"setup" 			=> ' . $this->core['settings']['setup'] .',
		"dev"    			=> ' . $this->core['settings']['dev'] . ', //true - Development false - Production
		"debug"				=> ' . $this->core['settings']['debug'] . ',
		"auto_off_debug"	=> ' . $this->core['settings']['auto_off_debug'] . ',
		"databasetype"	    => "' . $this->core['settings']['databasetype'] . '",';

if ($this->core['settings']['databasetype'] === 'hybrid') {
		$baseFileContent .=
'
		"db" 				=>
		[
			"host" 							=> "' . $dbConfig['host'] . '",
			"port" 							=> "' . $dbConfig['port'] . '",
			"dbname" 						=> "' . $dbConfig['dbname'] . '",
			"charset" 	 	    			=> "' . $dbConfig['charset'] . '",
			"collation" 	    			=> "' . $dbConfig['collation'] . '",
			"username" 						=> "' . $dbConfig['username'] . '",
			"password" 						=> "' . $dbConfig['password'] . '",
		],
		"ff" 				=>
		[
			"databaseDir" 					=> "' . $ffConfig['databaseDir'] . '"
		],';
} else if ($this->core['settings']['databasetype'] === 'ff') {
		$baseFileContent .=
'
		"ff" 				=>
		[
			"databaseDir" 					=> "' . $ffConfig['databaseDir'] . '"
		],';
} else if ($this->core['settings']['databasetype'] === 'db') {
		$baseFileContent .=
'
		"db" 				=>
		[
			"host" 							=> "' . $dbConfig['host'] . '",
			"port" 							=> "' . $dbConfig['port'] . '",
			"dbname" 						=> "' . $dbConfig['dbname'] . '",
			"charset" 	 	    			=> "' . $dbConfig['charset'] . '",
			"collation" 	    			=> "' . $dbConfig['collation'] . '",
			"username" 						=> "' . $dbConfig['username'] . '",
			"password" 						=> "' . $dbConfig['password'] . '",
		],';
}

		$baseFileContent .=
'
		"cache"				=>
		[
			"enabled"						=> ' . $this->core['settings']['cache']['enabled'] . ', //Global Cache value //true - Production false - Development
			"timeout"						=> ' . $this->core['settings']['cache']['timeout'] . ', //Global Cache timeout in seconds
			"service"						=> "' . $this->core['settings']['cache']['service'] . '"
		],
		"security"			=>
		[
			"sso"	  				 		=> ' . $this->core['settings']['security']['sso'] . ',
			"passwordWorkFactor"			=> ' . $this->core['settings']['security']['passwordWorkFactor'] . ',
			"cookiesWorkFactor" 			=> ' . $this->core['settings']['security']['cookiesWorkFactor'] . ',
		],
		"logs"				=>
		[
			"enabled"						=> ' . $this->core['settings']['logs']['enabled'] . ',
			"exceptions"					=> ' . $this->core['settings']['logs']['exceptions'] . ',
			"level"							=> "' . $this->core['settings']['logs']['level'] . '",
			"service"						=> "' . $this->core['settings']['logs']['service'] . '",
			"emergencyLogsEmail"			=> ' . $this->core['settings']['logs']['emergencyLogsEmail'] . ',
			"emergencyLogsEmailAddresses"	=> "' . $this->core['settings']['logs']['emergencyLogsEmailAddresses'] . '",
		],
		"websocket"			=>
		[
			"protocol"						=> "' . $this->core['settings']['websocket']['protocol'] . '",
			"host"							=> "' . $this->core['settings']['websocket']['host'] . '",
			"port"							=> ' . $this->core['settings']['websocket']['port'] . '
		],
		"timeout"			=>
		[
			"cookies"						=> ' . $this->core['settings']['timeout']['cookies'] . '
		]
	];';

		try {
			$this->localContent->write('/system/Configs/Base.php', $baseFileContent);
		} catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}
	}

	private function writeResetConfig()
	{
		$resetContent =
'<?php

return
	[
		"setup" 			=> true,
		"cache"				=>
		[
			"enabled"						=> false, //Global Cache value //true - Production false - Development
			"timeout"						=> 60, //Global Cache timeout in seconds
			"service"						=> "streamCache"
		],
		"logs"				=>
		[
			"enabled"						=> "false",
			"exceptions"					=> "false",
			"level"							=> "DEBUG",
			"service"						=> "streamLogs",
			"emergencyLogsEmail"			=> "false",
			"emergencyLogsEmailAddresses"	=> "",
		],
		"websocket"			=>
		[
			"protocol"						=> "tcp",
			"host"							=> "localhost",
			"port"							=> 5555
		],
		"timeout"			=>
		[
			"cookies"						=> 86400
		]
	];';

		try {
			$this->localContent->write('/system/Configs/Base.php', $resetContent);
		} catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}
	}

	private function createDbKey($dbname)
	{
		$keys = $this->getDbKey();

		$keys[$dbname] = $this->random->base58(4);

		try {
			$this->localContent->write('system/.dbkeys', $this->helper->encode($keys), ['visibility' => 'private']);
		} catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}

		return $keys[$dbname];
	}

	private function getDbKey($dbConfig = null)
	{
		try {
			$keys = $this->localContent->read('system/.dbkeys');

			if ($dbConfig) {
				return $this->helper->decode($keys, true)[$dbConfig['dbname']];
			}

			return $this->helper->decode($keys, true);
		} catch (\ErrorException | FilesystemException | UnableToReadFile $exception) {
			return false;
		}
	}

	private function removeDbKey($dbName)
	{
		$keys = $this->getDbKey();

		if (isset($keys[$dbName])) {
			unset($keys[$dbName]);
		}

		try {
			$this->localContent->write('system/.dbkeys', $this->helper->encode($keys), ['visibility' => 'private']);
		} catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}
	}

	public function getDb($active = true, $dbName = null)
	{
		foreach ($this->core['settings']['dbs'] as $db) {
			if ($active === true && $db['active'] == 'true') {
				return $db;
			} else if ($active === false &&
					   $dbName &&
					   $dbName === $db['dbname']
			) {
				return $db;
			}
		}

		return false;
	}

	public function getFf($active = true, $ffName = null)
	{
		foreach ($this->core['settings']['ffs'] as $ff) {
			if ($active === true && $ff['active'] == 'true') {
				return $ff;
			} else if ($active === false &&
					   $ffName &&
					   $ffName === $ff['ffname']
			) {
				return $ff;
			}
		}

		return false;
	}

	protected function validateEmail(array $data)
	{
		$this->validation->add('email', Email::class, ["message" => "Enter valid email address for Emergency Emails."]);

		$validated = $this->validation->validate($data)->jsonSerialize();

		if (count($validated) > 0) {
			$messages = 'Error: ';

			foreach ($validated as $key => $value) {
				$messages .= $value['message'] . ' ';
			}
			return $messages;
		} else {
			return true;
		}
	}

	public function get2faUsingOptions()
	{
		$available2fa = [];

		if ($this->basepackages->email->setup()) {
			$available2fa = array_merge($available2fa,
				[
					'email'   =>
					[
						'type'      	=> 'email',
						'name'          => 'EMAIL'
					]
				]
			);
		}

		if (class_exists("\OTPHP\TOTP") && class_exists("\OTPHP\HOTP")) {
			$available2fa = array_merge($available2fa,
				[
					'otp'   =>
					[
						'type'      	=> 'otp',
						'name'          => 'OTP'
					]
				]
			);
		}

		return $available2fa;
	}

	public function get2faAlgorithms()
	{
		$hashAlgos = [];

		foreach (hash_hmac_algos() as $algo) {
			$hashAlgos[$algo]['id'] = $algo;
			$hashAlgos[$algo]['name'] = strtoupper($algo);
		}

		return $hashAlgos;
	}

	public function get2faLogoLink($uuid, $width = 80)
	{
		$logoLink = $this->basepackages->storages->getPublicLink($uuid, $width);

		if ($logoLink) {
			$logoLink = str_replace('public/', '', $logoLink);
			$logoLink = $this->links->url($logoLink, true);

			return $logoLink;
		}

		return false;
	}
}