<?php

namespace System\Base\Providers\LoggerServiceProvider\Db\Model;

use System\Base\BaseModel;

class Logs extends BaseModel
{
	/**
	 * @Primary
	 * @Identity
	 * @Column(type='integer', nullable=false)
	 */
	public $id;

	/**
	 * @Column(column="type", type="integer")
	 */
	public $type;

	/**
	 * @Column(column="typeName", type="string", length=10)
	 */
	public $typeName;

	/**
	 * @Column(column="session", type="string", length=100)
	 */
	public $session;

	/**
	 * @Column(column="connection", type="string", length=10)
	 */
	public $connection;

	/**
	 * @Column(column="message", type="string")
	 */
	public $message;

	/**
	 * @Column(column="mseconds", type="string", length=2048)
	 */
	public $mseconds;
}