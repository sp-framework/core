<?php

namespace System\Base\Interfaces;

interface MiddlewareInterface
{
    public function process(array $data);
}