<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Doctrine\ORM\Tools\SchemaTool;
use League\Container\Container;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use System\Base\Providers\ModulesServiceProvider\Model\Components as ComponentsModel;
use System\Base\Providers\ModulesServiceProvider\ModulesInterface;

class Components implements ModulesInterface
{
	private $container;

	protected $componentInfo = null;

	protected $allComponents = [];

	protected $db;

	protected $em;

	public function __construct(Container $container)
	{
		$this->container = $container;

		$this->db = $this->container->get('db');

		$this->em = $this->container->get('em');
	}

	public function getAll($criteria = [], $sort = null, $limit = null, $offset = null)
	{
		return $this->db->getByData(ComponentsModel::class, $criteria, $sort, $limit, $offset);
	}

	public function getById($id)
	{
		return $this->db->getById(ComponentsModel::class, $id);
	}

	public function register(array $data)
	{
		return $this->db->addToDb(ComponentsModel::class, $data);
	}

	public function update(array $data)
	{
		return $this->db->updateToDbById(ComponentsModel::class, $data);
	}

	public function remove($id)
	{
		//
	}
}