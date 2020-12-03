<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Applications
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
					'category',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'	  => 50,
						'notNull' => true,
					]
				),
				new Column(
					'sub_category',
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
					'default_errors_component',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'default_view',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'default_email_service',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => false,
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
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 4096,
						'notNull' => false,
					]
				),
				new Column(
					'settings',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => false,
					]
				),
				// new Column(
				// 	'version',
				// 	[
				// 		'type'    => Column::TYPE_VARCHAR,
				// 		'size'    => 15,
				// 		'notNull' => true,
				// 	]
				// ),
				// new Column(
				// 	'repo',
				// 	[
				// 		'type'    => Column::TYPE_VARCHAR,
				// 		'size'    => 2048,
				// 		'notNull' => true,
				// 	]
				// ),
				// new Column(
				// 	'dependencies',
				// 	[
				// 		'type'    => Column::TYPE_TEXT,
				// 		'notNull' => true,
				// 	]
				// ),
				// new Column(
				// 	'installed',
				// 	[
				// 		'type'    => Column::TYPE_TINYINTEGER,
				// 		'size'    => 1,
				// 		'notNull' => true,
				// 	]
				// ),
				// new Column(
				// 	'files',
				// 	[
				// 		'type'    => Column::TYPE_TEXT,
				// 		'notNull' => false,
				// 	]
				// ),
				// new Column(
				// 	'update_available',
				// 	[
				// 		'type'    => Column::TYPE_TINYINTEGER,
				// 		'size'    => 1,
				// 		'notNull' => false,
				// 	]
				// ),
				// new Column(
				// 	'update_version',
				// 	[
				// 		'type'    => Column::TYPE_VARCHAR,
				// 		'size'    => 15,
				// 		'notNull' => false,
				// 	]
				// ),
			],
			'indexes' => [
				new Index(
					'column_UNIQUE',
					[
						'name'
					],
					'UNIQUE'
				)
			]
		];
	}
}