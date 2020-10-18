<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Repository
{
	public function register($db)
	{
		$db->insertAsDict(
			'repositories',
			[
				'name' 					=> 'Hello World Framework (h-w-f)',
				'description' 			=> 'Hello World Framework Repositories',
				'url'		 			=> 'https://api.github.com/orgs/h-w-f/repos',
				'need_auth'				=> 0,
				'username'				=> '',
				'token'					=> ''
			]
		);
	}
}