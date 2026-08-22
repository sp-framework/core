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
 * Table schema definition for Apps (Providers Apps).
 */
class Apps
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
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'route',
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
					'app_type',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'	  => 50,
						'notNull' => true,
					]
				),
				new Column(
					'default_component_guests',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'default_component_users',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'errors_component',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'registration_allowed',
					[
						'type'    => Column::TYPE_BOOLEAN,
						'notNull' => false,
					]
				),
				new Column(
					'recover_password',
					[
						'type'    => Column::TYPE_BOOLEAN,
						'notNull' => false,
					]
				),
				new Column(
					'approve_accounts_manually',
					[
						'type'    => Column::TYPE_BOOLEAN,
						'notNull' => false,
					]
				),
				new Column(
					'enforce_2fa',
					[
						'type'    => Column::TYPE_BOOLEAN,
						'notNull' => false,
					]
				),
				new Column(
					'registration_role_id',
					[
						'type'    => Column::TYPE_SMALLINTEGER,
						'notNull' => false,
					]
				),
				new Column(
					'guest_role_id',
					[
						'type'    => Column::TYPE_SMALLINTEGER,
						'notNull' => false,
					]
				),
				new Column(
					'can_login_role_ids',
					[
						'type'    => Column::TYPE_JSON,
						'size'    => 4096,
						'notNull' => false,
					]
				),
				new Column(
					'acceptable_usernames',
					[
						'type'    => Column::TYPE_JSON,
						'size'    => 4096,
						'notNull' => false,
					]
				),
				new Column(
					'menu_structure',
					[
						'type'    => Column::TYPE_JSON,
						'notNull' => false,
					]
				),
				new Column(
					'use_app_db',
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
				),
			],
			'indexes' => [
				new Index(
					'column_UNIQUE',
					[
						'route'
					],
					'UNIQUE'
				)
			]
		];
	}
}
