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
use Phalcon\Db\Index;

/**
 * Table schema definition for Domains (Providers Domains).
 */
class Domains
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
							'type'    => Column::TYPE_INTEGER,
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
