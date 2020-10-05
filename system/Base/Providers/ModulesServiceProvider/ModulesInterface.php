<?php

namespace System\Base\Providers\ModulesServiceProvider;

interface ModulesInterface
{
	public function getAll($criteria = [], $sort = null, $limit = null, $offset = null);

	public function getById($id);

	public function register(array $data);

	public function update(array $data);

	public function remove($id);
}