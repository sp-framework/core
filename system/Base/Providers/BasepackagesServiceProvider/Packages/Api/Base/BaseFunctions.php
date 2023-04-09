<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\HttpHandler;

class BaseFunctions
{
    /**
     * Returns a description of the type for the passed value.
     *
     * @param mixed $value The value whos type will be described.
     *
     * @return string A description of the value's type.
     */
    public static function describeType($value)
    {
        switch (gettype($value)) {
            case 'object':
                return 'object('. get_class($value) . ')';
            case 'array':
                return 'array(' . count($value) . ')';
            default:
                ob_start();
                return str_replace('double(', 'float(', rtrim(ob_get_clean()));
        }
    }

    /**
     * Merges multiple arrays, recursively, and returns the merged array.
     * Code taken from
     * https://api.drupal.org/api/drupal/includes!bootstrap.inc/function/drupal_array_merge_deep/7
     *
     * @return array The merged array.
     */
    public static function arrayMergeDeep()
    {
        $args = func_get_args();
        return self::arrayMergeDeepArray($args);
    }

    /**
     * Merges multiple arrays, recursively, and returns the merged array.
     *
     * @param array $arrays The arrays to merge.
     *
     * @return array The merged array.
     */
    public static function arrayMergeDeepArray(array $arrays)
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                // Renumber integer keys as array_merge_recursive() does. Note that PHP
                // automatically converts array keys that are integer strings (e.g., '1')
                // to integers.
                if (is_integer($key)) {
                    $result[] = $value;
                } elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    // Recurse when both values are arrays.
                    $result[$key] = self::arrayMergeDeepArray(array($result[$key], $value));
                } else {
                    // Otherwise, use the latter value, overriding any previous value.
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Returns the default HTTP handler.
     *
     * @param array &$configuration Not used.
     *
     * @return \DTS\eBaySDK\HttpHandler
     */
    public static function defaultHttpHandler(array &$configuration)
    {
        return new HttpHandler();
    }

    /**
     * Helper function that returns true if the property type should be checked.
     *
     * @param string $type The type name.
     *
     * @return bool
     */
    public static function checkPropertyType($type)
    {
        switch ($type) {
            case 'integer':
            case 'int':
            case 'string':
            case 'double':
            case 'boolean':
            case 'bool':
            case 'DateTime':
                return false;
            default:
                return true;
        }
    }
}