<?php

// namespace Apps\Dash\Packages\System\Api;

// use Apps\Dash\Packages\System\Api\Apis\Ebay;
// use Apps\Dash\Packages\System\Api\Apis\Generic;

// class Apis
// {
// 	protected $generic;

// 	protected $ebay;

// 	public function __construct()
// 	{
// 	}

// 	public function __get($name)
// 	{
// 		if (!isset($this->{$name})) {
// 			if (method_exists($this, $method = "init" . ucfirst("{$name}"))) {
// 				$this->{$name} = $this->{$method}();
// 			}
// 		}

// 		return $this->{$name};
// 	}

// 	protected function initGeneric()
// 	{
// 		$this->generic = (new Generic())->init();

// 		return $this->generic;
// 	}

// 	protected function initEbay()
// 	{
// 		$this->ebay = (new Ebay())->init();

// 		return $this->ebay;
// 	}
// }