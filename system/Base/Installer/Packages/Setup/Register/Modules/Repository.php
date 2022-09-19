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
				'description' 			=> 'Bazaari Core Repository',
				'repo_url'				=> 'https://dev.bazaari.com.au/api/v1/orgs/sp-core/repos',
				'site_url'				=> 'https://dev.bazaari.com.au/',
				'branch'				=> 'master',
				'repo_provider'			=> 1,//Gitea
				'auth_token'			=> 2,
				'username'				=> '',
				'password'				=> '',
				'token'					=> 'a782b559b0d5f5b747a6b67bd576f84b2e3fbde7'//bcust Token
			]
		);

		$db->insertAsDict(
			'modules_repositories',
			[
				'name' 					=> 'Bazaari Modules (SP)',
				'description' 			=> 'Bazaari Modules Repository',
				'repo_url'				=> 'https://dev.bazaari.com.au/api/v1/orgs/sp-modules/repos',
				'site_url'				=> 'https://dev.bazaari.com.au/',
				'branch'				=> 'master',
				'repo_provider'			=> 1,
				'auth_token'			=> 2,
				'username'				=> '',
				'password'				=> '',
				'token'					=> 'a782b559b0d5f5b747a6b67bd576f84b2e3fbde7'//bcust Token
			]
		);
	}
}