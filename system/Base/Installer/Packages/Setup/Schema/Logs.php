<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;

class Logs
{
	public function columns()
	{
		return
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_INTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'type',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'typeName',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 10,
						'notNull' => true,
					]
				),
				new Column(
					'session',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 100,
						'notNull' => true,
					]
				),
				new Column(
					'connection',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 10,
						'notNull' => true,
					]
				),
				new Column(
					'message',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'mseconds',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 100,
						'notNull' => true,
					]
				)
			]
		];
	}
}