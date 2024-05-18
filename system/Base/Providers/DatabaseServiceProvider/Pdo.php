<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use Phalcon\Config\Config as PhalconConfig;
use Phalcon\Db\Adapter\Pdo\Mysql;
use System\Base\Installer\Components\Setup;

class Pdo
{
	protected $config;

	protected $dbConfig;

	protected $session;

	protected $localContent;

	protected $crypt;

	protected $helper;

	protected $configsObj;

	public function __construct($config, $session, $localContent, $crypt, $helper)
	{
		$this->config = $config;

		$this->dbConfig = $config->db;

		$this->session = $session;

		$this->localContent = $localContent;

		$this->crypt = $crypt;

		$this->helper = $helper;

		$this->configsObj = new PhalconConfig($this->config->toArray());
	}

	public function init()
	{
		if ($this->checkDbConfig()) {
			try {
				$dbConfig = $this->dbConfig->toArray();

				$key = $this->getDbKey($dbConfig);

				if (!$key) {
					$this->runSetup(true, 'Unable to connect to DB server', $this->configsObj);

					return true;
				}

				try {
					$dbConfig['password'] = $this->crypt->decryptBase64($dbConfig['password'], $key);
				} catch (\Exception $e) {
					$this->runSetup(true, $e->getMessage(), $this->configsObj);

					return true;
				}

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
			$this->runSetup(true, 'DB configuration missing or has errors');
		}
		return true;
	}

	protected function runSetup($onlyUpdateDb = false, $message = null)
	{
		if (PHP_SAPI === 'cli') {
			sleep(10);//This is to avoid supervisord from not retrying

			exit();
		}

		require_once base_path('system/Base/Installer/Components/Setup.php');

		(new Setup($this->session, $this->configsObj, $onlyUpdateDb))->run($onlyUpdateDb, $message);

		exit;
	}

	private function getDbKey($dbConfig)
	{
		try {
			$keys = $this->localContent->read('system/.dbkeys');

			return $this->helper->decode($keys, true)[$dbConfig['dbname']];
		} catch (\ErrorException | FilesystemException | UnableToReadFile $exception) {
			return false;
		}
	}
}