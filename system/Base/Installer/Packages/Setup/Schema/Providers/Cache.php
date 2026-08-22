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
 * Table schema definition for Cache (Providers Cache).
 */
class Cache
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
						'key',
						[
							'type'    => Column::TYPE_VARCHAR,
							'size'    => 2048,
							'notNull' => true,
						]
					),
					new Column(
						'query',
						[
							'type'    => Column::TYPE_VARCHAR,
							'notNull' => true,
						]
					),
					new Column(
						'status',
						[
							'type'    => Column::TYPE_TINYINTEGER,
							'size'    => 1,
							'notNull' => true,
						]
					)
				]
			];
	}
}
