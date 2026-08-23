<?php

/**
 * SP Framework
 *
 * Procedural helper functions providing essential utilities for file system management,
 * string manipulations, array transformations, debugging, network inquiries, and execution environments.
 *
 * @package     System\Base
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

// Convert Warnings to Exceptions respecting error_reporting level
if (!defined('SP_ERROR_HANDLER_SET')) {
    define('SP_ERROR_HANDLER_SET', true);
    set_error_handler(function (int $severity, string $message, ?string $file = null, ?int $line = null): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file ?? __FILE__, $line ?? __LINE__);
    });
}

if (!function_exists('base_path')) {
    /**
     * Resolves the absolute project root path, optionally appending a relative subpath.
     *
     * @param string|null $path Optional relative path to append to base directory.
     *
     * @return string Normalized absolute base filesystem path.
     */
    function base_path(?string $path = null): string
    {
        $base = dirname(__DIR__, 2);

        if ($path === null || $path === '') {
            return $base;
        }

        return $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
    }
}

if (!function_exists('trace')) {
    /**
     * Generates and inspects an application execution backtrace with optional variable dumping.
     *
     * @param array $varsToDump   Optional list of variables to dump.
     * @param bool  $exit         Whether to terminate script execution after dumping.
     * @param bool  $args         Whether to include function arguments in trace items.
     * @param bool  $object       Whether to use VarDumper and include object instances.
     * @param bool  $file         Whether to include source filenames in trace items.
     * @param bool  $line         Whether to include source line numbers in trace items.
     * @param bool  $class        Whether to include class names in trace items.
     * @param bool  $function     Whether to include function names in trace items.
     * @param bool  $returnTraces Whether to return trace array directly instead of printing.
     * @param bool  $dumpTraces   Whether to dump formatted trace stack to output.
     *
     * @return array|void Trace details array if $returnTraces is true, void otherwise.
     */
    function trace(
        array $varsToDump = [],
        bool $exit = true,
        bool $args = false,
        bool $object = false,
        bool $file = true,
        bool $line = true,
        bool $class = true,
        bool $function = true,
        bool $returnTraces = false,
        bool $dumpTraces = true
    ) {
        $backtrace = debug_backtrace();
        $traces = [];

        foreach ($backtrace as $key => $traceItem) {
            $entry = [];

            if ($file && isset($traceItem['file'])) {
                $entry['file'] = $traceItem['file'];
            }
            if ($line && isset($traceItem['line'])) {
                $entry['line'] = $traceItem['line'];
            }
            if ($class && isset($traceItem['class'])) {
                $entry['class'] = $traceItem['class'];
            }
            if ($function && isset($traceItem['function'])) {
                $entry['function'] = $traceItem['function'];
            }
            if ($args && isset($traceItem['args'])) {
                $entry['args'] = $traceItem['args'];
            }
            if ($object && isset($traceItem['object'])) {
                $entry['object'] = $traceItem['object'];
            }

            $traces[$key] = $entry;
        }

        if ($returnTraces) {
            return $traces;
        }

        $reversedTraces = array_reverse($traces);
        $lastTraceKey = array_key_last($reversedTraces);
        $lastTrace = $lastTraceKey !== null ? $reversedTraces[$lastTraceKey] : ['line' => 0, 'file' => 'unknown'];

        echo 'Trace called at line: <strong>' . ($lastTrace['line'] ?? 0) . '</strong> on file: <strong>' . ($lastTrace['file'] ?? 'unknown') . '</strong>';

        $hasVarDumper = class_exists(\Symfony\Component\VarDumper\VarDumper::class);

        if ($object && $hasVarDumper) {
            foreach ($varsToDump as $var) {
                \Symfony\Component\VarDumper\VarDumper::dump($var);
            }
            if ($dumpTraces) {
                \Symfony\Component\VarDumper\VarDumper::dump($reversedTraces);
            }
        } else {
            foreach ($varsToDump as $var) {
                var_dump($var);
            }
            if ($dumpTraces) {
                var_dump($reversedTraces);
            }
        }

        if ($exit) {
            exit;
        }
    }
}

