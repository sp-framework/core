<?php

return
	[
		"setup" 			=> false,
		"dev"    			=> false, //true - Development false - Production
		"debug"				=> false,
		"db" 				=>
		[
			"host" 							=> "localhost",
			"dbname" 						=> "sp",
			"username" 						=> "sp",
			"password" 						=> "123",
			"port" 							=> "3306",
			"charset" 	 	    			=> "utf8mb4"
		],
		"cache"				=>
		[
			"enabled"						=> true, //Global Cache value //true - Production false - Development
			"timeout"						=> 60, //Global Cache timeout in seconds
			"service"						=> "streamCache"
		],
		"security"			=>
		[
			"passwordWorkFactor"			=> 8,
			"cookiesWorkFactor" 			=> 4,
		],
		"logs"				=>
		[
			"enabled"						=> true,
			"exceptions"					=> true,
			"level"							=> "INFO",
			"service"						=> "streamLogs",
			"emergencyEmailLogs"			=> false,
			"emergencyLogsEmailAddresses"	=> "",
		],
		"websocket"			=>
		[
			"protocol"						=> "tcp",
			"host"							=> "localhost",
			"port"							=> 5555
		]
	];