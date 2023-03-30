<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\Pdo\Mysql;
use System\Base\Installer\Components\Setup;

class Pdo
{
	protected $dbConfig;

	protected $session;

	public function __construct($dbConfig, $session)
	{
		$this->dbConfig = $dbConfig;

		$this->session = $session;
	}

	public function init()
	{
		if ($this->checkDbConfig()) {
			try {
				return new Mysql($this->dbConfig->toArray());
			} catch (\PDOException $e) {
				if ($e->getCode() === 1044 || $e->getCode() === 1049) {
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
			$this->runSetup();
		}
		return true;
	}

	protected function runSetup($onlyUpdateDb = false, $message = null)
	{
		require_once base_path('system/Base/Installer/Components/Setup.php');

		(new Setup($this->session))->run($onlyUpdateDb, $message);

		exit;
	}
}