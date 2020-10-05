<?php

namespace System\Base\Providers\ModulesServiceProvider;

use League\Container\Container;
use System\Base\Providers\ModulesServiceProvider\Model\Middlewares as MiddlewaresModel;
use System\Base\Providers\ModulesServiceProvider\ModulesInterface;

class Middlewares implements ModulesInterface
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
		return $this->db->getById(MiddlewaresModel::class, $id);
	}

	public function getAll($criteria = [], $sort = null, $limit = null, $offset = null)
	{
		return $this->db->getByData(MiddlewaresModel::class, $criteria, $sort, $limit, $offset);
	}

	public function register(array $data)
	{
		return $this->db->addToDb(MiddlewaresModel::class, $data);
	}

	public function update(array $data)
	{
		return $this->db->updateToDbById(MiddlewaresModel::class, $data);
	}

	public function remove($id)
	{
		//
	}
}