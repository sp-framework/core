<?php

namespace System\Base\Installer\Packages\Setup\Write;

class Pdo
{
	public function write($localContent)
	{
		$databaseServiceProvider =
'<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\Pdo\Mysql;

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
}';

		$localContent->write(
			'/system/Base/Providers/DatabaseServiceProvider/Pdo.php',
			$databaseServiceProvider
		);
	}
}