if (!function_exists('json_trace')) {
    /**
     * Serializes a Throwable exception into a structured JSON string.
     *
     * @param \Throwable $e Throwable exception instance.
     *
     * @return string JSON-encoded representation of exception details.
     */
    function json_trace(\Throwable $e): string
    {
        $json = [
            'class'         => $e::class,
            'message'       => $e->getMessage(),
            'code'          => $e->getCode(),
            'file'          => $e->getFile(),
            'line'          => $e->getLine(),
            'originalTrace' => [],
        ];

        foreach ($e->getTrace() as $item) {
            $item['args'] = [];
            $json['originalTrace'][] = $item;
        }

        return (string) json_encode($json, JSON_UNESCAPED_SLASHES);
    }
}

if (!function_exists('toBytes')) {
    /**
     * Converts human-readable memory size strings (e.g., '128M', '2G', '512K') into integer byte values.
     *
     * @param int|float|string $from Size specification string or numeric byte value.
     *
     * @return int Size in bytes.
     */
    function toBytes(int|float|string $from): int
    {
        if (is_numeric($from)) {
            return (int) $from;
        }

        $trimmed = trim((string) $from);
        if ($trimmed === '') {
            return 0;
        }

        $unit = strtoupper(substr($trimmed, -1));
        $numberPart = substr($trimmed, 0, -1);

        if ($unit === 'B' && strlen($trimmed) > 1) {
            $subUnit = strtoupper(substr($trimmed, -2, 1));
            if (in_array($subUnit, ['K', 'M', 'G', 'T'], true)) {
                $unit = $subUnit;
                $numberPart = substr($trimmed, 0, -2);
            }
        }

        $size = (float) trim($numberPart);

        return match ($unit) {
            'T'     => (int) ($size * 1099511627776),
            'G'     => (int) ($size * 1073741824),
            'M'     => (int) ($size * 1048576),
            'K'     => (int) ($size * 1024),
            default => (int) $size,
        };
    }
}

if (!function_exists('json_decode_recursive')) {
    /**
     * Decodes JSON encoded string values within arrays recursively in place.
     *
     * @param mixed $value Reference to the current array element value.
     * @param mixed $key   Current array element key (optional).
     *
     * @return void
     */
    function json_decode_recursive(mixed &$value, mixed $key = null): void
    {
        if (is_string($value) && (str_starts_with($value, '{') || str_starts_with($value, '['))) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                $value = $decoded;
            }
        }
    }
}

if (!function_exists('scanAllDir')) {
    /**
     * Recursively scans directory structure and categorizes paths into files and directories.
     *
     * @param string $dir Base directory path to scan.
     *
     * @return array Associative array with 'files' and 'dirs' list arrays.
     */
    function scanAllDir(string $dir): array
    {
        $result = [
            'files' => [],
            'dirs'  => [],
        ];

        $dir = rtrim($dir, '/\\');
        if (!is_dir($dir)) {
            return $result;
        }

        $items = @scandir($dir);
        if ($items === false) {
            return $result;
        }

        foreach ($items as $filename) {
            if ($filename === '' || $filename[0] === '.') {
                continue;
            }

            $filePath = $dir . '/' . $filename;

            if (is_dir($filePath)) {
                $result['dirs'][] = $filePath;
                $subScan = scanAllDir($filePath);
                $result['files'] = array_merge($result['files'], $subScan['files']);
                $result['dirs'] = array_merge($result['dirs'], $subScan['dirs']);
            } else {
                $result['files'][] = $filePath;
            }
        }

        return $result;
    }
}

if (!function_exists('deleteFilesFolders')) {
    /**
     * Deletes a list of files or directories from the filesystem.
     *
     * @param array|string $filePaths Array of absolute filesystem paths or single path to remove.
     *
     * @return bool True if all deletions succeeded, false on failure.
     */
    function deleteFilesFolders(array|string $filePaths): bool
    {
        $paths = is_array($filePaths) ? $filePaths : [$filePaths];

        foreach ($paths as $filePath) {
            if (!file_exists($filePath)) {
                continue;
            }

            if (is_file($filePath)) {
                if (false === @unlink($filePath) || file_exists($filePath)) {
                    return false;
                }
            } elseif (is_dir($filePath)) {
                if (false === @rmdir($filePath)) {
                    return false;
                }
            }
        }

        return true;
    }
}

