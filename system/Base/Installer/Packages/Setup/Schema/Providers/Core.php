<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers;

use Phalcon\Db\Column;

class Core
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
						'display_name',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 50,
							'notNull' => false,
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
						'version',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 15,
							'notNull' => true,
						]
					),
					new Column(
						'repo',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 2048,
							'notNull' => true,
						]
					),
					new Column(
						'settings',
						[
							'type'    => Column::TYPE_JSON,
							'notNull' => true,
						]
					)
				]
			];
	}
}