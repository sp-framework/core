<?php

namespace System\Base;

use Phalcon\Helper\Arr;
use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
	protected $app;

	protected $apps;

	protected $modules;

	protected $db;

	protected $modelRelations;

	public function onConstruct()
	{
		$this->useDynamicUpdate(true);
		$this->setTableSource();
	}

	public function initialize()
	{
		//
	}

	protected function setTableSource()
	{
		$reflection = new \ReflectionClass($this);

		$tableNameArr = preg_split('/(?=[A-Z])/', $reflection->getShortName(), -1, PREG_SPLIT_NO_EMPTY);

		$this->setSource(strtolower(join('_', $tableNameArr)));
	}

	public function init()
	{
		$this->apps = $this->getDi()->getShared('apps');

		$this->app = $this->apps->getAppInfo();

		$this->modules = $this->getDi()->getShared('modules');

		if (!isset($this->db)) {
			$this->db = $this->getDi()->getShared('db');
		}

		return $this;
	}

	protected function checkPackage($packageClass)
	{
		return
			$this->modules->packages->getPackageByNameForAppId(
				$this->helper->last(explode('\\', $packageClass)),
				$this->app['id']
			);
	}

	public function getModelRelations()
	{
		return $this->modelRelations;
	}

	public function tableExists(string $table)
	{
		return $this->db->tableExists($table);
	}

	public function executeSQL(string $sql, $data = [])
	{
		try {
			return $this->db->query($sql, $data);
		} catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}
	}
}