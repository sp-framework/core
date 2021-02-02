<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

class Repository
{
	public function register($db)
	{
		$db->insertAsDict(
			'modules_repositories',
			[
				// 'name' 					=> 'Baz-Dev',
				// 'description' 			=> 'Bazaari Development Repository',
				// 'repo_url'				=> 'https://dev.bazaari.com.au/api/v1/orgs/sp-dev/repos',
				// 'site_url'				=> 'https://dev.bazaari.com.au/',
				// 'branch'					=> 'master',
				// 'repo_provider'			=> 1,
				// 'auth_token'				=> 0,
				// 'username'				=> '',
				// 'password'				=> '',
				// 'token'					=> ''
				'name' 					=> 'Gitea Local',
				'description' 			=> 'Gitea Local Repository',
				'repo_url'				=> 'http://gitea.local:3000/api/v1/orgs/ecom-dashboard/repos',
				'site_url'				=> 'http://gitea.local:3000/',
				'branch'				=> 'master',
				'repo_provider'			=> 1,
				'auth_token'			=> 1,
				'username'				=> 'guru',
				'password'				=> '123123',
				'token'					=> ''
			]
		);
	}
}