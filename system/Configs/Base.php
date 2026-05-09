<?php

return
	[
		"setup" 			=> true,
		"cache"				=>
		[
			"enabled"						=> false, //Global Cache value //true - Production false - Development
			"timeout"						=> 60, //Global Cache timeout in seconds
			"service"						=> "streamCache"
		],
		"logs"				=>
		[
			"enabled"						=> "false",
			"exceptions"					=> "false",
			"level"							=> "DEBUG",
			"service"						=> "streamLogs",
			"emergencyLogsEmail"			=> "false",
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