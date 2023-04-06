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
use System\Base\Providers\CoreServiceProvider\Model\ServiceProviderCore;

class Core extends BasePackage
{
	protected $modelToUse = ServiceProviderCore::class;

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

			$fileName = 'db' . $data['db'] . Carbon::now()->getTimestamp() . '.gz';

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
				// if (checkCtype($data['dbname']))
				$newDbName = str_replace('.gz', '', $fileInfo['org_file_name']);

				$file = $this->basepackages->storages->getFile(['uuid' => $fileInfo['uuid'], 'headers' => false]);

				$file = gzdecode($file);

				try {
					$this->localContent->write('var/tmp/' . $newDbName . '.sql' , $file);
				} catch (FilesystemException | UnableToWriteFile $exception) {
					throw $exception;
				}
				var_dump($data);die();
				$newDbUserName = $newDbName . 'User';

				$dbConfig = $this->getDb(true);
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

	public function removeDb(array $data)
	{
		if (!isset($data['db'])) {
			$this->addResponse('Please provide database name', 1, []);

			return false;
		}

		$dbConfig = $this->getDb(false, $data['db']);

		if ($dbConfig['active'] == true) {
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

	public function updateDb(array $data)
	{
		if (!isset($data['db']['dbname'])) {
			$this->addResponse('Please provide database name', 1, []);

			return false;
		}

		$dbConfig = $this->getDb(false, $data['db']['dbname']);
		// var_dump($dbConfig);die();
		if ($dbConfig['active'] == true) {
			$this->addResponse('Cannot update active database', 1, []);

			return false;
		}

		$dbConfig = array_merge($dbConfig, $data['db']);

		// Try Connecting to DB with new information
		try {
			$this->db = new Mysql($dbConfig);

			if ($this->core['settings']['dbs'][$dbConfig['dbname']]['charset'] !== $dbConfig['charset']) {
				$charsetCollate = $dbConfig['charset'] . '_general_ci';
				$this->executeSQL("ALTER DATABASE ? CHARACTER SET ? COLLATE ?", [$dbConfig['dbname'], $dbConfig['charset'], $charsetCollate]);
				// ALTER TABLE tablename CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
			}
		} catch (\PDOException $e) {
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

	public function updateCore(array $data)
	{
		$core = $this->core;

		if (isset($data['cache'])) {
			$this->core['settings']['cache']['enabled'] = $data['cache'];
		}
		if (isset($data['cache_timeout'])) {
			$this->core['settings']['cache']['timeout'] = $data['cache_timeout'];
		}
		if (isset($data['cache_service'])) {
			$this->core['settings']['cache']['service'] = $data['cache_service'];
		}

		if (isset($data['passwordWorkFactor'])) {
			$this->core['settings']['security']['passwordWorkFactor'] = $data['passwordWorkFactor'];
		}
		if (isset($data['cookiesWorkFactor'])) {
			$this->core['settings']['security']['cookiesWorkFactor'] = $data['cookiesWorkFactor'];
		}

		if (isset($data['logs'])) {
			$this->core['settings']['logs']['enabled'] = $data['logs'];
		}
		if (isset($data['logs_level'])) {
			$this->core['settings']['logs']['level'] = $data['logs_level'];
		}
		if (isset($data['emergency_logs_email'])) {
			$this->core['settings']['logs']['emergencyLogsEmail'] = $data['emergency_logs_email'];
		}
		if (isset($data['emergency_logs_email_addresses'])) {
			$this->core['settings']['logs']['emergencyLogsEmailAddresses'] = $data['emergency_logs_email_addresses'];
		}
		if (isset($data['dbs'])) {
			$data['dbs'] = Json::decode($data['dbs'], true);
			$this->core['settings']['dbs'] = $data['dbs'];
		}

		$this->update($data);
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

	private function writeBaseConfig($dbConfig)
	{
		$this->core['settings']['setup'] = $this->core['settings']['setup'] === true ? 'true' : 'false';
		$this->core['settings']['dev'] = $this->core['settings']['dev'] === true ? 'true' : 'false';
		$this->core['settings']['debug'] = $this->core['settings']['debug'] === true ? 'true' : 'false';
		$this->core['settings']['cache']['enabled'] = $this->core['settings']['cache']['enabled'] === true ? 'true' : 'false';
		$this->core['settings']['logs']['level'] = $this->core['settings']['logs']['level'] === true ? 'true' : 'false';

		$baseFileContent =
'<?php

return
	[
		"setup" 			=> ' . $this->core['settings']['setup'] .',
		"dev"    			=> ' . $this->core['settings']['dev'] . ', //true - Development false - Production
		"debug"				=> ' . $this->core['settings']['debug'] . ',
		"db" 				=>
		[
			"host" 							=> "' . $dbConfig['host'] . '",
			"port" 							=> "' . $dbConfig['port'] . '",
			"dbname" 						=> "' . $dbConfig['dbname'] . '",
			"charset" 	 	    			=> "' . $dbConfig['charset'] . '"
			"username" 						=> "' . $dbConfig['username'] . '",
			"password" 						=> "' . $dbConfig['password'] . '",
		],
		"cache"				=>
		[
			"enabled"						=> ' . $this->core['settings']['cache']['enabled'] . ', //Global Cache value //true - Production false - Development
			"timeout"						=> ' . $this->core['settings']['cache']['timeout'] . ', //Global Cache timeout in seconds
			"service"						=> "' . $this->core['settings']['cache']['service'] . '"
		],
		"security"			=>
		[
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
			"protocol"						=> "tcp",
			"host"							=> "localhost",
			"port"							=> 5555
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
		"setup" 		=> true
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
			$this->localContent->write('system/.dbkeys', Json::encode($keys));
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
				return Json::decode($keys, true)[$dbConfig['dbname']];
			}

			return Json::decode($keys, true);
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

	public function getDb($active = true, $dbName = null)
	{
		foreach ($this->core['settings']['dbs'] as $db) {
			if ($active === true && $db['active'] == true) {
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

	public function generateNewPassword()
	{
		$this->addResponse('Password Generate Successfully', 0, ['password' => $this->secTools->random->base62(12)]);
	}
}