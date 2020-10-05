<?php

namespace System\Base\Providers\ModulesServiceProvider;

use League\Container\Container;
use System\Base\Providers\ModulesServiceProvider\Model\Repositories as RepositoriesModel;
use System\Base\Providers\ModulesServiceProvider\ModulesInterface;

class Repositories implements ModulesInterface
{
	private $container;

	protected $db;

	protected $em;

	public function __construct(Container $container)
	{
		$this->container = $container;

		$this->db = $this->container->get('db');

		$this->em = $this->container->get('em');
	}

	public function getById($id)
	{
		return $this->db->getById(RepositoriesModel::class, $id);
	}

	public function getAll($criteria = [], $sort = null, $limit = null, $offset = null)
	{
		return $this->db->getByData(RepositoriesModel::class, $criteria, $sort, $limit, $offset);
	}

	public function register(array $data)
	{
		return $this->db->addToDb(RepositoriesModel::class, $data);
	}

	public function update(array $data)
	{
		return $this->db->updateToDbById(RepositoriesModel::class, $data);
	}

	public function remove($id)
	{
		if ($id !== '1') {
			return $this->db->deleteFromDbById(RepositoriesModel::class, $id);
		} else {
			return false;
		}
	}
}