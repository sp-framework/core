<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers;

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
							'notNull' => true
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
						'default_app_id',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'notNull' => true,
						]
					),
					new Column(
						'exclusive_to_default_app',
						[
							'type'    => Column::TYPE_BOOLEAN,
							'notNull' => true,
						]
					),
					new Column(
						'exclusive_for_api',
						[
							'type'    => Column::TYPE_BOOLEAN,
							'notNull' => true,
						]
					),
					new Column(
						'apps',
						[
							'type'    => Column::TYPE_JSON,
							'notNull' => false,
						]
					),
					new Column(
						'dns_record',
						[
							'type'    => Column::TYPE_JSON,
							'size'	  => 4096,
							'notNull' => false,
						]
					),
					new Column(
						'is_internal',
						[
							'type'    => Column::TYPE_BOOLEAN,
							'notNull' => false,
						]
					),
					new Column(
						'settings',
						[
							'type'    => Column::TYPE_JSON,
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