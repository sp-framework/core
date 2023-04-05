<?php

namespace System\Base\Providers\CoreServiceProvider;

use Carbon\Carbon;
use Ifsnop\Mysqldump\Mysqldump;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\CoreServiceProvider\Model\Core as CoreModel;

class Core extends BasePackage
{
	protected $modelToUse = CoreModel::class;

	public $core;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		$this->core = $this->core[0];

		$this->core['settings'] = Json::decode($this->core['settings'], true);

		$this->checkKeys();

		$this->checkTmpPath();

		return $this;
	}

	public function dbBackup($data)
	{
		if (!isset($data['db'])) {
			$this->addResponse('Please provide db name', 1, []);

			return false;
		}

		if (!isset($this->core['settings']['dbs'][$data['db']])) {
			$this->addResponse('Db does not exist.', 1, []);

			return false;
		}

		$db = $this->core['settings']['dbs'][$data['db']];
		$db['password'] = $this->crypt->decryptBase64($db['password'], $this->getDbKey($db));

		try {
			$dumper =
				new Mysqldump(
					'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
					$db['username'],
					$db['password'],
					['compress' => Mysqldump::GZIP, 'default-character-set' => Mysqldump::UTF8MB4]
				);

			$fileName = 'db' . $data['db'] . Carbon::now()->getTimestamp() . '.gzip';

			$dumper->start(base_path('var/tmp/' . $fileName));
		} catch (\Exception $e) {
			$this->addResponse('Backup Error: ' . $e->getMessage(), 1);
		}

		try {
			$file = $this->localContent->read('var/tmp/' . $fileName);
		} catch (FilesystemException | UnableToReadFile | \Exception $exception) {
			throw $exception;
		}

		if ($this->basepackages->storages->storeFile(
				'private',
				'core',
				$file,
				$fileName,
				filesize(base_path('var/tmp/' . $fileName)),
				'application/gzip'
			)
		) {
			try {
				$file = $this->localContent->delete('var/tmp/' . $fileName);
			} catch (FilesystemException | UnableToDeleteFile | \Exception $exception) {
				throw $exception;
			}

			$this->basepackages->storages->changeOrphanStatus($this->basepackages->storages->packagesData->responseData['uuid']);

			$this->addResponse('Generated backup ' . $fileName . '.',
							   0,
							   ['filename' => $fileName,
								'uuid' => $this->basepackages->storages->packagesData->responseData['uuid']
							   ]
			);

			return true;
		}

		return false;
	}

	public function dbRestore($data)
	{
		if (!isset($data['filename'])) {
			$this->addResponse('Please provide database file name', 1, []);

			return false;
		}

		$fileInfo = $this->basepackages->storages->getFileInfo(null, $data['filename']);

		if ($fileInfo) {
			try {
				$newDbName = str_replace('.gzip', '', $fileInfo['org_file_name']);

				$file = $this->basepackages->storages->getFile(['uuid' => $fileInfo['uuid'], 'headers' => false]);

				$file = gzdecode($file);

				try {
					$this->localContent->write('var/tmp/' . $newDbName . '.sql' , $file);
				} catch (FilesystemException | UnableToWriteFile $exception) {
					throw $exception;
				}

				$newDbUserName = $newDbName . 'User';

				$dbConfig = $this->getActiveDb();
				$newDbUserPassword = $this->crypt->decryptBase64($dbConfig['password'], $this->getDbKey($dbConfig));
				$dbConfig['username'] = $data['username'];
				$dbConfig['password'] = $data['password'];
				$dbConfig['dbname'] = 'mysql';
				$this->db = new Mysql($dbConfig);

				$this->executeSQL("CREATE DATABASE IF NOT EXISTS " . $newDbName . " CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
				$checkUser = $this->executeSQL("SELECT * FROM `user` WHERE `User` LIKE ?", [$newDbUserName]);
				if ($checkUser->numRows() === 0) {
					$this->executeSQL("CREATE USER ?@'%' IDENTIFIED WITH mysql_native_password BY ?;", [$newDbUserName, $newDbUserPassword]);
				}
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

				//Store new username pass to $this->core->settings->dbs
				$this->addResponse($data['filename'] . ' restored!');

				return true;
			} catch (\Exception $e) {
				$this->addResponse('Restore Error: ' . $e->getMessage(), 1);

				return false;
			}
		}

		$this->addResponse('File ' . $data['filename'] . ' not found on system!', 1);

		return false;
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
			$this->localContent->write('system/.keys', Json::encode($keys));
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

		$keys = Json::decode($keysFile, true);

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

	private function writeResetConfig()
	{
		$resetContent =
'<?php

return
	[
		"setup" 		=> true
	];';

		$this->localContent->write('/system/Configs/Base.php', $resetContent);
	}

	private function getDbKey($dbConfig)
	{
		try {
			$keys = $this->localContent->read('system/.dbkeys');

			return Json::decode($keys, true)[$dbConfig['dbname']];
		} catch (\ErrorException | FilesystemException | UnableToReadFile $exception) {
			return false;
		}
	}

	protected function checkTmpPath()
	{
		if (!is_dir(base_path('var/tmp/'))) {
			if (!mkdir(base_path('var/tmp/'), 0777, true)) {
				return false;
			}
		}

		return true;
	}

	public function getActiveDb()
	{
		foreach ($this->core['settings']['dbs'] as $db) {
			if ($db['active'] == true) {
				return $db;
			}
		}

		return false;
	}
}