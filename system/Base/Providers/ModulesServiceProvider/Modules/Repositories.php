<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Repositories as RepositoriesModel;

class Repositories extends BasePackage
{
	protected $repositories;

	public function getAllRepositories($conditions = null)
	{
		if (!$this->repositories) {
			$this->repositories = RepositoriesModel::find($conditions, 'repositories')->toArray();
		}

		return $this;
	}

	// public function remove($id)
	// {
	// 	if ($id !== '1') {
	// 		return $this->db->deleteFromDbById(RepositoriesModel::class, $id);
	// 	} else {
	// 		return false;
	// 	}
	// }
}