<?php

namespace System\Base\Installer\Packages\Setup\Register\Providers;

use Phalcon\Db\Enum;

class Core
{
	public function register($baseConfig, $db, $ff)
	{
		$core =
			[
				'name' 					=> $baseConfig['name'],
				'display_name'			=> $baseConfig['display_name'],
				'description' 			=> $baseConfig['description'],
				'version'	 			=> $baseConfig['version'],
				'repo'					=> $baseConfig['repo'],
				'settings'			 	=>
					isset($baseConfig['settings']) ?
					json_encode($baseConfig['settings']) :
					null
			];

		if ($db) {
			$db->insertAsDict('service_provider_core', $core);
		}

		if ($ff) {
			$coreStore = $ff->store('service_provider_core');

			$coreStore->updateOrInsert($core);
		}
	}

	public function onlyUpdateDb($dbs, $helper, $db, $ff)
	{
		if ($ff) {
			$coreStore = $ff->store('service_provider_core');

			$core = $coreStore->findById('1');

			if (is_string($core['settings'])) {
				$core['settings'] = $helper->decode($core['settings'], true);
			}

			$core['settings']['dbs'] = array_merge($core['settings']['dbs'], $dbs);

			$coreStore->updateOrInsert($core);
		}

		if ($db) {
			$core = $db->fetchAll(
				"SELECT * FROM service_provider_core WHERE id = :id",
				Enum::FETCH_ASSOC,
				[
					"id" => "1",
				]
			);

			if (is_string($core[0]["settings"])) {
				$core[0]["settings"] = $helper->decode($core[0]["settings"], true);
			}

            $core[0]['settings']['dbs'] = array_merge($core[0]['settings']['dbs'], $dbs);

			$db->updateAsDict(
				'service_provider_core',
				[
					'settings' 	=> $helper->encode($core[0]["settings"]),
				],
				"id = 1"
			);
		}
	}
}