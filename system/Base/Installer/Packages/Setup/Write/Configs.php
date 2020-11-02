<?php

namespace System\Base\Installer\Packages\Setup\Write;

class Configs
{
	public function write($container, $postData, $coreJson)
	{
		return $this->writeBaseConfig($container, $postData, $coreJson);
	}

	public function revert($container, $postData, $coreJson)
	{
		return $this->writeBaseConfig($container, $postData, $coreJson, true);
	}

	protected function writeBaseConfig($container, $postData, $coreJson, $revert = false)
	{
		if ($revert) {
			$postData['username'] = '';
		}

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
			"level"				=> "' . $logLevel . '",
			"service"			=> "' . $coreJson['settings']['logs']['service'] . '", //streamLogs (/var/log/debug.log) OR dbLogs (table = logs)
			"email"				=> false,
			"emergencyEmails"	=> "' . $coreJson['settings']['logs']['emergencyEmails'] . '",
		],
		"email"			=>
		[
			"enabled"			=> false,
			"encryption"	 	=> "",
			"allow_html_body"	=> "",
			"host"				=> "",
			"port"				=> "",
			"auth" 				=> "",
			"username"			=> "",
			"password"			=> ""
		]
	];';

		$container['localContent']->put('/system/Configs/Base.php', $baseContent);

		$coreJson['settings']['debug'] = $debug;
		$coreJson['settings']['db']['host'] = $postData['host'];
		$coreJson['settings']['db']['dbname'] = $postData['database_name'];
		$coreJson['settings']['db']['username'] = $postData['username'];
		$coreJson['settings']['db']['password'] = $postData['password'];
		$coreJson['settings']['db']['port'] = $postData['port'];
		$coreJson['settings']['cache']['enabled'] = $cache;
		$coreJson['settings']['logs']['level'] = $logLevel;

		return $coreJson;
	}
}