if (!function_exists('flatten_array')) {
    /**
     * Flattens a multi-dimensional array into a single one-dimensional array of values.
     *
     * @param array $items Multi-dimensional nested array.
     *
     * @return array One-dimensional flat array of values.
     */
    function flatten_array(array $items): array
    {
        if (empty($items)) {
            return [];
        }

        return iterator_to_array(
            new \RecursiveIteratorIterator(
                new \RecursiveArrayIterator($items)
            ),
            false
        );
    }
}

if (!function_exists('true_flatten')) {
    /**
     * Flattens a nested array into key-value pairs where nested keys are joined by underscores.
     *
     * @param array $array   Array to flatten.
     * @param array $parents Parent key path hierarchy.
     *
     * @return array Flattened associative array with underscore-separated keys.
     */
    function true_flatten(array $array, array $parents = []): array
    {
        $return = [];

        foreach ($array as $k => $value) {
            $p = empty($parents) ? [$k] : [...$parents, $k];
            if (is_array($value)) {
                $return = array_merge($return, true_flatten($value, $p));
            } else {
                $return[implode('_', $p)] = $value;
            }
        }

        return $return;
    }
}

if (!function_exists('convertObjToArr')) {
    /**
     * Converts an object's properties into an associative array using Reflection.
     *
     * @param object $object Object instance to convert.
     *
     * @return array Associative array of object properties and values.
     */
    function convertObjToArr(object $object): array
    {
        $reflection = new \ReflectionClass($object);
        $objectArray = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            if ($property->isInitialized($object)) {
                $objectArray[$property->getName()] = $property->getValue($object);
            } else {
                $objectArray[$property->getName()] = null;
            }
        }

        return $objectArray;
    }
}

if (!function_exists('getObjectProperty')) {
    /**
     * Retrieves the value of a specific property from an object instance.
     *
     * @param object $object Object instance.
     * @param string $name   Property name to retrieve.
     *
     * @return mixed Property value or null if not found.
     */
    function getObjectProperty(object $object, string $name): mixed
    {
        try {
            $reflection = new \ReflectionClass($object);
            if ($reflection->hasProperty($name)) {
                $prop = $reflection->getProperty($name);
                $prop->setAccessible(true);
                return $prop->isInitialized($object) ? $prop->getValue($object) : null;
            }
        } catch (\ReflectionException) {
            return null;
        }

        if (property_exists($object, $name)) {
            return $object->{$name};
        }

        return null;
    }
}

