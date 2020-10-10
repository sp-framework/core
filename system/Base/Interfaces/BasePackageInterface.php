<?php

namespace System\Base\Interfaces;

interface BasePackageInterface
{
	public function getAll(array $conditions);

	public function add(array $data);

	public function update(array $data);

	public function remove(int $id);
}