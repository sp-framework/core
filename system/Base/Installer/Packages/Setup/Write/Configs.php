<?php

namespace System\Base\Installer\Packages\Setup\Write;

class Configs
{
	public function write($container, $postData)
	{
		$this->writeDbConfig($container, $postData);
		$this->writeBaseConfig($container, $postData);
	}

	protected function writeDbConfig($container, $postData)
	{
		$dbContent =
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

		$container['localContent']->put('/system/Configs/Db.php', $dbContent);
	}

	protected function writeBaseConfig($container, $postData)
	{
		if ($postData['mode'] === 'production') {
			$debug = "false";
			$cache = "true";
			$logLevel = "INFO";
		} else if ($postData['mode'] === 'development') {
			$debug = "true";
			$cache = "false";
			$logLevel = "DEBUG";
		}

		$baseContent =
'<?php

return
	[
		"debug"					=> ' . $debug . ', //true - Development false - Production
		"cache"					=>
		[
			"enabled"			=> ' . $cache . ', //Global Cache value //true - Production false - Development
			"timeout"			=> 60, //Global Cache timeout in seconds
			"service"			=> "streamCache"
		],
		"logs"					=>
		[
			"service"			=> "streamLogs", //streamLogs (/var/log/debug.log) OR dbLogs (table = logs)
			"level"				=> "' . $logLevel . '",
		]
	];';

		$container['localContent']->put('/system/Configs/Base.php', $baseContent);
	}
}