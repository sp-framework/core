<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Helper\Json;
use System\Base\Installer\Components\Setup;

class Pdo
{
	protected $dbConfig;

	protected $session;

	protected $localContent;

	protected $crypt;

	public function __construct($dbConfig, $session, $localContent, $crypt)
	{
		$this->dbConfig = $dbConfig;

		$this->session = $session;

		$this->localContent = $localContent;

		$this->crypt = $crypt;
	}

	public function init()
	{
		if ($this->checkDbConfig()) {
			try {
				$dbConfig = $this->dbConfig->toArray();

				$key = $this->getDbKey($dbConfig);

				if (!$key) {
					$this->runSetup(true, 'Unable to connect to DB server');

					return true;
				}

				$dbConfig['password'] = $this->crypt->decryptBase64($dbConfig['password'], $key);

				return new Mysql($dbConfig);
			} catch (\PDOException $e) {
				if ($e->getCode() === 1044 || $e->getCode() === 1045 || $e->getCode() === 1049) {
					$this->runSetup(true, $e->getMessage());
				}

				throw $e;
			}
		}
	}

	public function checkDbConfig()
	{
		if (!$this->dbConfig->host 		||
			!$this->dbConfig->dbname 	||
			!$this->dbConfig->username	||
			!$this->dbConfig->password 	||
			!$this->dbConfig->port
		) {
			$this->runSetup(true);
		}
		return true;
	}

	protected function runSetup($onlyUpdateDb = false, $message = null)
	{
		if (PHP_SAPI === 'cli') {
			exit;
		}

		require_once base_path('system/Base/Installer/Components/Setup.php');

		(new Setup($this->session))->run($onlyUpdateDb, $message);

		exit;
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
}