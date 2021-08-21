<?php

namespace System\Base;

use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
	protected static $modelRelations;

	public function onConstruct()
	{

	}

	public function initialize()
	{
		$this->useDynamicUpdate(true);
	}

	public function getModelRelations()
	{
		return self::$modelRelations;
	}
}