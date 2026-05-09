<?php

return
	[
		"setup" 			=> false,
		"dev"    			=> true, //true - Development false - Production
		"debug"				=> true,
		"auto_off_debug"	=> 0,
		"databasetype" 		=> "ff",
		"ff" 				=>
		[
			"databaseDir" 					=> "btc_local"
		],
		"cache"				=>
		[
			"enabled"						=> false, //Global Cache value //true - Production false - Development
			"timeout"						=> 60, //Global Cache timeout in seconds
			"service"						=> "streamCache"
		],
		"security"			=>
		[
			"sso"							=> false,
			"passwordWorkFactor"			=> 2,
			"cookiesWorkFactor" 			=> 2,
			"passwordPolicy"     			=> false,
		],
		"logs"				=>
		[
			"enabled"						=> "true",
			"exceptions"					=> "false",
			"level"							=> "DEBUG",
			"service"						=> "streamLogs",
			"emergencyLogsEmail"			=> "true",
			"emergencyLogsEmailAddresses"	=> "",
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
	];