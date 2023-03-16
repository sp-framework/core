<?php

namespace System\Base\Interfaces;

interface ComponentInterface
{
    public function viewAction();

    public function addAction();

    public function updateAction();

    public function removeAction();
}