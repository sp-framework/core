<?php
// (new Phalcon\Support\Debug())->listen();
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
	require_once (__DIR__ . '/../system/Bootstrap.php');

	$bootstrap = new \System\Bootstrap;

	if (PHP_SAPI === 'cli') {
		$bootstrap->cli($argv);
	} else if (PHP_SAPI !== 'cli') {
		$bootstrap->mvc();
	}
} catch (throwable | Exception $exception) {
	if (isset($bootstrap->error)) {
		$bootstrap->error->handle($exception);
	} else {
		$class = (new \ReflectionClass($exception))->getShortName();

		if ($class === 'AppNotFoundException') {
			http_response_code(404);

			return;
		}

		$traces = [];

		foreach ($exception->getTrace() as $key => $trace) {
			$traces[] =
				isset($trace['file']) ?
				$trace['line'] . ' - ' . $trace['file'] . ' - Class: ' . $trace['function'] . ' - Function: ' . $trace['function'] :
				'Class: ' . $trace['function'] . ' - Function: ' . $trace['function'];
		}

		echo
			'<style type="text/css">
				.tg  {border-collapse:collapse;border-spacing:0;width:100%;}
				.tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
				  overflow:hidden;padding:10px 5px;word-break:normal;}
				.tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
				  font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
				.tg .tg-6kns{background-color:#fe0000;border-color:#333333;text-align:center;vertical-align:top}
				.tg .tg-orf0{font-family:"Arial Black", Gadget, sans-serif !important;;text-align:left;vertical-align:top}
				.tg .tg-0lax{text-align:left;vertical-align:top}
			</style>
			<table class="tg">
				<thead>
				<tr>
					<th class="tg-6kns" colspan="2">
						<span style="font-weight:bold; text-align:center;">Exception Thrown: "' . get_class($exception) . '"</span>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="tg-orf0"><span>Info</span></td>
					<td class="tg-0lax">' . $exception->getMessage() . '</td>
				</tr>
				<tr>
					<td class="tg-0lax"><span>File</span></td>
					<td class="tg-0lax">' . $exception->getFile() . '</td>
				</tr>
				<tr>
					<td class="tg-0lax"><span>Line</span></td>
					<td class="tg-0lax">' . $exception->getLine() . '</td>
				</tr>
				<tr>
					<td class="tg-0lax"><span>Trace</span></td>
					<td class="tg-0lax">' . join('<br>', array_reverse($traces)) . '</td>
				</tr>
			</tbody>
			</table>';
	}
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
