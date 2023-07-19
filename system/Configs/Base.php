<?php

return
	[
		"setup" 			=> true,
		"databasetype" 		=> "hybrid",
		"db"				=> [],
		"ff" 				=>
		[
			"databaseDir" 					=> ".ff/"
		],
		"cache"				=>
		[
			"enabled"						=> false, //Global Cache value //true - Production false - Development
			"timeout"						=> 60, //Global Cache timeout in seconds
			"service"						=> "streamCache"
		],
		"logs"				=>
		[
			"enabled"						=> true,
			"exceptions"					=> true,
			"level"							=> "DEBUG",
			"service"						=> "streamLogs",
			"emergencyLogsEmail"			=> false,
			"emergencyLogsEmailAddresses"	=> "",
		],
		"websocket"			=>
		[
			"protocol"						=> "tcp",
			"host"							=> "localhost",
			"port"							=> 5555
		]
	];