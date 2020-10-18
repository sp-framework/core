<?php

namespace System\Base\Installer\Packages\Setup\Write;

class DatabaseServiceProvider
{
	public function write($localContent)
	{
		$databaseServiceProvider =
'<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\PdoFactory;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\DiInterface;
use System\Base\Installer\Setup;

class Db
{
	private $container;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;

		$this->dbConfig = $container->getShared("config")->db;
	}

	public function getPdo()
	{
		return new Mysql($this->dbConfig->toArray());
	}
}';

		$localContent->put(
			'/system/Base/Providers/DatabaseServiceProvider/Db.php',
			$databaseServiceProvider
		);
	}
}