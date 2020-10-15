<?php

// require_once "../vendor/autoload.php";

// $config = array(
// 	// If defined, use specific profiler
// 	// otherwise use any profiler that's found
// 	// 'profiler' => \Xhgui\Profiler\Profiler::PROFILER_TIDEWAYS_XHPROF,

// 	// This allows to configure, what profiling data to capture
// 	'profiler.flags' => array(
// 		\Xhgui\Profiler\ProfilingFlags::CPU,
// 		\Xhgui\Profiler\ProfilingFlags::MEMORY,
// 		\Xhgui\Profiler\ProfilingFlags::NO_BUILTINS,
// 		\Xhgui\Profiler\ProfilingFlags::NO_SPANS,
// 	),

// 	// Saver to use.
// 	// Please note that 'pdo' and 'mongo' savers are deprecated
// 	// Prefer 'upload' or 'file' saver.
// 	'save.handler' => \Xhgui\Profiler\Profiler::SAVER_UPLOAD,

// 	// Saving profile data by upload is only recommended with HTTPS
// 	// endpoints that have IP whitelists applied.
// 	'save.handler.upload' => array(
// 		'uri' => 'http://phalcon.local/run/import',
// 		// The timeout option is in seconds and defaults to 3 if unspecified.
// 		'timeout' => 3,
// 		// the token must match 'upload.token' config in XHGui
// 		'token' => 'token',
// 	),
// 	// Environment variables to exclude from profiling data
// 	'profiler.exclude-env' => array(
// 		'APP_DATABASE_PASSWORD',
// 		'PATH',
// 	),

// 	'profiler.options' => array(
// 	),

// 	'profiler.enable' => function () {
// 		return true;
// 	},

// 	'profiler.simple_url' => function($url) {
// 		return preg_replace('/=\d+/', '', $url);
// 	},

// 	'profiler.replace_url' => function($url) {
// 		return str_replace('token', '', $url);
// 	},
// );

// try {
// 	$profiler = new \Xhgui\Profiler\Profiler($config);

// 	$profiler->start();

// } catch (Exception $e){
// 	echo "Profiler Errors: " . get_class($e) . "<br>";
// 	echo "Info: " . $e->getMessage() . "<br>";
// 	echo "File: " . $e->getFile() . "<br>";
// 	echo "Line: " . $e->getLine() . "<br>";
// 	exit;
// }

try {
	require_once "../system/bootstrap.php";
} catch (\Exception $e) {
	echo "Bootstrap Errors: " . get_class($e) . "<br>";
	echo "Info: " . $e->getMessage() . "<br>";
	echo "File: " . $e->getFile() . "<br>";
	echo "Line: " . $e->getLine() . "<br>";
	exit;
}

// try {

// 	$profiler_data = $profiler->disable();
// 	$profiler->save($profiler_data);
// 	echo '<br><br><a href="http://phalcon.local" target="_blank">Profiler</a>';

// } catch (Exception $e){
// 	echo "Profiler Errors: " . get_class($e) . "<br>";
// 	echo "Info: " . $e->getMessage() . "<br>";
// 	echo "File: " . $e->getFile() . "<br>";
// 	echo "Line: " . $e->getLine() . "<br>";
// 	exit;
// }
//
//
