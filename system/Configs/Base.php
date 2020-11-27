<?php

return
	[
		"debug"			=> true, //true - Development false - Production
		"db" 			=>
		[
			"host" 				=> "localhost",
			"dbname" 			=> "sp",
			"username" 			=> "guru",
			"password" 			=> "123",
			"port" 				=> "3306",
			"charset" 	 	    => "utf8"
		],
		"cache"			=>
		[
			"enabled"			=> false, //Global Cache value //true - Production false - Development
			"timeout"			=> 60, //Global Cache timeout in seconds
			"service"			=> "streamCache"
		],
		"logs"			=>
		[
			"enabled"			=> true,
			"level"				=> "DEBUG",
			"service"			=> "streamLogs", //streamLogs (/var/log/debug.log) OR dbLogs (table = logs)
			"email"				=> false,
			"emergencyEmails"	=> "",
		]
	];