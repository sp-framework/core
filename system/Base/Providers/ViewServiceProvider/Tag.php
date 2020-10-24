<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Tag as PhalconTag;;

class Tag
{
	protected $tag;

	public function __construct()
	{
	}

	public function init()
	{
		$this->tag = new PhalconTag;

		//Register new tags
		return $this->tag;
	}
}