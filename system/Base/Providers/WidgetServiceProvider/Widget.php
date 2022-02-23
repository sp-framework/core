<?php

namespace System\Base\Providers\WidgetServiceProvider;

class Widget
{
	protected $connection;

	protected $request;

	protected $remoteContent;

	protected $logger;

	public function __construct($session, $connection, $request, $remoteContent, $logger)
	{
		$this->session = $session;

		$this->connection = $connection;

		$this->request = $request;

		$this->remoteContent = $remoteContent;

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

		return $this->remoteContent->get($widgetRoute)->getBody()->getContents();
	}
}