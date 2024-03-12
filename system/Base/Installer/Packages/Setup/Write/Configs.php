<?php

namespace System\Base\Installer\Packages\Setup\Write;

use League\Flysystem\UnableToReadFile;

class Configs
{
	protected $container;

	protected $postData;

	protected $coreJson;

	protected $baseFileContent;

	public function __construct($container, $postData, $coreJson = null)
	{
		$this->container = $container;

		$this->postData = $postData;

		$this->coreJson = $coreJson;
	}

	public function write($writeBaseFile = false)
	{
		if ($writeBaseFile) {
			$this->writeBaseFile();

			return $this->coreJson;
		}

		$this->writeBaseConfig();

		return $this->coreJson;
	}

	public function revert()
	{
		return $this->writeBaseConfig(true);
	}

	protected function writeBaseConfig($revert = false)
	{
		if ($revert) {
			$this->baseFileContent =
'<?php

return
	[
		"setup" 			=> true,
		"databasetype" 		=> "hybrid",
		"db"				=> [],
		"ff" 				=>
		[
			"databaseDir" 					=> "sp/"
		],
		"cache"				=>
		[
			"enabled"						=> false, //Global Cache value //true - Production false - Development
			"timeout"						=> 60, //Global Cache timeout in seconds
			"service"						=> "streamCache"
		],
		"security"			=>
		[
			"sso"							=> false
		],
		"logs"				=>
		[
			"enabled"						=> true,
			"exceptions"					=> true,
			"level"							=> "DEBUG",
			"service"						=> "streamLogs",
			"emergencyLogsEmail"			=> false,
			"emergencyLogsEmailAddresses"	=> ""
		],
		"websocket"			=>
		[
			"protocol"						=> "tcp",
			"host"							=> "localhost",
			"port"							=> 5555
		],
		"timeout"			=>
		[
			"cookies"						=> 86400
		]
	];';
			$this->writeBaseFile();

			return;
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
			$logsEnabled = "true";
			$logsExceptions = "true";
			$logLevel = "INFO";
			$logsEmail = "false";
			$dev = "false";
		} else if (isset($this->postData['dev']) && $this->postData['dev'] == 'true') {
			$debug = "true";
			$cache = "false";
			$logsEnabled = "true";
			$logsExceptions = "false";
			$logLevel = "DEBUG";
			$logsEmail = "true";
			$dev = "true";
		} else {
			$debug = $this->coreJson['settings']['debug'] == 'true' ? 'true' : 'false';
			$cache = $this->coreJson['settings']['cache']['enabled'] == 'true' ? 'true' : 'false';
			$logsEnabled = $this->coreJson['settings']['logs']['enabled'] == 'true' ? 'true' : 'false';
			$logsExceptions = $this->coreJson['settings']['logs']['exceptions'] == 'true' ? 'true' : 'false';
			$logLevel = $this->coreJson['settings']['logs']['level'] == 'true' ? 'true' : 'false';
			$logsEmail = $this->coreJson['settings']['logs']['emergencyLogsEmail'] == 'true' ? 'true' : 'false';
			$dev = $this->coreJson['settings']['dev'] == 'true' ? 'true' : 'false';
		}
		$setup = 'false';
		$sso = 'false';

		$this->coreJson['settings']['setup'] = $setup == 'true' ? true : false;
		$this->coreJson['settings']['debug'] = $debug == 'true' ? true : false;
		$this->coreJson['settings']['cache']['enabled'] = $cache == 'true' ? true : false;
		$this->coreJson['settings']['dev'] = $dev == 'true' ? true : false;
		$this->coreJson['settings']['databasetype'] = $this->postData['databasetype'] ?? $this->coreJson['settings']['databasetype'];
		if ($this->coreJson['settings']['databasetype'] !== 'ff') {
			$this->coreJson['settings']['dbs'][$this->postData['dbname']]['active'] = true;
			$this->coreJson['settings']['dbs'][$this->postData['dbname']]['host'] = $this->postData['host'];
			$this->coreJson['settings']['dbs'][$this->postData['dbname']]['dbname'] = $this->postData['dbname'];
			$this->coreJson['settings']['dbs'][$this->postData['dbname']]['username'] = $this->postData['username'];
			$this->postData['password'] = $this->container['crypt']->encryptBase64($this->postData['password'], $this->createDbKey());
			$this->coreJson['settings']['dbs'][$this->postData['dbname']]['password'] = $this->postData['password'];
			$this->coreJson['settings']['dbs'][$this->postData['dbname']]['port'] = $this->postData['port'];
			$this->coreJson['settings']['dbs'][$this->postData['dbname']]['charset'] = $this->postData['charset'];
			$this->coreJson['settings']['dbs'][$this->postData['dbname']]['collation'] = $this->postData['collation'];
		}
		$this->coreJson['settings']['logs']['level'] = $logLevel;
		$this->coreJson['settings']['security']['sso'] = $sso;
		$this->coreJson['settings']['security']['passwordWorkFactor'] = $pwf;
		$this->coreJson['settings']['security']['cookiesWorkFactor'] = $cwf;

		$this->baseFileContent =
'<?php

return
	[
		"setup" 			=> ' . $setup .',
		"dev"    			=> ' . $dev . ', //true - Development false - Production
		"debug"				=> ' . $debug . ',
		"auto_off_debug"	=> ' . $this->coreJson['settings']['auto_off_debug'] . ',
		"databasetype" 		=> "' . $this->coreJson['settings']['databasetype'] . '",';
if ($this->coreJson['settings']['databasetype'] === 'hybrid') {
		$this->baseFileContent .= '
		"db" 				=>
		[
			"host" 							=> "' . $this->postData['host'] . '",
			"port" 							=> "' . $this->postData['port'] . '",
			"dbname" 						=> "' . $this->postData['dbname'] . '",
			"charset" 	 	    			=> "' . $this->postData['charset'] . '",
			"collation" 	    			=> "' . $this->postData['collation'] . '",
			"username" 						=> "' . $this->postData['username'] . '",
			"password" 						=> "' . $this->postData['password'] . '"
		],
		"ff" 				=>
		[
			"databaseDir" 					=> "' . $this->coreJson['settings']['ffs']['sp']['databaseDir'] . '"
		],';
} else if ($this->coreJson['settings']['databasetype'] === 'ff') {
		$this->baseFileContent .= '
		"ff" 				=>
		[
			"databaseDir" 					=> "' . $this->coreJson['settings']['ffs']['sp']['databaseDir'] . '"
		],';
} else if ($this->coreJson['settings']['databasetype'] === 'db') {
		$this->baseFileContent .= '
		"db" 				=>
		[
			"host" 							=> "' . $this->postData['host'] . '",
			"port" 							=> "' . $this->postData['port'] . '",
			"dbname" 						=> "' . $this->postData['dbname'] . '",
			"charset" 	 	    			=> "' . $this->postData['charset'] . '",
			"collation" 	    			=> "' . $this->postData['collation'] . '",
			"username" 						=> "' . $this->postData['username'] . '",
			"password" 						=> "' . $this->postData['password'] . '"
		],';
}
		$this->baseFileContent .= '
		"cache"				=>
		[
			"enabled"						=> ' . $cache . ', //Global Cache value //true - Production false - Development
			"timeout"						=> ' . $this->coreJson['settings']['cache']['timeout'] . ', //Global Cache timeout in seconds
			"service"						=> "' . $this->coreJson['settings']['cache']['service'] . '"
		],
		"security"			=>
		[
			"sso"							=> ' . $sso . ',
			"passwordWorkFactor"			=> ' . $pwf . ',
			"cookiesWorkFactor" 			=> ' . $cwf . ',
		],
		"logs"				=>
		[
			"enabled"						=> "' . $logsEnabled .'",
			"exceptions"					=> "' . $logsExceptions .'",
			"level"							=> "' . $logLevel . '",
			"service"						=> "streamLogs",
			"emergencyLogsEmail"			=> "' . $logsEmail . '",
			"emergencyLogsEmailAddresses"	=> "' . $this->coreJson['settings']['logs']['emergencyLogsEmailAddresses'] . '",
		],
		"websocket"			=>
		[
			"protocol"						=> "' . $this->coreJson['settings']['websocket']['protocol'] . '",
			"host"							=> "' . $this->coreJson['settings']['websocket']['host'] . '",
			"port"							=> ' . $this->coreJson['settings']['websocket']['port'] . '
		],
		"timeout"			=>
		[
			"cookies"						=> ' . $this->coreJson['settings']['timeout']['cookies'] . '
		]
	];';

		return $this->coreJson;
	}

	protected function writeBaseFile()
	{
		if (!$this->baseFileContent) {
			$this->writeBaseConfig();
		}

		try {
			$this->container['localContent']->write('/system/Configs/Base.php', $this->baseFileContent);
		} catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}
	}

	protected function getWorkFactor()
	{
		for ($workFactor = 4; $workFactor <= 16 ; $workFactor ++) {
			$timeStart = $this->microtimeFloat();

			$this->container['security']->hash(rand(), ['cost' => $workFactor]);

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
		$keys[$this->postData['dbname']] = $this->container['random']->base58(4);

		try {
			$this->container['localContent']->write('system/.dbkeys', $this->container['helper']->encode($keys));
		} catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}

		return $keys[$this->postData['dbname']];
	}

	private function getDbKey()
	{
		try {
			$keys = $this->container['localContent']->read('system/.dbkeys');
		} catch (\ErrorException | FilesystemException | UnableToReadFile $exception) {
			throw $exception;
		}

		return $this->helper->decode($keys, true)[$this->postData['dbname']];
	}
}