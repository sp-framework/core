<?php

namespace System\Base;

use Phalcon\Mvc\Model;

abstract class BaseModel extends Model
{
	public function onConstruct()
	{

	}

	public function initialize()
	{
		$this->useDynamicUpdate(true);
	}
}