<?php

namespace Packages\Admin\Modules\Module;

use System\Base\BasePackage;

class Update extends BasePackage
{
	protected $postData;

	public function runProcess($postData)
	{
		$this->postData = $postData;

		if ($this->postData['process'] === 'update') {
			if ($this->fileSystem->has('applications/' . ucfirst($this->postData['moduleename']) . '/files.info')) {
				$filesInfo =
					json_decode(
						$this->fileSystem->read(
							'applications/' . ucfirst($this->postData['moduleename']) . '/files.info'
						)
					);

				foreach ($filesInfo->files as $fileKey => $file) {
					$this->fileSystem->delete($file);
				}
				foreach ($filesInfo->dir as $dirKey => $dir) {
					$this->fileSystem->deleteDir($dir);
				}
			} else {
				$this->viewsData->responseCode = 1;
				$this->viewsData->responseMessage =
					'files.info file missing! Cannot update ' . rtrim(ucfirst($this->postData['type']), 's') . '.';

				return $this->generateView();
			}
		}
	}
}