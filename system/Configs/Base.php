<?php

return
	[
		"setup" 			=> false,
		"dev"    			=> true, //true - Development false - Production
		"debug"				=> true,
		"auto_off_debug"	=> 0,
		"databasetype" 		=> "hybrid",
		"db" 				=>
		[
			"host" 							=> "localhost",
			"port" 							=> "3306",
			"dbname" 						=> "sp",
			"charset" 	 	    			=> "utf8mb4",
			"collation" 	    			=> "utf8mb4_general_ci",
			"username" 						=> "sp",
			"password" 						=> "r2czMyUAuNPDN3FSfdM7zT+i+LnywKIZzqewh9E9nyCOn1o7gNnAfralTC3jlqou1fqU"
		],
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
			"passwordWorkFactor"			=> 2,
			"cookiesWorkFactor" 			=> 2,
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
		]
	];