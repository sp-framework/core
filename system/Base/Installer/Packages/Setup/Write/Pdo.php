<?php

namespace System\Base\Installer\Packages\Setup\Write;

class Pdo
{
	public function write($localContent)
	{
		$databaseServiceProvider =
'<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\PdoFactory;
use Phalcon\Db\Adapter\Pdo\Mysql;

class Pdo
{
	protected $dbConfig;

	public function __construct($dbConfig)
	{
		$this->dbConfig = $dbConfig;
	}

	public function init()
	{
		return new Mysql($this->dbConfig->toArray());
	}
}';

		$localContent->put(
			'/system/Base/Providers/DatabaseServiceProvider/Pdo.php',
			$databaseServiceProvider
		);
	}
}