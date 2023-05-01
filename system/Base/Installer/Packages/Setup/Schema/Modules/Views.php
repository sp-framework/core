<?php

namespace System\Base\Installer\Packages\Setup\Schema\Modules;

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
						'module_type',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'	  => 50,
							'notNull' => true,
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
						'apps',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'	  => 2048,
							'notNull' => true,
						]
					),
					new Column(
						'installed',
						[
							'type'    => Column::TYPE_TINYINTEGER,
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
						'api_id',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'notNull' => true,
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
					new Column(
						'updated_by',
						[
							'type'    => Column::TYPE_INTEGER,
							'notNull' => true,
						]
					),
					new Column(
						'updated_on',
						[
							'type'    => Column::TYPE_TIMESTAMP,
							'notNull' => true,
							'default' => 'CURRENT_TIMESTAMP',
						]
					),
					new Column(
						'repo_details',
						[
							'type'    => Column::TYPE_JSON,
							'notNull' => false,
						]
					)
				]
			];
	}
}