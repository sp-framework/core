<?php

namespace System\Base\Loader;

use Phalcon\Di\DiInterface;
use Phalcon\Loader;

class Service
{
	private static $mode;

	private static $container;

	protected static $base;

	/**
	 * @var null|\System\Base\Service Singleton instance.
	 */
	protected static $instance = null;

	public static function Instance(DiInterface $container, $base = null)
	{
		self::$container = $container;

		self::$base = $base;

		if (self::$instance === null) {

			self::$instance = new self(self::$container, $base);
		}

		return self::$instance;
	}

	public function load()
	{
		try {
			$config = include('../system/Configs/Base.php');
		} catch (Exception $e) {
			throw $e;
		}

		self::$mode = $config['debug'];

		$files = include(self::$base . 'system/Base/Loader/Files.php');

		$loader = new Loader();

		$loader->registerNamespaces($this->getNamespaces());

		$loader->registerFiles($this->getFiles());

		$loader->register();
	}

	protected function getNamespaces()
	{
		if (self::$mode) {
			$dev = include(self::$base . 'system/Base/Loader/Dev.php');
			return
				array_merge(
					include(self::$base . 'system/Base/Loader/Namespaces.php'),
					$dev['namespaces']
				);
		} else {
			return include(self::$base . 'system/Base/Loader/Namespaces.php');
		}
	}

	protected function getFiles()
	{
		if (self::$mode) {
			$dev = include(self::$base . 'system/Base/Loader/Dev.php');
			return
				array_merge(
					include(self::$base . 'system/Base/Loader/Files.php'),
					$dev['files']
				);
		} else {
			return include(self::$base . 'system/Base/Loader/Files.php');
		}
	}

	public function addNamespaces(array $namespaces = [])
	{
		//
	}

	public function addFiles(array $files = [])
	{
		//
	}
}