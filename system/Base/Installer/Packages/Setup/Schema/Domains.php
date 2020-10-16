<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;

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
						'domain',
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
						'settings',
						[
							'type'    => Column::TYPE_TEXT,
							'notNull' => true,
						]
					)
				]
			];
	}
}