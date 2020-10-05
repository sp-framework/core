<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

class BaseDb
{
	private $em;

	protected $db;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;

		$this->db = $this;
	}

	public function getAll($modelClass, array $data = null)
	{
		return $this->em->getRepository($modelClass)->findAll($data);
	}

	public function getById($modelClass, $id)
	{
		if (isset($this->em->find($modelClass, $id)->ver)) {
			return $this->em->find($modelClass, $id, LockMode::OPTIMISTIC);
		} else {
			return $this->em->find($modelClass, $id);
		}
	}

	public function getByData($modelClass, array $criteria, array $sort = null, $limit = null, $offset = null)
	{
		return $this->em->getRepository($modelClass)->findBy($criteria, $sort, $limit, $offset);
	}

	public function addToDb($modelClass, array $data)
	{
		if ($this->valueIsArray($data)) {
			throw new \Exception('Value is array and must be serialized before adding to db.');
		}

		$class = new $modelClass($this->em);

		if ($class) {
			$class->fill($data);

			$this->em->persist($class);

			$this->em->flush();

			return $class;
		}
	}

	public function updateToDbById($modelClass, array $data)
	{
		if ($this->valueIsArray($data)) {
			throw new \Exception('Value is array and must be serialized before adding to db.');
		}

		$class = $this->getById($modelClass, $data['id']);

		if ($class) {
			$class->fill($data);

			$this->em->flush();

			return $class;
		}

		return false;
	}

	public function updateToDbByData($modelClass, array $data, array $searchData = null)
	{
		if ($this->valueIsArray($data)) {
			throw new \Exception('Value is array and must be serialized before adding to db.');
		}

		$class = $this->getByData($modelClass, $searchData);

		if ($class) {
			$class->fill($data);

			$this->em->flush();

			return $class;
		}
	}

	public function deleteFromDbById($modelClass, $id)
	{
		$class = $this->getById($modelClass, $id);

		if ($class) {
			$this->em->remove($class);

			$this->em->flush();

			return $class;
		}
	}

	// public function deleteFromDbByData($modelClass, array $data)
	// {
	// 	$class = $this->getById($modelClass, $id);

	// 	if ($class) {
	// 		$this->em->remove($class);

	// 		$this->em->flush();

	// 		return $class;
	// 	}
	// }

	// Check if values of provided array is no array as we need to serialize the data.
	protected function valueIsArray(array $data)
	{
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				return true;
			}
		}

		return false;
	}
}