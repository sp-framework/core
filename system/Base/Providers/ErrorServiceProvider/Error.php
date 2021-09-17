<?php

namespace System\Base\Providers\ErrorServiceProvider;

use System\Base\Providers\ErrorServiceProvider\ExceptionHandlers;

class Error
{
	protected $appInfo;

	protected $config;

	protected $logger;

	protected $request;

	protected $response;

	protected $exception;

	protected $appDebug;

	protected $class;

	protected $newException = null;

	public function __construct($appInfo, $config, $logger, $request, $response)
	{
		$this->appInfo = $appInfo;

		$this->config = $config;

		$this->logger = $logger;

		$this->request = $request;

		$this->response = $response;
	}

	public function init()
	{
		$this->appDebug = (bool) $this->config->debug;

		if ($this->appDebug) {
			error_reporting(-1);
		} else {
			error_reporting(0);
		}

		return $this;
	}

	public function handle(\Exception $exception)
	{
		$this->exception = $exception;

		$this->class = (new \ReflectionClass($this->exception))->getShortName();

		if ($this->config->logs->enabled) {
			if ($this->config->logs->email &&
				($this->class === 'Exception' || !$this->emailClass($this->class))
			) {
				$this->emailMessage();
			}

			$this->logMessage();
		}
		if ($this->class === 'DomainNotRegisteredException') {
			$this->showOnScreen();
			return;
		}

		$customHandler = $this->customHandler($this->class);

		if (!$customHandler) {
			if ($this->appDebug) {
				$this->showOnScreen();
			}
		} else {
			return $customHandler;
		}
	}

	public function customHandler()
	{
		$exceptionHandler = new ExceptionHandlers;

		if (method_exists($exceptionHandler, $method = "handle{$this->class}")) {
			return $exceptionHandler->{$method}($this->exception);
		} else {
			return false;
		}
	}

	protected function emailClass($class)
	{
		if (str_replace('Exception', '', $class) === 'Email') {
			return false;
		} else if ($class === 'Exception') {
			return true;
		}

		return true;
	}

	protected function logMessage()
	{
		$this->logger->log->emergency($this->buildMessage(false, false));

		if ($this->newException) {
			$this->logger->log->emergency($this->buildMessage(false, false, true));
		}

		$this->logger->commit();
	}

	protected function showOnScreen()
	{
		if ($this->config->debug) {
			echo $this->buildMessage();

			if ($this->newException) {
				echo '<br>' . $this->buildMessage(true, true, true);
			}
		}
	}

	protected function emailMessage()
	{
		try {
			$this->logger->commitEmail($this->buildMessage(true, false));

		} catch (\Exception $exception) {
			$this->newException = $exception;

			$this->class = 'EmailException';
		}
	}

	protected function buildMessage($template = true, $style = true, $new = false)
	{
		if ($new) {
			$exception = $this->newException;
		} else {
			$exception = $this->exception;
		}

		if (!$template) {
			return $exception->getMessage();
		}

		$traces = [];

		foreach ($exception->getTrace() as $key => $trace) {
			$traces[] =
				isset($trace['file']) ?
				$trace['line'] . ' - ' . $trace['file'] . ' - Class: ' . $trace['function'] . ' - Function: ' . $trace['function'] :
				'Class: ' . $trace['function'] . ' - Function: ' . $trace['function'];
		}

		if (!$style) {
			return $this->buildTable($traces, $exception);
		}

		return $this->buildStyle() . $this->buildTable($traces, $exception);
	}

	protected function buildStyle()
	{
		return
			'<style type="text/css">
				.tg  {border-collapse:collapse;border-spacing:0;width:100%;}
				.tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
				  overflow:hidden;padding:10px 5px;word-break:normal;}
				.tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
				  font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
				.tg .tg-6kns{background-color:#fe0000;border-color:#333333;text-align:center;vertical-align:top}
				.tg .tg-orf0{font-family:"Arial Black", Gadget, sans-serif !important;;text-align:left;vertical-align:top}
				.tg .tg-0lax{text-align:left;vertical-align:top}
			</style>';
	}

	protected function buildTable($traces, $exception)
	{
		return '
			<table class="tg" style="width:100%">
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