<?php

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return __DIR__ . '/../..' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('trace')) {
    function trace(array $varsToDump = [], $exit = true, $args = false, $object = false, $file = true, $line = true, $class = true, $function = true) {
        $backtrace = debug_backtrace();

        $traces = [];

        foreach ($backtrace as $key => $trace) {
            if ($file && isset($trace['file'])) {
                $traces[$key]['file'] = $trace['file'];
            }
            if ($line && isset($trace['line'])) {
                $traces[$key]['line'] = $trace['line'];
            }
            if ($class && isset($trace['class'])) {
                $traces[$key]['class'] = $trace['class'];
            }
            if ($function && isset($trace['function'])) {
                $traces[$key]['function'] = $trace['function'];
            }
            if ($args && isset($trace['args'])) {
                $traces[$key]['args'] = $trace['args'];
            }
            if ($object && isset($trace['object'])) {
                $traces[$key]['object'] = $trace['object'];
            }
        }

        if ($object) {
            if (class_exists(\Symfony\Component\VarDumper\VarDumper::class)) {
                if (count($varsToDump) > 0) {
                    foreach ($varsToDump as $var) {
                        dump($var);
                    }
                }
                dump(array_reverse($traces));
            } else {
                if (count($varsToDump) > 0) {
                    foreach ($varsToDump as $var) {
                        var_dump($var);
                    }
                }
                var_dump(array_reverse($traces));
            }
        } else {
            if (count($varsToDump) > 0) {
                foreach ($varsToDump as $var) {
                    var_dump($var);
                }
            }
            var_dump(array_reverse($traces));
        }

        if ($exit) {
            exit;
        }
    }
}

if (!function_exists('toBytes')) {
    function toBytes($from) {
        $type = substr($from, -1);
        $size = (int) substr($from, 0, -1);

        if ($type === 'G') {
            return $size * 1073741824;
        } else if ($type === 'M') {
            return $size * 1048576;
        }
    }
}

if (!function_exists('json_decode_recursive')) {
    function json_decode_recursive(&$value, $key) {
        $value_decoded = json_decode($value, true);
        if ($value_decoded) {
            $value = $value_decoded;
        }
    }
}

if (!function_exists('scanAllDir')) {
    function scanAllDir($dir) {
        $result = [];

        foreach(scandir($dir) as $filename) {
            if ($filename[0] === '.') continue;

            $filePath = $dir . '/' . $filename;

            if (is_dir($filePath)) {
                foreach (scanAllDir($filePath) as $childFilename) {
                    $result[] = $filename . '/' . $childFilename;
                }
            } else {
                $result[] = $filename;
            }
        }

        return $result;
    }
}

if (!function_exists('deleteFiles')) {
    function deleteFiles($target) {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK);

            foreach ($files as $file ) {
                deleteFiles( $file );
            }

            rmdir($target);
        } else {
            unlink($target);
        }
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

if (!function_exists('true_flatten')) {
    function true_flatten(array $array, array $parents = [])
    {
        $return = [];
        foreach ($array as $k => $value) {
            $p = empty($parents) ? [$k] : [...$parents, $k];
            if (is_array($value)) {
                $return = [...$return, ...true_flatten($value, $p)];
            } else {
                $return[implode('_', $p)] = $value;
            }
        }

        return $return;
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

if (!function_exists('checkCtype')) {
    function checkCtype($str, $ctype = 'alnum', $ignoreChars = null) {
        if (!$ignoreChars) {
            $ignoreChars = [' ', '&amp;', '&', ',', ':', ';'];
        }

        if ($ctype === 'alnum') {
            if (ctype_alnum(trim(str_replace($ignoreChars, '' , $str)))) {
                return trim(str_replace($ignoreChars, '' , $str));
            }
        } else if ($ctype === 'alpha') {
            if (ctype_alpha(trim(str_replace($ignoreChars, '' , $str)))) {
                return trim(str_replace($ignoreChars, '' , $str));
            }
        } else if ($ctype === 'digits') {
            if (ctype_digit(trim(str_replace($ignoreChars, '' , $str)))) {
                return trim(str_replace($ignoreChars, '' , $str));
            }
        }

        return false;
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

//Convert Warnings to Exceptions
set_error_handler(function ($severity, $message, $file, $line) {
    throw new \ErrorException($message, $severity, $severity, $file, $line);
});
//To restore defaults
//restore_error_handler();

if (!function_exists('array_merge_recursive_ex')) {
    function array_merge_recursive_ex(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => & $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_ex($merged[$key], $value);
            } else if (is_numeric($key)) {
                 if (!in_array($value, $merged)) {
                    $merged[] = $value;
                 }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}

if (!function_exists('array_merge_recursive_distinct')) {
    function array_merge_recursive_distinct()
    {
        $arrays = func_get_args();
        $base = array_shift($arrays);
        if (!is_array($base)) {
            $base = empty($base) ? array() : array($base);
        }
        foreach ($arrays as $append) {
            if (!is_array($append)) {
                $append = array($append);
            }
            foreach ($append as $key => $value) {
                if (!array_key_exists($key, $base) and !is_numeric($key)) {
                    $base[$key] = $append[$key];
                    continue;
                }
                if (is_array($value) or is_array($base[$key])) {
                    $base[$key] = array_merge_recursive_distinct($base[$key], $append[$key]);
                } else {
                    if (is_numeric($key)) {
                        if (!in_array($value, $base)) {
                            $base[] = $value;
                        }
                    } else {
                        $base[$key] = $value;
                    }
                }
            }
        }
        return $base;
    }
}

if (!function_exists('drupal_array_merge_deep')) {
    function drupal_array_merge_deep() {
      $args = func_get_args();
      return drupal_array_merge_deep_array($args);
    }
}

// source : https://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/drupal_array_merge_deep_array/7.x
if (!function_exists('drupal_array_merge_deep_array')) {
    function drupal_array_merge_deep_array($arrays) {
        $result = array();
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                // Renumber integer keys as array_merge_recursive() does. Note that PHP
                // automatically converts array keys that are integer strings (e.g., '1')
                // to integers.
                if (is_integer($key)) {
                    $result[] = $value;
                }
                elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = drupal_array_merge_deep_array(array(
                        $result[$key],
                        $value,
                    ));
                }
                else {
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }
}

if (!function_exists('prefix_get_next_key_array')) {
    function prefix_get_next_key_array( $arr, $key ) {
        $keys     = array_keys( $arr );
        $position = array_search( $key, $keys, true );

        if ( isset( $keys[ $position + 1 ] ) ) {
            $next_key = $keys[ $position + 1 ];
        }

        return $next_key;
    }
}

if (!function_exists('recursive_array_search')) {
    function recursive_array_search($needle, $haystack, $needleKey = null) {
        foreach ($haystack as $key => $value) {
            $current_key = $key;

            if ($needleKey) {
                if (($needleKey == $key && $needle == $value) ||
                    (is_array($value) && recursive_array_search($needle, $value, $needleKey) !== false)
                ) {
                    return $current_key;
                }
            } else {
                if ($needle == $value ||
                    (is_array($value) && recursive_array_search($needle, $value, $needleKey) !== false)
                ) {
                    return $current_key;
                }
            }
        }

        return false;
    }
}