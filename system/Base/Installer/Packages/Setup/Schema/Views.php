<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;

class Views
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
							'type'    => Column::TYPE_TEXT,
							'notNull' => true,
						]
					),
					new Column(
						'dependencies',
						[
							'type'    => Column::TYPE_TEXT,
							'notNull' => true,
						]
					),
					new Column(
						'application_id',
						[
							'type'    => Column::TYPE_INTEGER,
							'notNull' => true,
						]
					),
					new Column(
						'view_id',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'notNull' => false,
						]
					),
					new Column(
						'installed',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'size'    => 1,
							'notNull' => true,
						]
					),
					new Column(
						'files',
						[
							'type'    => Column::TYPE_MEDIUMTEXT,
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