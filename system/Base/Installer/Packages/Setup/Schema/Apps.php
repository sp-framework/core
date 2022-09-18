<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Apps
{
	public function columns()
	{
		return
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_TINYINTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'route',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'description',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'app_type',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'	  => 50,
						'notNull' => true,
					]
				),
				new Column(
					'default_component',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'errors_component',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'registration_allowed',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => false,
					]
				),
				new Column(
					'registration_role_id',
					[
						'type'    => Column::TYPE_SMALLINTEGER,
						'notNull' => false,
					]
				),
				new Column(
					'guest_role_id',
					[
						'type'    => Column::TYPE_SMALLINTEGER,
						'notNull' => false,
					]
				),
				new Column(
					'can_login_role_ids',
					[
						'type'    => Column::TYPE_JSON,
						'size'    => 4096,
						'notNull' => false,
					]
				),
				new Column(
					'ip_black_list',
					[
						'type'    => Column::TYPE_JSON,
						'notNull' => false,
					]
				),
				new Column(
					'incorrect_login_attempt_blacklist',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => false,
					]
				)
			],
			'indexes' => [
				new Index(
					'column_UNIQUE',
					[
						'route'
					],
					'UNIQUE'
				)
			],
			'options' => [
				'TABLE_COLLATION' => 'utf8mb4_general_ci'
			]
		];
	}
}