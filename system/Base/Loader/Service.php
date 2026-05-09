<?php

namespace System\Base\Loader;

use Phalcon\Autoload\Loader;

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

	public function load($argv = null)
	{
		//Get Domain Specific Config
		try {
			if (PHP_SAPI === 'cli') {
				if (isset($argv[3]) && $argv[3] !== '') {
					$domain = explode('=', $argv[3]);

					if (isset($domain[0]) && $domain[0] === 'domain' && isset($domain[1])) {
						$config = include(__DIR__ . '/../../../system/Configs/' . ucfirst($domain[1]) . '.php');
					}
				}
			} else {
				$config = include(__DIR__ . '/../../../system/Configs/' . ucfirst($_SERVER['HTTP_HOST']) . '.php');
			}
		} catch (\ErrorException $e) {
			//Try Base Config
			try {
				$config = include(__DIR__ . '/../../../system/Configs/Base.php');
			} catch (\ErrorException $e) {
				throw new \Exception("Base.php file is missing in Configs directory");
			}
		}

		if (isset($config['debug'])) {
			self::$mode = $config['debug'];
		} else {
			self::$mode = true;
		}

		self::$loader = new Loader();

		self::$loader->setNamespaces($this->getNamespaces());

		self::$loader->setClasses($this->getClasses());

		self::$loader->setFiles($this->getFiles());

		self::$loader->register();
	}

	protected function getNamespaces()
	{
		if (self::$mode) {
			return
				array_merge(
					$this->namespaces(),
					$this->externalNamespaces(),
					$this->devNamespaces()
				);
		} else {
			return
				array_merge(
					$this->namespaces(),
					$this->externalNamespaces()
				);
		}
	}

	protected function getClasses()
	{
		if (self::$mode) {
			return
				array_merge(
					$this->classes(),
					$this->externalClasses(),
					$this->devClasses()
				);
		} else {
			return
				array_merge(
					$this->classes(),
					$this->externalClasses()
				);
		}
	}

	protected function getFiles()
	{
		if (self::$mode) {
			return
				array_merge(
					$this->files(),
					$this->externalFiles(),
					$this->devFiles()
				);
		} else {
			return
				array_merge(
					$this->files(),
					$this->externalFiles()
				);
		}
	}

	private function namespaces()
	{
		return
			[
				'Apps'                          	=> self::$base . 'apps/',
				'System'                        	=> self::$base . 'system/'
			];
	}

	private function externalNamespaces()
	{
		$externalNamespaces = [];

		$externalPsr4Arr = include_once self::$base . 'external/vendor/composer/autoload_psr4.php';

		foreach ($externalPsr4Arr as $psr4class => $psr4classArr) {
			$psr4class = rtrim($psr4class, '\\');

			if (count($psr4classArr) === 1) {
				$externalNamespaces[$psr4class] = $psr4classArr[0];
			} else {
				$externalNamespaces[$psr4class] = [];
				foreach ($psr4classArr as $path) {
					array_push($externalNamespaces[$psr4class], $path);
				}
			}
		}

		$externalNamespacesArr = include_once self::$base . 'external/vendor/composer/autoload_namespaces.php';

		foreach ($externalNamespacesArr as $namespaceclass => $namespaceclassArr) {
			$namespaceclass = rtrim($namespaceclass, '\\');

			if (count($namespaceclassArr) === 1) {
				$externalNamespaces[$namespaceclass] = $namespaceclassArr[0] . '/' . $namespaceclass;
			} else {
				$externalNamespaces[$namespaceclass] = [];
				foreach ($namespaceclassArr as $path) {
					array_push($externalNamespaces[$namespaceclass], $path . '/' . $namespaceclass);
				}
			}
		}

		return $externalNamespaces;
	}

	private function devNamespaces()
	{
		return
			[
				'Symfony\\Component\\VarDumper'		=> self::$base . 'vendor/symfony/var-dumper/'
			];
	}

	private function classes()
	{
		return
			[
			];
	}

	private function externalClasses()
	{
		$externalClasses = include_once self::$base . 'external/vendor/composer/autoload_classmap.php';

		if ($externalClasses) {
			return $externalClasses;
		}

		return [];
	}

	private function devClasses()
	{
		return
			[
			];
	}

	private function files()
	{
		return
			[
				__DIR__ . '/../../Base/Helpers.php'
			];
	}

	private function externalFiles()
	{
		$externalFiles = include_once self::$base . 'external/vendor/composer/autoload_files.php';
		if ($externalFiles) {
			return $externalFiles;
		}

		return [];
	}

	private function devFiles()
	{
		return
			[
				__DIR__ . '/../../../vendor/symfony/var-dumper/VarDumper.php'
			];
	}
}