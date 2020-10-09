<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Repositories as RepositoriesModel;

class Repositories extends BasePackage
{
	public $repositories;

	public function getAllRepositories($conditions = null)
	{
		if (!$this->repositories) {
			$this->repositories = RepositoriesModel::find($conditions, 'repositories')->toArray();
		}

		return $this;
	}

	public function add(array $data)
	{
		if ($data) {
			$repository = new RepositoriesModel();

			return $repository->add($data);
			//We need to add first
			//Clear cache
			//Renew cache
		}
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