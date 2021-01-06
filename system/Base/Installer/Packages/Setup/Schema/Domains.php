<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Domains
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
							'size'    => 100,
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
						'default_application_id',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'notNull' => true,
						]
					),
					new Column(
						'exclusive_to_default_application',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'notNull' => false,
						]
					),
					new Column(
						'applications',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 2048,
							'notNull' => false,
						]
					),
					new Column(
						'settings',
						[
							'type'    => Column::TYPE_TEXT,
							'notNull' => false,
						]
					)
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