<?php

namespace System\Base\Loader;

use Phalcon\Loader;

class Service
{
	private static $mode;

	protected static $base;

	public static $loader;

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
		try {
			$config = include('../system/Configs/Base.php');
		} catch (\ErrorException $e) {
			throw new \Exception("Base.php file in configs directory missing");
		}

		if (isset($config['debug'])) {
			self::$mode = $config['debug'];
		} else {
			self::$mode = true;
		}

		self::$loader = new Loader();

		self::$loader->registerNamespaces($this->getNamespaces());

		self::$loader->registerClasses($this->getClasses());

		self::$loader->registerFiles($this->getFiles());

		self::$loader->register();
	}

	protected function getNamespaces()
	{
		if (self::$mode) {
			return
				array_merge(
					include(self::$base . 'system/Base/Loader/Namespaces.php'),
					include(self::$base . 'system/Base/Loader/ThirdParty/Namespaces.php'),
					include(self::$base . 'system/Base/Loader/Dev/Namespaces.php')
				);
		} else {
			return
				array_merge(
					include(self::$base . 'system/Base/Loader/Namespaces.php'),
					include(self::$base . 'system/Base/Loader/ThirdParty/Namespaces.php')
				);
		}
	}

	protected function getClasses()
	{
		if (self::$mode) {
			return
				array_merge(
					include(self::$base . 'system/Base/Loader/Classes.php'),
					include(self::$base . 'system/Base/Loader/ThirdParty/Classes.php'),
					include(self::$base . 'system/Base/Loader/Dev/Classes.php')
				);
		} else {
			return
				array_merge(
					include(self::$base . 'system/Base/Loader/Classes.php'),
					include(self::$base . 'system/Base/Loader/ThirdParty/Classes.php')
				);
		}
	}

	protected function getFiles()
	{
		if (self::$mode) {
			return
				array_merge(
					include(self::$base . 'system/Base/Loader/Files.php'),
					include(self::$base . 'system/Base/Loader/ThirdParty/Files.php'),
					include(self::$base . 'system/Base/Loader/Dev/Files.php')
				);
		} else {
			return
				array_merge(
					include(self::$base . 'system/Base/Loader/Files.php'),
					include(self::$base . 'system/Base/Loader/ThirdParty/Files.php')
				);
		}
	}

	public function addNamespaces(array $namespaces = [])
	{
		//
	}

	public function addClasses(array $classes = [])
	{
		//
	}

	public function addFiles(array $files = [])
	{
		//
	}
}