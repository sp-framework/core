<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class View
{
	public function register($db, $viewFile, $installedFiles)
	{
		return $db->insertAsDict(
			'views',
			[
				'name' 					=> $viewFile['name'],
				'description' 			=> $viewFile['description'],
				'category'  			=> $viewFile['category'],
				'sub_category'  		=> $viewFile['sub_category'],
				'version'				=> $viewFile['version'],
				'repo'		 			=> $viewFile['repo'],
				'settings'				=>
					isset($viewFile['settings']) ?
					Json::encode($viewFile['settings']) :
					null,
				'dependencies'			=>
					isset($viewFile['dependencies']) ?
					Json::encode($viewFile['dependencies']) :
					null,
				'applications'			=>
					Json::encode(['1'=>['installed'=>true]]),
				'files'					=> Json::encode($installedFiles)
			]
		);
	}
}