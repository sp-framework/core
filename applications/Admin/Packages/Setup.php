<?php

namespace Applications\Admin\Packages;

use Phalcon\Di\DiInterface;
use System\Base\Providers\CoreServiceProvider\Model\Core;
use System\Base\Providers\ModulesServiceProvider\Model\Repositories;
use System\Base\Providers\ModulesServiceProvider\Model\Applications;
use System\Base\Providers\ModulesServiceProvider\Model\Components;
use System\Base\Providers\ModulesServiceProvider\Model\Packages;
use System\Base\Providers\ModulesServiceProvider\Model\Middlewares;
use System\Base\Providers\ModulesServiceProvider\Model\Views;
use System\Base\Providers\DatabaseServiceProvider\BaseDb;

class Setup
{
	private $container;

	protected $db;

	protected $postData;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;

		$this->db = $this->container->getShared('db');

		$this->postData = $this->container->getShared('request')->getPost();
	}

	public function checkFields()
	{
		if (
			$this->postData['host'] === '' ||
			$this->postData['port'] === '' ||
			$this->postData['database_name'] === '' ||
			$this->postData['username'] === '' ||
			$this->postData['password'] === ''
		) {
			return false;
		} else {

			return true;
		}
	}

	public function checkDbEmpty()
	{
		$allTables =
			$this->em->getConnection()->getSchemaManager()->listTableNames();

		if (count($allTables) > 0) {
			if ($this->postData['drop'] === 'false') {

				return false;
			} else {

				foreach ($allTables as $tableKey => $tableValue) {
					$this->em->getConnection()->getSchemaManager()->dropTable($tableValue);
				}
				return true;
			}
		}
	}

	public function buildSchema()
	{
		$schemaTool = new SchemaTool($this->em);

		$acpTable =
			[
				$this->em->getClassMetadata(Repositories::class),
				$this->em->getClassMetadata(Core::class),
				$this->em->getClassMetadata(Applications::class),
				$this->em->getClassMetadata(Components::class),
				$this->em->getClassMetadata(Packages::class),
				$this->em->getClassMetadata(Views::class),
				$this->em->getClassMetadata(Middlewares::class)
			];

		try {
			$schemaTool->createSchema($acpTable);

			return true;
		} catch (ToolsException $e) {

			return $e;
		}
	}

	public function registerHWFRepository()
	{
		$this->db->addToDb(
			Repositories::class,
			[
				'id'					=> '',
				'name' 					=> 'Hello World Framework (h-w-f)',
				'description' 			=> 'Hello World Framework Repositories',
				'url'		 			=> 'https://api.github.com/orgs/h-w-f/repos',
				'need_auth'				=> 0,
				'username'				=> '',
				'token'					=> ''
			]
		);
	}

	protected function getInstalledFiles($directory = null, $sub = true)
	{
		$installedFiles = [];
		$installedFiles['dir'] = [];
		$installedFiles['files'] = [];

		if ($directory) {
			$contents = $this->container['fileSystem']->listContents($directory, $sub);

			foreach ($contents as $contentKey => $content) {
				if ($content['type'] === 'dir') {
					array_push($installedFiles['dir'], $content['path']);
				} else if ($content['type'] === 'file') {
					array_push($installedFiles['files'], $content['path']);
				}
			}

			return $installedFiles;
		} else {
			return null;
		}
	}

	public function registerCore(array $coreFile)
	{
		$installedFiles = $this->getInstalledFiles('system/');

		array_push($installedFiles['files'], 'index.php', 'composer.json', 'core.json');

		$this->db->addToDb(
			Core::class,
			[
				'id'					=> '',
				'name' 					=> $coreFile['name'],
				'display_name'			=> $coreFile['displayName'],
				'description' 			=> $coreFile['description'],
				'version'	 			=> $coreFile['version'],
				'repo'					=> $coreFile['repo'],
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	public function registerModule($type, $newApplicationId)
	{
		if ($type === 'applications') {
			return
				$this->registerAdminApplication(
					json_decode(
						$this->container['fileSystem']->read('applications/Admin/application.json'),
						true
					)
				)->getAllArr();

		} else if ($type === 'components') {

			$this->registerAdminComponent(
				json_decode(
					$this->container['fileSystem']->read('components/Admin/Install/Modules/component.json'),
					true
				),
				$newApplicationId
			);

		} else if ($type === 'packages') {

			$this->registerAdminPackage(
				json_decode(
					$this->container['fileSystem']->read('packages/Admin/Install/Modules/package.json'),
					true
				),
				$newApplicationId
			);

		} else if ($type === 'middlewares') {

			$adminMiddlewares = $this->container['fileSystem']->listContents('middlewares/Admin/Install/', true);

			foreach ($adminMiddlewares as $adminMiddlewareKey => $adminMiddleware) {
				if ($adminMiddleware['basename'] === 'middleware.json') {
					$this->registerAdminMiddleware(
						json_decode(
							$this->container['fileSystem']->read($adminMiddleware['path']),
							true
						),
						$newApplicationId
					);
				}
			}
		} else if ($type === 'views') {

			$this->registerAdminView(
				json_decode(
					$this->container['fileSystem']->read('views/Admin/Default/view.json'),
					true
				),
				$newApplicationId
			);
		}
	}

	protected function registerAdminApplication(array $applicationFile)
	{
		$installedFiles = $this->getInstalledFiles('applications/');

		return $this->db->addToDb(
			Applications::class,
			[
				'id'					=> '',
				'name' 					=> $applicationFile['name'],
				'display_name' 			=> $applicationFile['displayName'],
				'description' 			=> $applicationFile['description'],
				'version'				=> $applicationFile['version'],
				'repo'					=> $applicationFile['repo'],
				'settings'			 	=>
					isset($applicationFile['settings']) ?
					json_encode($applicationFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($applicationFile['dependencies']) ?
					json_encode($applicationFile['dependencies']) :
					null,
				'is_default'			=> 1,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles),
				'mode'					=> $this->postData['mode'] === 'true' ? 0 : 1
			]
		);
	}

	protected function registerAdminComponent(array $componentFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('components/');

		return $this->db->addToDb(
			Components::class,
			[
				'id'					=> '',
				'name' 					=> $componentFile['name'],
				'display_name' 			=> $componentFile['displayName'],
				'description' 			=> $componentFile['description'],
				'version'				=> $componentFile['version'],
				'path'					=> $componentFile['path'],
				'repo'					=> $componentFile['repo'],
				'dependencies'		 	=>
					isset($componentFile['dependencies']) ?
					json_encode($componentFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	protected function registerAdminPackage(array $packageFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('packages/');

		$installedFiles = $this->getInstalledFiles('packages/');

		return $this->db->addToDb(
			Packages::class,
			[
				'id'					=> '',
				'name' 					=> $packageFile['name'],
				'display_name'			=> $packageFile['displayName'],
				'description' 			=> $packageFile['description'],
				'version'				=> $packageFile['version'],
				'repo'					=> $packageFile['repo'],
				'path'					=> $packageFile['path'],
				'dependencies'		 	=>
					isset($packageFile['dependencies']) ?
					json_encode($packageFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	public function registerAdminMiddleware(array $middlewareFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('middlewares/');

		$this->db->addToDb(
			Middlewares::class,
			[
				'id'					=> '',
				'name' 					=> $middlewareFile['name'],
				'display_name' 			=> $middlewareFile['displayName'],
				'description' 			=> $middlewareFile['description'],
				'version'				=> $middlewareFile['version'],
				'repo'		 			=> $middlewareFile['repo'],
				'path'					=> $middlewareFile['path'],
				'class'					=> $middlewareFile['class'],
				'settings'				=>
					isset($middlewareFile['settings']) ?
					json_encode($middlewareFile['settings']) :
					null,
				'dependencies'			=>
					isset($middlewareFile['dependencies']) ?
					json_encode($middlewareFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'sequence'				=> 0,
				'enabled'				=> 0,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	protected function registerAdminView(array $viewFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('views/');

		return $this->db->addToDb(
			Views::class,
			[
				'id'					=> '',
				'name' 					=> $viewFile['name'],
				'display_name' 			=> $viewFile['displayName'],
				'description' 			=> $viewFile['description'],
				'version'				=> $viewFile['version'],
				'repo'		 			=> $viewFile['repo'],
				'settings'				=>
					isset($viewFile['settings']) ?
					json_encode($viewFile['settings']) :
					null,
				'dependencies'			=>
					isset($viewFile['dependencies']) ?
					json_encode($viewFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	public function writeDbConfig()
	{
		$configContent =
'<?php

return
	[
		"mysql" =>
			[
				"driver" 	=> "pdo_mysql",
				"host" 		=> "' . $this->postData['host'] . '",
				"port" 		=> "' . $this->postData['port'] . '",
				"dbname" 	=> "' . $this->postData['database_name'] . '",
				"user" 		=> "' . $this->postData['username'] . '",
				"password" 	=> "' . $this->postData['password'] . '",
			]
	];';

		$this->container['fileSystem']->put('/system/Configs/Db.php', $configContent);

		if ($this->postData['mode'] === 'production') {
			$debug = "false";
		} else if ($this->postData['mode'] === 'development') {
			$debug = "true";
		}

		$baseContent =
'<?php

return
	[
		"debug"					=> "' . $debug . '" //true - Development false - Production
	];';

		$this->container['fileSystem']->put('/system/Configs/Base.php', $baseContent);
	}

	public function removeSetup()
	{
		//If production delete setup file and rewrite ConfigServiceProvider Boot section.
		//Do the same when updating core via modules update
		// if ($this->postData['mode'] === 'production') {
		// 	$this->container['fileSystem']->delete(base_path('system/Base/Installer/Setup.php'));
		// 	$this->container['fileSystem']->delete(base_path('system/Base/Installer/SetupPackage.php'));
		// 	$this->redoConfigServiceProviderFile();
		// }
	}

	protected function redoConfigServiceProviderFile()
	{
		$configServiceProviderContent =
'<?php

namespace System\Base\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use System\Base\Loaders\ArrayLoader;
use System\Base\Providers\ConfigServiceProvider\Config;

class ConfigServiceProvider extends AbstractServiceProvider
{
	protected $provides = [
		\'config\'
	];

	public function register()
	{
		$container = $this->getContainer();

		$container->share(\'config\', function () {
			$loader = new ArrayLoader([
				\'base\'          => base_path(\'system/Configs/Base.php\'),
				\'db\'            => base_path(\'system/Configs/Db.php\'),
				\'providers\'     => base_path(\'system/Configs/Providers.php\')
			]);

			return (new Config)->load([$loader]);
		});
	}
}';

		$this->container['fileSystem']->put('/system/Base/Providers/ConfigServiceProvider.php', $configServiceProviderContent);
	}
}