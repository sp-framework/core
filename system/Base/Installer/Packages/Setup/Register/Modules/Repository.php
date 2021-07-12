<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

class Repository
{
	public function register($db)
	{
		$db->insertAsDict(
			'modules_repositories',
			[
				'name' 					=> 'Bazaari Core (SP)',
				'description' 			=> 'Bazaari Core Repository v3',
				'repo_url'				=> 'https://repo.bazaari.com.au/api/v1/orgs/sp-core/repos',
				'site_url'				=> 'https://repo.bazaari.com.au/',
				'branch'				=> 'master',
				'repo_provider'			=> 1,
				'auth_token'			=> 0,
				'username'				=> '',
				'password'				=> '',
				'token'					=> ''
			]
		);

		$db->insertAsDict(
			'modules_repositories',
			[
				'name' 					=> 'Bazaari Modules (SP)',
				'description' 			=> 'Bazaari Modules Repository v3',
				'repo_url'				=> 'https://repo.bazaari.com.au/api/v1/orgs/sp-modules/repos',
				'site_url'				=> 'https://repo.bazaari.com.au/',
				'branch'				=> 'master',
				'repo_provider'			=> 1,
				'auth_token'			=> 0,
				'username'				=> '',
				'password'				=> '',
				'token'					=> ''
			]
		);
	}
}