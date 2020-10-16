<?php

return
	[
		"debug"					=> true, //true - Development false - Production
		"cache"					=>
		[
			"enabled"			=> false, //Global Cache value //true - Production false - Development
			"timeout"			=> 60, //Global Cache timeout in seconds
			"service"			=> "streamCache"
		],
		"logs"					=>
		[
			"service"			=> "dbLogs", //streamLogs (/var/log/debug.log) OR dbLogs (table = logs)
			"level"				=> "INFO"
		]
	];