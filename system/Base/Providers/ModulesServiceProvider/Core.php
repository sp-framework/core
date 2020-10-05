<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Phalcon\Di\DiInterface;

class Core
{
	private $container;

	protected $db;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;

		$this->db = $this->container->getShared('db');
	}

	public function getCoreInfo()
	{
		return $this->db->fetchOne("SELECT * FROM core");
	}
	// public function updateCoreInfo(array $newCoreInfo)
	// {
	// 	return $this->db->update('core', );
	// }
}