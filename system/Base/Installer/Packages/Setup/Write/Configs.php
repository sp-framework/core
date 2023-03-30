<?php

namespace System\Base\Installer\Packages\Setup\Write;

class Configs
{
	protected $container;

	protected $postData;

	protected $coreJson;

	public function __construct($container, $postData, $coreJson = null)
	{
		$this->container = $container;

		$this->postData = $postData;

		$this->coreJson = $coreJson;
	}

	public function write()
	{
		return $this->writeBaseConfig();
	}

	public function revert()
	{
		return $this->writeBaseConfig(true);
	}

	protected function writeBaseConfig($revert = false)
	{
		if (!$this->coreJson) {
			$this->coreJson['settings'] = include(base_path('system/Configs/Base.php'));
		}

		if (isset($this->postData['pwf']) &&
			isset($this->postData['cwf']) &&
			$this->postData['auto-encrypt-level'] === 'false'
		) {
			$pwf = $this->postData['pwf'];
			$cwf = $this->postData['cwf'];
		} else if (
			(isset($this->coreJson['settings']['security']['passwordWorkFactor']) &&
			 is_int($this->coreJson['settings']['security']['passwordWorkFactor'])
			) &&
			(isset($this->coreJson['settings']['security']['cookiesWorkFactor']) &&
			 is_int($this->coreJson['settings']['security']['cookiesWorkFactor'])
			)
		) {
			$pwf = $this->coreJson['settings']['security']['passwordWorkFactor'];
			$cwf = $this->coreJson['settings']['security']['cookiesWorkFactor'];
		} else {
			$workFactor = (int) $this->getWorkFactor();

			$pwf = floor($workFactor * 2);
			$cwf = floor($workFactor);
		}

		if ($revert) {
			$this->postData['username'] = '';
		}

		if ($this->postData['dev'] == 'false') {
			$debug = "false";
			$cache = "true";
			$logLevel = "INFO";
			$dev = "false";
		} else if ($this->postData['dev'] == 'true') {
			$debug = "true";
			$cache = "false";
			$logLevel = "DEBUG";
			$dev = "true";
		}
		$setup = 'false';

		$baseContent =
'<?php

return
	[
		"setup" 			=> ' . $setup .',
		"dev"    			=> ' . $dev . ', //true - Development false - Production
		"debug"				=> ' . $debug . ',
		"db" 				=>
		[
			"host" 							=> "' . $this->postData['host'] . '",
			"dbname" 						=> "' . $this->postData['database_name'] . '",
			"username" 						=> "' . $this->postData['username'] . '",
			"password" 						=> "' . $this->postData['password'] . '",
			"port" 							=> "' . $this->postData['port'] . '",
			"charset" 	 	    			=> "utf8mb4"
		],
		"cache"				=>
		[
			"enabled"						=> ' . $cache . ', //Global Cache value //true - Production false - Development
			"timeout"						=> ' . $this->coreJson['settings']['cache']['timeout'] . ', //Global Cache timeout in seconds
			"service"						=> "' . $this->coreJson['settings']['cache']['service'] . '"
		],
		"security"			=>
		[
			"passwordWorkFactor"			=> ' . $pwf . ',
			"cookiesWorkFactor" 			=> ' . $cwf . ',
		],
		"logs"				=>
		[
			"enabled"						=> true,
			"exceptions"					=> true,
			"level"							=> "' . $logLevel . '",
			"service"						=> "' . $this->coreJson['settings']['logs']['service'] . '",
			"emergencyEmailLogs"			=> false,
			"emergencyLogsEmailAddresses"	=> "' . $this->coreJson['settings']['logs']['emergencyLogsEmailAddresses'] . '",
		],
		"websocket"			=>
		[
			"protocol"						=> "tcp",
			"host"							=> "localhost",
			"port"							=> 5555
		]
	];';

		$this->container['localContent']->write('/system/Configs/Base.php', $baseContent);

		$this->coreJson['settings']['debug'] = $debug === 'true'? true: false;
		$this->coreJson['settings']['setup'] = $setup === 'true'? true: false;
		$this->coreJson['settings']['dev'] = $dev === 'true'? true: false;
		$this->coreJson['settings']['db'][$this->postData['database_name']]['host'] = $this->postData['host'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['dbname'] = $this->postData['database_name'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['username'] = $this->postData['username'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['password'] = $this->postData['password'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['port'] = $this->postData['port'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['charset'] = 'utf8mb4';
		$this->coreJson['settings']['cache']['enabled'] = $cache === 'true'? true: false;
		$this->coreJson['settings']['logs']['level'] = $logLevel;
		$this->coreJson['settings']['security']['passwordWorkFactor'] = $pwf;

		return $this->coreJson;
	}

	protected function getWorkFactor()
	{
		for ($workFactor = 4; $workFactor <= 16 ; $workFactor ++) {
			$timeStart = $this->microtimeFloat();

			$this->container['security']->hash(rand(), $workFactor);

			$timeEnd = $this->microtimeFloat();

			$time = $timeEnd - $timeStart;

			if ($time < 0.100) {
				return $workFactor;
			}
		}
	}

	protected function microtimeFloat()
	{
		list($usec, $sec) = explode(" ", microtime());

		return ((float)$usec + (float)$sec);
	}
}