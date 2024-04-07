<?php

try {
	require_once (__DIR__ . '/../system/Bootstrap.php');

	$bootstrap = new \System\Bootstrap;

	if (PHP_SAPI === 'cli') {
		$bootstrap->cli($argv);
	} else if (PHP_SAPI !== 'cli') {
		$bootstrap->mvc();
	}
} catch (throwable | Exception $exception) {
	if (isset($bootstrap->isApi) && $bootstrap->isApi === true) {
		http_response_code(500);

		if ($bootstrap->config->debug) {
			(new \System\Base\Providers\ErrorServiceProvider\MicroExceptionHandler())->init($exception, $bootstrap->logger, $bootstrap->response);
		} else {
			echo json_encode(['responseMessage' => 'Error! Please contact Administrator.', 'responseCode' => 1]);
		}

		exit;
	} else {
		if (isset($bootstrap->error)) {
			$bootstrap->error->handle($exception);
		} else {
			if ($bootstrap->config && $bootstrap->config->debug) {
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
			} else {
				throw $exception;
			}
		}
	}
}