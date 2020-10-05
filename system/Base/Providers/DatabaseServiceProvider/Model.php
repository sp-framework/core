<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Model
{
    public function getAllArr()
    {
        $reflection = new \ReflectionClass($this);

        $objectArray = [];

        foreach ($reflection->getProperties() as $key => $value) {
            if (!$reflection->getProperty($value->name)->isPrivate()) {
                $objectArray[$value->name] = $this->get($value->name);
            }
        }

        return $objectArray;
    }

    public function get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        if (property_exists($this, $name)) {
            return true;
        }

        return false;
    }

    public function update(array $columns)
    {
        foreach ($columns as $column => $value) {
            $this->{$column} = $value;
        }
    }

    public function fill(array $columns)
    {
        $this->update($columns);
    }
}
