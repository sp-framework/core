<?php

namespace Apps\Ecom\Admin\Packages\Barebone;

use System\Base\BasePackage;

class Modify extends BasePackage
{
	public function modifyModuleFiles($type, $names, $postData)
	{
		if ($type === 'apps') {
			$file = 'apps/' . $names['appName'] . '/app.json';

			$appFile = $this->localContent->read($file);

			if ($appFile) {
				$appFile = str_replace('Barebone', $names['appName'], $appFile);
				$appFile = str_replace('barebone', $names['appRoute'], $appFile);

				$this->localContent->put($file, $appFile);
			} else {
				//Error
			}
		} else if ($type === 'components') {
			$file = 'apps/' . $names['appName'] . '/Components/' . $names['componentName'] . '/' . $names['componentName'] . 'Component.php';

			$componentFile = $this->localContent->read($file);

			if ($componentFile) {
				$componentFile = str_replace('Barebone', $names['appName'], $componentFile);

				if ($postData['task'] === 'component') {
					$componentFile = str_replace('Home', $names['componentName'], $componentFile);
				}

				$this->localContent->put($file, $componentFile);
			} else {
				//Error
			}

			$file =
				'apps/' . $names['appName'] . '/Components/' . $names['componentName'] . '/Install/component.json';

			$componentFile = $this->localContent->read($file);

			if ($componentFile) {
				$componentFile = str_replace('Barebone', $names['appName'], $componentFile);

				if ($postData['task'] === 'component') {
					$componentFile = str_replace('home', $names['componentRoute'], $componentFile);
					$componentFile = str_replace('Home', $names['componentName'], $componentFile);
					$componentFile = str_replace('Hello World', $names['componentName'], $componentFile);
				}

				$this->localContent->put($file,$componentFile);

			} else {
				//Error
			}

		} else if ($type === 'packages') {
			$file = 'apps/' . $names['appName'] . '/Packages/' . $names['packageName'] . '/' . $names['packageName'] . '.php';

			$packageFile = $this->localContent->read($file);

			if ($packageFile) {
				$packageFile = str_replace('Barebone', $names['appName'], $packageFile);

				if ($postData['task'] === 'package') {
					$packageFile = str_replace('Home', $names['packageName'], $packageFile);
				}

				$this->localContent->put($file, $packageFile);
			} else {
				//Error
			}

			$file =
				'apps/' . $names['appName'] . '/Packages/' . $names['packageName'] . '/Install/package.json';

			$packageFile = $this->localContent->read($file);

			if ($packageFile) {

				$packageFile = str_replace('Barebone', $names['appName'], $packageFile);

				if ($postData['task'] === 'package') {
					$packageFile = str_replace('Home', $names['packageName'], $packageFile);
					$packageFile = str_replace('Hello World', $names['packageName'], $packageFile);
				}

				$this->localContent->put($file, $packageFile);

			} else {
				//Error
			}

		} else if ($type === 'middlewares') {
			$file =
				'apps/' . $names['appName'] . '/Middlewares/' . $names['middlewareName'] . '/' . $names['middlewareName'] . '.php';

			$middlewareFile = $this->localContent->read($file);

			if ($middlewareFile) {
				$middlewareFile = str_replace('Barebone', $names['appName'], $middlewareFile);

				if ($postData['task'] === 'middleware') {
					$middlewareFile = str_replace('Home', $names['middlewareName'], $middlewareFile);
				}

				$this->localContent->put($file, $middlewareFile);

			} else {
				//Error
			}

			$file =
				'apps/' . $names['appName'] . '/Middlewares/' . $names['middlewareName'] . '/Install/middleware.json';

			$middlewareFile = $this->localContent->read($file);

			if ($middlewareFile) {

				$middlewareFile = str_replace('Barebone', $names['appName'], $middlewareFile);

				if ($postData['task'] === 'middleware') {
					$middlewareFile = str_replace('Home', $names['middlewareName'], $middlewareFile);
					$middlewareFile = str_replace('Hello World', $names['middlewareName'], $middlewareFile);
				}

				$this->localContent->put($file, $middlewareFile);

			} else {
				//Error
			}

		} else if ($type === 'views') {
			$file = 'apps/' . $names['appName'] . '/Views/' . $names['viewName'] . '/view.json';

			$viewFile = $this->localContent->read($file);

			if ($viewFile) {
				$viewFile = str_replace('Barebone', $names['appName'], $viewFile);

				if ($postData['task'] === 'view') {
					$viewFile = str_replace('Default', $names['viewName'], $viewFile);
				}

				$this->localContent->put($file, $viewFile);
			} else {
				//Error
			}
		}
	}
}