<?php

namespace System\Base\Installer\Packages\Setup\Write;

class Configs
{
	public function write($container, $postData, $coreJson)
	{
		$this->writeBaseConfig($container, $postData, $coreJson);
	}

	protected function writeBaseConfig($container, $postData, $coreJson)
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
		"debug"			=> ' . $debug . ', //true - Development false - Production
		"db" 			=>
		[
			"host" 				=> "' . $postData['host'] . '",
			"dbname" 			=> "' . $postData['database_name'] . '",
			"username" 			=> "' . $postData['username'] . '",
			"password" 			=> "' . $postData['password'] . '",
			"port" 				=> "' . $postData['port'] . '",
		],
		"cache"			=>
		[
			"enabled"			=> ' . $cache . ', //Global Cache value //true - Production false - Development
			"timeout"			=> ' . $coreJson['settings']['cache']['timeout'] . ', //Global Cache timeout in seconds
			"service"			=> "' . $coreJson['settings']['cache']['service'] . '"
		],
		"logs"			=>
		[
			"enabled"			=> true,
			"email"				=> false,
			"service"			=> "' . $coreJson['settings']['logs']['service'] . '", //streamLogs (/var/log/debug.log) OR dbLogs (table = logs)
			"level"				=> "' . $logLevel . '",
			"emergencyEmails"	=> "' . $coreJson['settings']['logs']['emergencyEmails'] . '",
		]
	];';

		$container['localContent']->put('/system/Configs/Base.php', $baseContent);
	}
}