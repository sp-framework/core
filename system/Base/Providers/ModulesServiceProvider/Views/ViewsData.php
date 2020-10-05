<?php

namespace System\Base\Providers\ModulesServiceProvider\Views;

class ViewsData implements \JsonSerializable
{
	public $viewsData = [];

	public function __set($key, $value)
	{
		$this->viewsData[$key] = $value;
	}

	public function getAllData()
	{
		return ['viewsData' => $this->viewsData];
	}

	public function __get($key)
	{
		return $this->viewsData[$key];
	}

	public function jsonSerialize()
	{
		$jsonArray = [];

		foreach ($this->viewsData as $key => $value) {
			if (is_object($value)) {
				$jsonArray[$key] = $this->extractProperties($value);
			} else if (is_array($value)) {
				foreach ($value as $key => $arrayValue) {
					if (is_object($arrayValue)) {
						$jsonArray[$key] = $this->extractProperties($arrayValue);
					} else {
						$jsonArray[$key] = $arrayValue;
					}
				}
			} else {
				$jsonArray[$key] = $value;
			}
		}

		return $jsonArray;
	}

	protected function extractProperties($object)
	{
		$public = [];

		$reflection = new \ReflectionClass(get_class($object));
		foreach ($reflection->getProperties() as $property) {
			$property->setAccessible(true);

			$value = $property->getValue($object);
			$name = $property->getName();

			if(is_array($value)) {
				$public[$name] = [];

				foreach ($value as $item) {
					if (is_object($item)) {
						$itemArray = $this->extractProperties($item);
						$public[$name][] = $itemArray;
					} else {
						$public[$name][] = $item;
					}
				}
			} else if (is_object($value)) {
				$public[$name] = $this->extractProperties($value);
			} else {
				$public[$name] = $value;
			}
		}
		return json_encode($public);
	}
}