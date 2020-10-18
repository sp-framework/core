<?php

namespace System\Base\Loader;

use Phalcon\Loader;

class Service
{
	private static $mode;

	protected static $base;

	/**
	 * @var null|\System\Base\Service Singleton instance.
	 */
	protected static $instance = null;

	public static function Instance($base = null)
	{
		self::$base = $base;

		if (self::$instance === null) {

			self::$instance = new self($base);
		}

		return self::$instance;
	}

	public function load()
	{
		include('../system/Base/Helpers.php');

		// try {
			$config = include('../system/Configs/Base.php');
		// } catch (\ErrorException $e) {
		// 	throw new \Exception("Base.php file in configs directory missing");
		// }

		if (isset($config['debug'])) {
			self::$mode = $config['debug'];
		} else {
			self::$mode = true;
		}

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