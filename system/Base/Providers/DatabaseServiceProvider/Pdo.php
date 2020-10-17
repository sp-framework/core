<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\PdoFactory;
use Phalcon\Db\Adapter\Pdo\Mysql;
use System\Base\Installer\Components\Setup;

class Pdo
{
	protected $dbConfig;

	public function __construct($dbConfig)
	{
		$this->dbConfig = $dbConfig;
	}

	public function init()
	{
		if ($this->checkDbConfig()) {
			return new Mysql($this->dbConfig->toArray());
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
			require_once base_path('system/Base/Installer/Components/Setup.php');

			(new Setup())->run();

			exit;
		}
		return true;
	}
}