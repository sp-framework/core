<?php

namespace System\Base\Installer\Packages\Setup\Write;

use League\Flysystem\UnableToReadFile;
use Phalcon\Helper\Json;

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
			try {
				$this->coreJson['settings'] = include(base_path('system/Configs/Base.php'));
			} catch (\Exception $exception) {
				throw $exception;
			}
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

		if (isset($this->postData['dev']) && $this->postData['dev'] == 'false') {
			$debug = "false";
			$cache = "true";
			$logLevel = "INFO";
			$dev = "false";
		} else if (isset($this->postData['dev']) && $this->postData['dev'] == 'true') {
			$debug = "true";
			$cache = "false";
			$logLevel = "DEBUG";
			$dev = "true";
		} else {
			$debug = $this->coreJson['settings']['debug'] === true ? 'true' : 'false';
			$cache = $this->coreJson['settings']['cache']['enabled'] === true ? 'true' : 'false';
			$logLevel = $this->coreJson['settings']['logs']['level'] === true ? 'true' : 'false';
			$dev = $this->coreJson['settings']['dev'] === true ? 'true' : 'false';
		}
		$setup = 'false';

		$this->coreJson['settings']['setup'] = $setup === 'true'? true: false;
		$this->coreJson['settings']['debug'] = $debug === 'true'? true: false;
		$this->coreJson['settings']['cache']['enabled'] = $cache === 'true'? true: false;
		$this->coreJson['settings']['dev'] = $dev === 'true'? true: false;
		$this->coreJson['settings']['db'][$this->postData['database_name']]['host'] = $this->postData['host'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['dbname'] = $this->postData['database_name'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['username'] = $this->postData['username'];
		$this->postData['password'] = $this->container['crypt']->encryptBase64($this->postData['password'], $this->createDbKey());
		$this->coreJson['settings']['db'][$this->postData['database_name']]['password'] = $this->postData['password'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['port'] = $this->postData['port'];
		$this->coreJson['settings']['db'][$this->postData['database_name']]['charset'] = 'utf8mb4';
		$this->coreJson['settings']['logs']['level'] = $logLevel;
		$this->coreJson['settings']['security']['passwordWorkFactor'] = $pwf;

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

		try {
			$this->container['localContent']->write('/system/Configs/Base.php', $baseContent);
		} catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}

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

	private function createDbKey()
	{
		$keys[$this->postData['database_name']] = $this->container['random']->base58(4);

		try {
			$this->container['localContent']->write('system/.dbkeys', Json::encode($keys));
		} catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}

		return $keys[$this->postData['database_name']];
	}

	private function getDbKey()
	{
		try {
			$keys = $this->container['localContent']->read('system/.dbkeys');
		} catch (\ErrorException | FilesystemException | UnableToReadFile $exception) {
			throw $exception;
		}

		return Json::decode($keys, true)[$this->postData['database_name']];
	}
}