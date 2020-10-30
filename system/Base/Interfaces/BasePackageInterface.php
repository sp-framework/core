<?php

namespace System\Base\Interfaces;

interface BasePackageInterface
{
    public function getAll(bool $resetCache = false, bool $enableCache = true);

    public function getById(int $id, bool $resetCache = false, bool $enableCache = true);

    public function getByParams(array $params, bool $resetCache = false, bool $enableCache = true);

    public function add(array $data);

    public function update(array $data);

    public function remove(int $id);
}