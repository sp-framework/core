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
			"charset" 	 	    => "utf8mb4"
		],
		"cache"			=>
		[
			"enabled"			=> false, //Global Cache value //true - Production false - Development
			"timeout"			=> 60, //Global Cache timeout in seconds
			"service"			=> "streamCache"
		],
		"security"		=>
		[
			"passwordWorkFactor"=> 8,
			"cookiesWorkFactor" => 4,
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