<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Repository
{
	public function register($db)
	{
		$db->insertAsDict(
			'repositories',
			[
				'name' 					=> 'Baz-Dev',
				'description' 			=> 'Bazaari Development Repository',
				'url'		 			=> 'https://dev.bazaari.com.au/api/v1/orgs/sp-dev/repos',
				'repo_provider'			=> 1,
				'auth_token'			=> 0,
				'username'				=> '',
				'password'				=> '',
				'token'					=> ''
			]
		);
	}
}