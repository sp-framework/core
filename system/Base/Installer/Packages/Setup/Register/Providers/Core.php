<?php

namespace System\Base\Installer\Packages\Setup\Register\Providers;

class Core
{
	public function register($baseConfig, $db)
	{
		$db->insertAsDict(
			'service_provider_core',
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
			]
		);
	}
}