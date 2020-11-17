<?php

return
	[
		"debug"			=> false, //true - Development false - Production
		"db" 			=>
		[
			"host" 				=> "localhost",
			"dbname" 			=> "sp",
			"username" 			=> "guru",
			"password" 			=> "123",
			"port" 				=> "3306",
		],
		"cache"			=>
		[
			"enabled"			=> true, //Global Cache value //true - Production false - Development
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
		],
		"email"			=>
		[
			"enabled"			=> false,
			"encryption"	 	=> "",
			"allow_html_body"	=> "",
			"host"				=> "",
			"port"				=> "",
			"auth" 				=> "",
			"username"			=> "",
			"password"			=> ""
		]
	];