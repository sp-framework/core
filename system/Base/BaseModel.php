<?php

namespace System\Base;

use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
	protected $app;

	protected $modules;

	protected $helper;

	protected $config;

	protected $db;

	protected $modelRelations;

	public function onConstruct()
	{
		$this->useDynamicUpdate(true);
	}

	public function initialize()
	{
		//
	}

	protected function setTableSource($source = null)
	{
		if (!$source) {
			$reflection = new \ReflectionClass($this);

			$tableNameArr = preg_split('/(?=[A-Z])/', $reflection->getShortName(), -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$tableNameArr = preg_split('/(?=[A-Z])/', $source, -1, PREG_SPLIT_NO_EMPTY);
		}

		$this->setSource(strtolower(join('_', $tableNameArr)));
	}

	public function init($app = null)
	{
		$source = null;

		if ($app) {
			$this->app = $app;

			$this->modules = $this->getDi()->getShared('modules');

			$this->helper = $this->getDi()->getShared('helper');

			$this->config = $this->getDi()->getShared('config');

			if (!isset($this->db) && $this->config->databasetype !== 'ff') {
				$this->db = $this->getDi()->getShared('db');
			}

			if (isset($this->app['use_app_db']) && $this->app['use_app_db'] === true) {
				if (str_starts_with(get_class($this), 'Apps\\' . ucfirst($this->app['app_type']))) {
					$modelArr = explode('\\', get_class($this));

					if (str_starts_with($this->helper->last($modelArr), 'Apps' . ucfirst($this->app['app_type']))) {
						$source = str_replace(ucfirst($this->app['app_type']), ucfirst($this->app['route']), $this->helper->last($modelArr));
					}
				}
			}
		}

		$this->setTableSource($source);

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

	protected function logException($exception)
	{
		if ($this->config->logs->exceptions) {
			$this->logger->logExceptions->critical(json_trace($exception));
		}
	}
}