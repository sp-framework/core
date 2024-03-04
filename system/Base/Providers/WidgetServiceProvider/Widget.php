<?php

namespace System\Base\Providers\WidgetServiceProvider;

class Widget
{
	protected $session;

	protected $connection;

	protected $request;

	protected $remoteWebContent;

	protected $logger;

	public function __construct($session, $connection, $request, $remoteWebContent, $logger)
	{
		$this->session = $session;

		$this->connection = $connection;

		$this->request = $request;

		$this->remoteWebContent = $remoteWebContent;

		$this->logger = $logger;
	}

	public function init()
	{
		return $this;
	}

	public function get(string $route)
	{
		$widgetRoute =
			$this->request->getScheme() . '://' .
			$this->request->getHttpHost() . '/' . $route . '/q/layout/0/';
			// '/ses/0/' . $this->session->getId() . '/' . $this->connection->getId();

		$this->logger->log->debug('Making widget connection for connection ID: ' . $this->connection->getId());

		return $this->remoteWebContent->get($widgetRoute)->getBody()->getContents();
	}
}