<?php

namespace System\Base\Interfaces;

interface PackageInterface
{
    public function init(bool $resetCache = false);

    public function get(array $data = [], bool $resetCache = false);

    public function add(array $data);

    public function update(array $data);

    public function remove(array $data);

    public function error(string $messageTitle = null, string $messageDetails = null, int $id = null);
}