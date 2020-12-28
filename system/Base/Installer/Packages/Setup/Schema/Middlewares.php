<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;

class Middlewares
{
	public function columns()
	{
		return
			[
			   'columns' => [
					new Column(
						'id',
						[
							'type'          => Column::TYPE_SMALLINTEGER,
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
						'class',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 2048,
							'notNull' => true,
						]
					),
					new Column(
						'settings',
						[
							'type'    => Column::TYPE_TEXT,
							'notNull' => true,
						]
					),
					new Column(
						'applications',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'	  => 2048,
							'notNull' => true,
						]
					),
					new Column(
						'files',
						[
							'type'    => Column::TYPE_TEXT,
							'notNull' => false,
						]
					),
					new Column(
						'update_available',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'size'    => 1,
							'notNull' => false,
						]
					),
					new Column(
						'update_version',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 15,
							'notNull' => false,
						]
					),
				]
			];
	}
}