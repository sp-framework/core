<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\PdoFactory;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\DiInterface;
use System\Base\Installer\Setup;

class Pdo
{
	private $container;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;

		$this->dbConfig = $container->getShared('config')->db;
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
			require_once base_path('system/Base/Installer/Setup.php');

			(new Setup())->run();

			exit;
		}
		return true;
	}
}