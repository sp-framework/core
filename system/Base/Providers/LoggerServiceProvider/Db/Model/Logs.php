<?php

namespace System\Base\Providers\LoggerServiceProvider\Db\Model;

use System\Base\BaseModel;

class Logs extends BaseModel
{
	public $id;

	public $type;

	public $typeName;

	public $session;

	public $connection;

	public $message;

	public $mseconds;
}