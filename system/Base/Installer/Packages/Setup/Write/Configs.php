<?php

namespace System\Base\Installer\Packages\Setup\Write;

class Configs
{
	public function write($container, $postData)
	{
		$configContent =
'<?php

return
	[
		"db" =>
			[
				"host" 		=> "' . $postData['host'] . '",
				"dbname" 	=> "' . $postData['database_name'] . '",
				"username" 	=> "' . $postData['username'] . '",
				"password" 	=> "' . $postData['password'] . '",
				"port" 		=> "' . $postData['port'] . '",
			]
	];';

		$container['localContent']->put('/system/Configs/Db.php', $configContent);

		if ($postData['mode'] === 'production') {
			$debug = "false";
			$cache = "true";
		} else if ($postData['mode'] === 'development') {
			$debug = "true";
			$cache = "false";
		}

		$baseContent =
'<?php

return
	[
		"debug"					=> ' . $debug . ', //true - Development false - Production
		"cache"					=> ' . $cache . ', //Global Cache value //true - Production false - Development
		"cacheTimeout"			=> 60, //Global Cache timeout in seconds
		"cacheService"			=> "streamCache"
	];';

		$container['localContent']->put('/system/Configs/Base.php', $baseContent);
	}
}