<?php

namespace System\Base\Providers\SessionServiceProvider;

interface SessionStore
{
	public function get($key, $default = null);

	public function set($key, $value = null);

	public function exists($key);

	public function clear(...$key);
}