if (!function_exists('xmlToArray')) {
    /**
     * Parses an XML file or XML string into an associative array.
     *
     * @param string $xml  XML filepath or raw XML string.
     * @param string $type Input type indicator ('file' or 'string').
     *
     * @return array|false Parsed array or false on failure.
     */
    function xmlToArray(string $xml, string $type = 'file'): array|false
    {
        if ($type === 'file') {
            if (!file_exists($xml)) {
                return false;
            }
            $xmlElement = @simplexml_load_file($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {
            $xmlElement = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOWARNING | LIBXML_NOERROR);
        }

        if ($xmlElement === false) {
            return false;
        }

        $json = json_encode($xmlElement);
        if ($json === false) {
            return false;
        }

        return json_decode($json, true) ?: [];
    }
}

if (!function_exists('checkCtype')) {
    /**
     * Validates and optionally strips ignored characters from a string using ctype validation.
     *
     * @param string     $str          String to validate.
     * @param string     $ctype        Validation rule ('alnum', 'alpha', or 'digits').
     * @param array|null $ignoreChars  Array of character sequences to strip before validation.
     * @param bool       $replaceChars Whether to return stripped string or original string on match.
     *
     * @return string|false Filtered/original string if validation passes, false otherwise.
     */
    function checkCtype(
        string $str,
        string $ctype = 'alnum',
        ?array $ignoreChars = null,
        bool $replaceChars = true
    ): string|false {
        $ignore = $ignoreChars ?? [' ', '&amp;', '&', '.', ',', ':', ';', '&#64;', '@'];
        $string = trim(str_replace($ignore, '', $str));

        $valid = match ($ctype) {
            'alnum'  => ctype_alnum($string),
            'alpha'  => ctype_alpha($string),
            'digits' => ctype_digit($string),
            default  => false,
        };

        if ($valid) {
            return $replaceChars ? $string : $str;
        }

        return false;
    }
}

if (!function_exists('msort')) {
    /**
     * Sorts a multi-dimensional array by one or more column keys.
     *
     * @param array            $array       Target array of records to sort.
     * @param array|string     $key         Single column key name or array of column keys to sort by.
     * @param int              $sort_flags  Standard PHP sort flags (e.g. SORT_REGULAR, SORT_NUMERIC).
     * @param int              $order       Sort direction (SORT_ASC or SORT_DESC).
     * @param bool             $preserveKey Whether to preserve original array keys.
     *
     * @return array Sorted array.
     */
    function msort(
        array $array,
        array|string $key,
        int $sort_flags = SORT_REGULAR,
        int $order = SORT_ASC,
        bool $preserveKey = false
    ): array {
        if (empty($array) || empty($key)) {
            return $array;
        }

        $mapping = [];
        foreach ($array as $k => $v) {
            $sortKey = '';
            if (is_array($key)) {
                foreach ($key as $subKey) {
                    $sortKey .= (string) ($v[$subKey] ?? '');
                }
            } else {
                $sortKey = (string) ($v[$key] ?? '');
            }
            $mapping[$k] = $sortKey;
        }

        if ($order === SORT_ASC) {
            asort($mapping, $sort_flags);
        } else {
            arsort($mapping, $sort_flags);
        }

        $sorted = [];
        foreach ($mapping as $k => $v) {
            if ($preserveKey) {
                $sorted[$k] = $array[$k];
            } else {
                $sorted[] = $array[$k];
            }
        }

        return $sorted;
    }
}

if (!function_exists('array_merge_recursive_ex')) {
    /**
     * Recursively merges two arrays, overwriting scalar values and appending unique numeric entries.
     *
     * @param array $array1 Base array.
     * @param array $array2 Overriding array to merge into base.
     *
     * @return array Merged array.
     */
    function array_merge_recursive_ex(array $array1, array $array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_ex($merged[$key], $value);
            } elseif (is_numeric($key)) {
                if (!in_array($value, $merged, true)) {
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
    /**
     * Distinctly merges multiple arrays recursively without duplicating scalar values into sub-arrays.
     *
     * @param array ...$arrays Multiple arrays passed as arguments.
     *
     * @return array Merged array.
     */
    function array_merge_recursive_distinct(...$arrays): array
    {
        if (empty($arrays)) {
            return [];
        }

        $base = array_shift($arrays);
        if (!is_array($base)) {
            $base = empty($base) ? [] : [$base];
        }

        foreach ($arrays as $append) {
            if (!is_array($append)) {
                $append = [$append];
            }

            foreach ($append as $key => $value) {
                if (!array_key_exists($key, $base) && !is_numeric($key)) {
                    $base[$key] = $append[$key];
                    continue;
                }

                if (is_array($value) || (isset($base[$key]) && is_array($base[$key]))) {
                    $baseSub = $base[$key] ?? [];
                    $appendSub = is_array($append[$key]) ? $append[$key] : [$append[$key]];
                    $base[$key] = array_merge_recursive_distinct($baseSub, $appendSub);
                } else {
                    if (is_numeric($key)) {
                        if (!in_array($value, $base, true)) {
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
    /**
     * Deeply merges arrays passed as variable arguments using Drupal deep merge semantics.
     *
     * @param array ...$arrays Multiple arrays to merge.
     *
     * @return array Deeply merged array.
     */
    function drupal_array_merge_deep(...$arrays): array
    {
        return drupal_array_merge_deep_array($arrays);
    }
}

if (!function_exists('drupal_array_merge_deep_array')) {
    /**
     * Merges multiple arrays deeply while renumbering integer keys and merging nested associative structures.
     *
     * @param array $arrays List of arrays to merge.
     *
     * @return array Merged array.
     */
    function drupal_array_merge_deep_array(array $arrays): array
    {
        $result = [];

        foreach ($arrays as $array) {
            if (!is_array($array)) {
                continue;
            }

            foreach ($array as $key => $value) {
                if (is_int($key)) {
                    $result[] = $value;
                } elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = drupal_array_merge_deep_array([$result[$key], $value]);
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}

if (!function_exists('prefix_get_next_key_array')) {
    /**
     * Retrieves the next array key immediately following a given key.
     *
     * @param array      $arr Target array.
     * @param int|string $key Target key reference.
     *
     * @return int|string|null The subsequent key or null if not found or at the end.
     */
    function prefix_get_next_key_array(array $arr, int|string $key): int|string|null
    {
        $keys = array_keys($arr);
        $position = array_search($key, $keys, true);

        if ($position !== false && isset($keys[$position + 1])) {
            return $keys[$position + 1];
        }

        return null;
    }
}

if (!function_exists('recursive_array_search')) {
    /**
     * Recursively searches for a value in a nested array structure.
     *
     * @param mixed           $needle    Value to search for.
     * @param array           $haystack  Array to search within.
     * @param int|string|null $needleKey Optional specific key name to require.
     *
     * @return int|string|false Top-level or matching key if found, false otherwise.
     */
    function recursive_array_search(mixed $needle, array $haystack, int|string|null $needleKey = null): int|string|false
    {
        foreach ($haystack as $key => $value) {
            if ($needleKey !== null) {
                if (($key == $needleKey && $value == $needle) ||
                    (is_array($value) && recursive_array_search($needle, $value, $needleKey) !== false)
                ) {
                    return $key;
                }
            } else {
                if ($value == $needle ||
                    (is_array($value) && recursive_array_search($needle, $value, $needleKey) !== false)
                ) {
                    return $key;
                }
            }
        }

        return false;
    }
}

if (!function_exists('array_diff_assoc_recursive')) {
    /**
     * Computes the difference between two multi-dimensional arrays with index checking recursively.
     *
     * @param array $array1 Base array to compare from.
     * @param array $array2 Array to compare against.
     *
     * @return array|int Difference array or 0 if identical.
     */
    function array_diff_assoc_recursive(array $array1, array $array2): array|int
    {
        $difference = [];

        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } elseif (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $newDiff = array_diff_assoc_recursive($value, $array2[$key]);
                    if ($newDiff !== 0 && !empty($newDiff)) {
                        $difference[$key] = $newDiff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        return empty($difference) ? 0 : $difference;
    }
}

if (!function_exists('array_get_values_recursive')) {
    /**
     * Recursively extracts values matching specific target keys from a nested array.
     *
     * @param array $keys Target keys to extract.
     * @param array $arr  Nested source array.
     *
     * @return mixed Array of matched key-value pairs or single matched entry.
     */
    function array_get_values_recursive(array $keys = [], array $arr = []): mixed
    {
        $val = [];

        array_walk_recursive($arr, function (mixed $v, mixed $k) use ($keys, &$val): void {
            if (in_array($k, $keys, true)) {
                $val[] = [$k => $v];
            }
        });

        return count($val) > 1 ? $val : (count($val) === 1 ? array_pop($val) : []);
    }
}

if (!function_exists('extractLineFromFile')) {
    /**
     * Reads a file line by line and extracts the value token from the first line matching a prefix word.
     *
     * @param string $file Absolute filesystem path to source file.
     * @param string $word Prefix word to match.
     *
     * @return string|null Extracted value token or null if not matched.
     */
    function extractLineFromFile(string $file, string $word): ?string
    {
        if (!file_exists($file) || !is_readable($file)) {
            return null;
        }

        $lineWithWord = null;
        $handle = @fopen($file, 'r');

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (str_starts_with($line, $word)) {
                    $parts = explode(' ', $line);
                    if (isset($parts[1])) {
                        $lineWithWord = rtrim(trim($parts[1]), ';');
                    }
                    break;
                }
            }
            fclose($handle);
        }

        return $lineWithWord;
    }
}

if (!function_exists('arraySqueeze')) {
    /**
     * Filters an associative array by keeping only specified keys or unsetting specified keys.
     *
     * @param array  $array Target array to modify.
     * @param array  $keys  List of keys to filter on.
     * @param string $task  Filter operation: 'keep' to retain only $keys, 'unset' to remove $keys.
     *
     * @return array Squeezed array.
     */
    function arraySqueeze(array $array, array $keys, string $task = 'keep'): array
    {
        foreach ($array as $key => $value) {
            if ($task === 'keep') {
                if (!in_array($key, $keys, true)) {
                    unset($array[$key]);
                }
            } elseif ($task === 'unset') {
                if (in_array($key, $keys, true)) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }
}

if (!function_exists('arrayFilterKeywords')) {
    /**
     * Filters array elements based on substring keyword inclusion or exclusion.
     *
     * @param array  $array    Target array to filter.
     * @param array  $keywords Array of substring keywords to search for.
     * @param string $task     Filter operation: 'keep' to retain elements containing keywords, 'unset' to remove.
     *
     * @return array Filtered array.
     */
    function arrayFilterKeywords(array $array, array $keywords, string $task = 'keep'): array
    {
        foreach ($array as $key => $value) {
            $valueStr = is_scalar($value) ? (string) $value : '';

            if ($task === 'keep') {
                $matched = false;
                foreach ($keywords as $keyword) {
                    if (str_contains($valueStr, (string) $keyword)) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    unset($array[$key]);
                }
            } elseif ($task === 'unset') {
                foreach ($keywords as $keyword) {
                    if (str_contains($valueStr, (string) $keyword)) {
                        unset($array[$key]);
                        break;
                    }
                }
            }
        }

        return $array;
    }
}

if (!function_exists('printArrayList')) {
    /**
     * Generates an HTML nested unordered list (<ul><li>) representation of an array.
     *
     * @param array $array Array data to render as HTML list.
     *
     * @return string Formatted HTML list markup.
     */
    function printArrayList(array $array): string
    {
        $html = '<ul>';

        foreach ($array as $k => $v) {
            if (is_string($v) && str_starts_with($v, '{') && str_ends_with($v, '}')) {
                $decoded = @json_decode($v, true);
                if (is_array($decoded)) {
                    $v = $decoded;
                }
            }

            if (is_array($v)) {
                $html .= '<li>' . htmlspecialchars((string) $k, ENT_QUOTES, 'UTF-8') . ' : </li>';
                $html .= '    ' . printArrayList($v);
            } else {
                $html .= '<li>' . htmlspecialchars((string) $k, ENT_QUOTES, 'UTF-8') . ' : ' . htmlspecialchars((string) ($v ?? 'null'), ENT_QUOTES, 'UTF-8') . '</li>';
            }
        }

        $html .= '</ul>';

        return $html;
    }
}

if (!function_exists('arrayReplace')) {
    /**
     * Recursively searches for a specific key in a nested array and replaces its value.
     *
     * @param array      $array   Target array to search and modify.
     * @param int|string $findKey Key identifier to find.
     * @param mixed      $replace Replacement value to assign.
     *
     * @return array Modified array.
     */
    function arrayReplace(array $array, int|string $findKey, mixed $replace): array
    {
        foreach ($array as $key => $val) {
            if ($key === $findKey) {
                $array[$key] = $replace;
            } elseif (is_array($val)) {
                $array[$key] = arrayReplace($val, $findKey, $replace);
            }
        }

        return $array;
    }
}

if (!function_exists('getRemoteFilesize')) {
    /**
     * Retrieves the file size of a remote resource using HTTP headers.
     *
     * @author  Stephan Schmitz <eyecatchup@gmail.com>
     * @license MIT <http://eyecatchup.mit-license.org/>
     * @link    <https://gist.github.com/eyecatchup/f26300ffd7e50a92bc4d>
     * @param string $url        Remote resource URL.
     * @param bool   $formatSize Whether to return human-readable string (e.g. '12.5 MiB') or integer bytes.
     * @param bool   $useHead    Whether to send HEAD request instead of GET.
     *
     * @return int|string|false File size in bytes or formatted string, or false on retrieval error.
     */
    function getRemoteFilesize(string $url, bool $formatSize = false, bool $useHead = false): int|string|false
    {
        if ($useHead) {
            stream_context_set_default(['http' => ['method' => 'HEAD']]);
        }

        $headers = @get_headers($url, true);
        if ($headers === false) {
            return false;
        }

        $head = array_change_key_case($headers);
        $clen = $head['content-length'] ?? 0;

        if (!$clen) {
            return false;
        }

        $size = 0;

        if (is_array($clen)) {
            foreach ($clen as $len) {
                if ((int) $len > 0) {
                    $size = (int) $len;
                    break;
                }
            }
        } else {
            $size = (int) $clen;
        }

        if ($size <= 0) {
            return false;
        }

        if ($formatSize) {
            return match (true) {
                $size < 1024          => $size . ' B',
                $size < 1048576       => round($size / 1024, 2) . ' KiB',
                $size < 1073741824    => round($size / 1048576, 2) . ' MiB',
                default               => round($size / 1073741824, 2) . ' GiB',
            };
        }

        return $size;
    }
}

if (!function_exists('numberFormatPrecision')) {
    /**
     * Formats a floating-point number to a precise decimal precision without mathematical rounding.
     *
     * @param float|int|string $number    Input numeric value.
     * @param int              $precision Number of decimal digits to preserve.
     * @param string           $separator Decimal separator character.
     *
     * @return float Formatted float value.
     */
    function numberFormatPrecision(float|int|string $number, int $precision = 2, string $separator = '.'): float
    {
        $numberParts = explode($separator, (string) $number);
        $response = $numberParts[0];

        if (count($numberParts) > 1 && $precision > 0) {
            $response .= '.' . substr($numberParts[1], 0, $precision);
        }

        if ($response === '-0' || $response === '-0.0' || $response === '0') {
            return 0.0;
        }

        return (float) $response;
    }
}

if (!function_exists('findKeysByValue')) {
    /**
     * Recursively searches for all array path keys containing a specific value.
     *
     * @param array $array  Target array to search.
     * @param mixed $search Value to locate.
     * @param array $keys   Accumulated key hierarchy.
     *
     * @return array List of matching key paths or empty array if not found.
     */
    function findKeysByValue(array $array, mixed $search, array $keys = []): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $subPath = findKeysByValue($value, $search, array_merge($keys, [$key]));
                if (!empty($subPath)) {
                    return $subPath;
                }
            } elseif ($value === $search) {
                return array_merge($keys, [$key]);
            }
        }

        return [];
    }
}

if (!function_exists('findKeyLocation')) {
    /**
     * Recursively traverses an array to find the exact hierarchy path of a specific key.
     *
     * @param array      $array     Target array to search.
     * @param int|string $searchKey Key name to locate.
     * @param array      $path      Accumulated key path hierarchy.
     *
     * @return array|null Key path array if found, null otherwise.
     */
    function findKeyLocation(array $array, int|string $searchKey, array $path = []): ?array
    {
        foreach ($array as $key => $value) {
            $currentPath = array_merge($path, [$key]);

            if ($key === $searchKey) {
                return $currentPath;
            }

            if (is_array($value)) {
                $result = findKeyLocation($value, $searchKey, $currentPath);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }
}

if (!function_exists('command_exists')) {
    /**
     * Determines whether a specific CLI shell command exists and is executable on the host system.
     *
     * @param string $command Name of the binary command to evaluate.
     *
     * @return bool True if executable binary exists in PATH, false otherwise.
     */
    function command_exists(string $command): bool
    {
        $isWindows = (false !== stripos(PHP_OS, 'win'));
        $testCommand = $isWindows ? 'where ' . escapeshellarg($command) : 'command -v ' . escapeshellarg($command);

        $output = @shell_exec($testCommand);

        return !is_null($output) && trim($output) !== '';
    }
}