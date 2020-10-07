<?php

use Laminas\Diactoros\Response\RedirectResponse;
use System\Base\Providers\DatabaseServiceProvider\Model;

if (!function_exists('redirect')) {
    function redirect($path) {
        return new RedirectResponse($path);
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return __DIR__ . '/../..' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('flatten_array')) {
    function flatten_array(array $items) {
        return iterator_to_array(
             new \RecursiveIteratorIterator(
                 new \RecursiveArrayIterator($items)
             ), false
         );
    }
}

if (!function_exists('convertObjToArr')) {
    function convertObjToArr($object)
    {
        $reflection = new \ReflectionClass($object);

        $objectArray = [];

        foreach ($reflection->getProperties() as $key => $value) {
            $objectArray[$value->name] = getObjectProperty($object, $value->name);
        }

        return $objectArray;
    }
}

if (!function_exists('getObjectProperty')) {
    function getObjectProperty($object, $name)
    {
        if (property_exists($object, $name)) {
            return $object->{$name};
        }
    }
}

if (!function_exists('xmlToArray')) {
    function xmlToArray($xml, $type = 'file')
    {
        if ($type === 'file') {
            return json_decode(
                        json_encode(
                            simplexml_load_file(
                                $xml,
                                'SimpleXMLElement',
                                LIBXML_NOCDATA
                            )
                        ), 1
                    );
        } else if ($type === 'string') {
            return json_decode(
                        json_encode(
                            simplexml_load_string(
                                $xml,
                                'SimpleXMLElement',
                                LIBXML_NOWARNING
                            )
                        ), 1
                    );
        }
    }
}

if (!function_exists('getAllArr')) {
    function getAllArr(array $array)
    {
        $objArr = [];

        foreach ($array as $arrayKey => $arrayValue) {
            if ($arrayValue instanceof Model) {
                $objArr[$arrayKey] = $arrayValue->getAllArr();
            }
        }

        return $objArr;
    }
}

if (!function_exists('msort')) {
    function msort($array, $key, $sort_flags = SORT_REGULAR, $order = SORT_ASC) {
        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = array();
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        // $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                switch ($order) {
                    case SORT_ASC:
                    asort($mapping, $sort_flags);
                    break;
                    case SORT_DESC:
                    arsort($mapping, $sort_flags);
                    break;
                }
                $sorted = array();
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }
}