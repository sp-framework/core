<?php

namespace System\Base\Interfaces;

interface ComponentInterface
{
    public function ViewAction();

    public function addAction();

    public function updateAction();

    public function removeAction();
}