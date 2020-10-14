<?php

namespace Applications\Admin\Packages\Barebone;

use System\Base\BasePackage;

class Copy extends BasePackage
{
	protected $scanDir;

	public function copyModuleStructure($type, $names, $postData)
	{
		if (!$this->scanDir) {
			$this->scanDir =
				$this->localContent->listContents(
					'applications/Admin/Packages/Barebone/Data/',
					true
				);
		}

		$contents = $this->getTaskContentsFromScannedDir($type, $postData);

		$installedFiles = [];
		$installedFiles['dir'] = [];
		$installedFiles['files'] = [];

		foreach ($contents as $contentKey => $content) {
			$destDir =
				str_replace('Barebone', $names['applicationName'],
					str_replace(
						'applications/Admin/Packages/Barebone/Data/',
						'',
						$content['path']
					)
				);

			if ($content['basename'] === 'Barebone') {
				$content['basename'] = $names['applicationName'];
			}

			if ($postData['task'] === 'component') {
				if (strpos($content['basename'], 'Hw') !== false) {
					$content['basename'] =
						str_replace('Hw', $names['componentName'], $content['basename']);
				}
				if (strpos($content['dirname'], 'Hw') !== false) {
					$content['dirname'] =
						str_replace('Hw', $names['componentName'], $content['dirname']);
				}

				$destDir = str_replace('Hw', $names['componentName'], $destDir);
			}
			// var_dump($content);
			if ($postData['task'] === 'package') {
				if (strpos($content['basename'], 'Hw') !== false) {
					$content['basename'] =
						str_replace('Hw', $names['packageName'], $content['basename']);
				}
				if (strpos($content['dirname'], 'Hw') !== false) {
					$content['dirname'] =
						str_replace('Hw', $names['packageName'], $content['dirname']);
				}

				$destDir = str_replace('Hw', $names['packageName'], $destDir);
			}

			if ($postData['task'] === 'middleware') {
				if (strpos($content['basename'], 'Hw') !== false) {
					$content['basename'] =
						str_replace('Hw', $names['middlewareName'], $content['basename']);
				}
				if (strpos($content['dirname'], 'Hw') !== false) {
					$content['dirname'] =
						str_replace('Hw', $names['middlewareName'], $content['dirname']);
				}

				$destDir = str_replace('Hw', $names['middlewareName'], $destDir);
			}

			if ($postData['task'] === 'view') {
				if (strpos($content['dirname'], 'Default') !== false) {
					$content['dirname'] =
						str_replace('Default', $names['viewName'], $content['dirname']);
				}

				$destDir = str_replace('Default', $names['viewName'], $destDir);
			}

			if ($content['type'] === 'dir') {
				if (!$this->localContent->has($destDir)) {
					$this->localContent->createDir($destDir);
				}

				array_push($installedFiles['dir'], $destDir);

			} else if ($content['type'] === 'file') {

			// var_dump($destDir);
				$this->localContent->copy($content['path'], $destDir);

				array_push($installedFiles['files'], $destDir);
			}
		}

		return $installedFiles;
	}

	protected function getTaskContentsFromScannedDir($type, $postData)
	{
		$contents = [];

		if ($type === 'applications') {
			foreach ($this->scanDir as $key => $value) {
				if ($value['path'] === 'applications/Admin/Packages/Barebone/Data/applications/Barebone' ||
					$value['path'] === 'applications/Admin/Packages/Barebone/Data/applications/Barebone/application.json'
				) {
					$contents[$key] = $value;
				}
			}
		} else if ($type === 'components') {
			foreach ($this->scanDir as $key => $value) {
				if ($postData['task'] === 'component') {
					if (strpos($value['path'], 'Barebone/Components') &&
						strpos($value['path'], 'Errors') === false
					) {
						$contents[$key] = $value;
					}
				} else {
					if (strpos($value['path'], 'Barebone/Components')) {
						$contents[$key] = $value;
					}
				}
			}
		} else if ($type === 'packages') {
			foreach ($this->scanDir as $key => $value) {
				if (strpos($value['path'], 'Barebone/Packages')) {
					$contents[$key] = $value;
				}
			}
		} else if ($type === 'middlewares') {
			foreach ($this->scanDir as $key => $value) {
				if (strpos($value['path'], 'Barebone/Middlewares')) {
					$contents[$key] = $value;
				}
			}
		} else if ($type === 'views') {
			foreach ($this->scanDir as $key => $value) {
				if (strpos($value['path'], 'Barebone/Views') ||
					strpos($value['path'], 'public')
				) {
					$contents[$key] = $value;
				}
			}
		}

		return $contents;
	}
}

