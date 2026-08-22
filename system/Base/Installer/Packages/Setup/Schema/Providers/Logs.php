<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package 	System\Base\Installer\Packages\Setup\Schema\Providers
 * @copyright 	Copyright (c) 2026
 * @link      	https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Providers;

use Phalcon\Db\Column;

/**
 * Table schema definition for Logs (Providers Logs).
 */
class Logs
{
	/**
	 * Defines table columns and data types.
	 *
	 * @return array<string, array<int, Column>> Columns definition array.
	 */
	public function columns(): array
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
					'type_name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 10,
						'notNull' => true,
					]
				),
				new Column(
					'client_ip',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
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
