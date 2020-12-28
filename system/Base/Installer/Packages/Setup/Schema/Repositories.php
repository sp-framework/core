<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;

class Repositories
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
						'description',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 2048,
							'notNull' => false,
						]
					),
					new Column(
						'repo_url',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 1024,
							'notNull' => true,
						]
					),
					new Column(
						'site_url',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 1024,
							'notNull' => true,
						]
					),
					new Column(
						'branch',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 100,
							'notNull' => true,
						]
					),
					new Column(
						'repo_provider',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'size'    => 1,
							'notNull' => true,
						]
					),
					new Column(
						'auth_token',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'size'    => 1,
							'notNull' => false,
						]
					),
					new Column(
						'username',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 50,
							'notNull' => false,
						]
					),
					new Column(
						'password',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 50,
							'notNull' => false,
						]
					),
					new Column(
						'token',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 2048,
							'notNull' => false,
						]
					),
				]
			];
	}
}