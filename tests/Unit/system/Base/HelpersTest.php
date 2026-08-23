<?php

/**
 * SP Framework
 *
 * @package   Tests\System\Base
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\System\Base;

use Codeception\Test\Unit;
use Tests\Support\UnitTester;

/**
 * Class TestDummyObject
 *
 * Test object fixture for testing reflection and property extraction helpers.
 *
 * @package Tests\System\Base
 */
class TestDummyObject
{
    /**
     * Public test property.
     *
     * @var string
     */
    public string $publicProp = 'publicValue';

    /**
     * Protected test property.
     *
     * @var int
     */
    protected int $protectedProp = 42;

    /**
     * Private test property.
     *
     * @var bool
     */
    private bool $privateProp = true;
}

/**
 * Class HelpersTest
 *
 * Comprehensive unit test suite evaluating all procedural helper utilities in system/Base/Helpers.php.
 * Covers path resolution, debugging, size conversion, array manipulation, file operations, string utilities, and system commands.
 *
 * @package Tests\System\Base
 */
class HelpersTest extends Unit
{
    /**
     * Unit tester actor instance.
     *
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * Path to temporary test directory.
     *
     * @var string
     */
    protected string $tempDir;

    /**
     * Sets up test environment before each test execution.
     *
     * @return void
     */
    protected function _before(): void
    {
        require_once dirname(__DIR__, 4) . '/system/Base/Helpers.php';

        $this->tempDir = sys_get_temp_dir() . '/sp_helpers_test_' . uniqid('', true);
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    /**
     * Cleans up temporary test files after each test execution.
     *
     * @return void
     */
    protected function _after(): void
    {
        if (is_dir($this->tempDir)) {
            deleteFilesFolders(scanAllDir($this->tempDir)['files']);
            deleteFilesFolders(scanAllDir($this->tempDir)['dirs']);
            @rmdir($this->tempDir);
        }
    }

    /**
     * Tests base_path resolving root and subpath directories.
     *
     * @return void
     */
    public function testBasePath(): void
    {
        $base = base_path();
        $this->assertNotEmpty($base);
        $this->assertTrue(is_dir($base));

        $baseFromNull = base_path(null);
        $this->assertSame($base, $baseFromNull);

        $subPath = base_path('system/Base');
        $this->assertStringContainsString('system' . DIRECTORY_SEPARATOR . 'Base', $subPath);
        $this->assertTrue(is_dir($subPath));
    }

    /**
     * Tests trace function with returnTraces enabled.
     *
     * @return void
     */
    public function testTraceWithReturnTraces(): void
    {
        $traces = trace([], false, false, false, true, true, true, true, true, false);

        $this->assertIsArray($traces);
        $this->assertNotEmpty($traces);
        $this->assertArrayHasKey('file', $traces[0]);
        $this->assertArrayHasKey('line', $traces[0]);
        $this->assertArrayHasKey('function', $traces[0]);
    }

    /**
     * Tests json_trace serializing exceptions into valid JSON.
     *
     * @return void
     */
    public function testJsonTrace(): void
    {
        $exception = new \RuntimeException('Database connection lost', 503);
        $json = json_trace($exception);

        $this->assertIsString($json);
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertSame('RuntimeException', $data['class']);
        $this->assertSame('Database connection lost', $data['message']);
        $this->assertSame(503, $data['code']);
        $this->assertNotEmpty($data['file']);
        $this->assertGreaterThan(0, $data['line']);
        $this->assertIsArray($data['originalTrace']);
    }

    /**
     * Tests toBytes conversion of human-readable units and raw numbers.
     *
     * @return void
     */
    public function testToBytes(): void
    {
        $this->assertSame(1024, toBytes(1024));
        $this->assertSame(1024, toBytes('1K'));
        $this->assertSame(1024, toBytes('1KB'));
        $this->assertSame(1048576, toBytes('1M'));
        $this->assertSame(1048576, toBytes('1MB'));
        $this->assertSame(1073741824, toBytes('1G'));
        $this->assertSame(1073741824, toBytes('1GB'));
        $this->assertSame(1099511627776, toBytes('1T'));
        $this->assertSame(1099511627776, toBytes('1TB'));
        $this->assertSame(2097152, toBytes('2M'));
        $this->assertSame(500, toBytes('500B'));
        $this->assertSame(0, toBytes(''));
    }

    /**
     * Tests json_decode_recursive decoding embedded JSON strings within arrays.
     *
     * @return void
     */
    public function testJsonDecodeRecursive(): void
    {
        $data = [
            'raw'      => 'plain string',
            'json_obj' => '{"name":"Alice","role":"admin"}',
            'json_arr' => '[1,2,3,4]',
            'number'   => 123,
        ];

        array_walk($data, 'json_decode_recursive');

        $this->assertSame('plain string', $data['raw']);
        $this->assertSame(['name' => 'Alice', 'role' => 'admin'], $data['json_obj']);
        $this->assertSame([1, 2, 3, 4], $data['json_arr']);
        $this->assertSame(123, $data['number']);
    }

    /**
     * Tests scanAllDir recursively indexing directory contents and ignoring dotfiles.
     *
     * @return void
     */
    public function testScanAllDir(): void
    {
        $subDir = $this->tempDir . '/nested';
        mkdir($subDir, 0777, true);

        file_put_contents($this->tempDir . '/file1.txt', 'file 1');
        file_put_contents($subDir . '/file2.txt', 'file 2');
        file_put_contents($this->tempDir . '/.hidden', 'hidden file');

        $scanned = scanAllDir($this->tempDir);

        $this->assertIsArray($scanned);
        $this->assertArrayHasKey('files', $scanned);
        $this->assertArrayHasKey('dirs', $scanned);

        $this->assertCount(2, $scanned['files']);
        $this->assertContains($this->tempDir . '/file1.txt', $scanned['files']);
        $this->assertContains($subDir . '/file2.txt', $scanned['files']);
        $this->assertNotContains($this->tempDir . '/.hidden', $scanned['files']);

        $this->assertContains($subDir, $scanned['dirs']);
    }

    /**
     * Tests deleteFilesFolders removing files and directories.
     *
     * @return void
     */
    public function testDeleteFilesFolders(): void
    {
        $fileA = $this->tempDir . '/del_a.txt';
        $fileB = $this->tempDir . '/del_b.txt';
        $subFolder = $this->tempDir . '/empty_sub';

        file_put_contents($fileA, 'to delete');
        file_put_contents($fileB, 'to delete');
        mkdir($subFolder, 0777, true);

        $this->assertTrue(file_exists($fileA));
        $this->assertTrue(file_exists($fileB));
        $this->assertTrue(is_dir($subFolder));

        $result = deleteFilesFolders([$fileA, $fileB, $subFolder]);

        $this->assertTrue($result);
        $this->assertFalse(file_exists($fileA));
        $this->assertFalse(file_exists($fileB));
        $this->assertFalse(is_dir($subFolder));
    }

    /**
     * Tests flatten_array flattening multi-dimensional array into flat values list.
     *
     * @return void
     */
    public function testFlattenArray(): void
    {
        $nested = [
            'a' => 1,
            'b' => [
                'c' => 2,
                'd' => [3, 4],
            ],
            'e' => 5,
        ];

        $flat = flatten_array($nested);

        $this->assertSame([1, 2, 3, 4, 5], $flat);
        $this->assertSame([], flatten_array([]));
    }

    /**
     * Tests true_flatten flattening nested array with underscore-joined key hierarchy.
     *
     * @return void
     */
    public function testTrueFlatten(): void
    {
        $nested = [
            'user' => [
                'profile' => [
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                ],
                'active'  => true,
            ],
            'status' => 'ok',
        ];

        $flattened = true_flatten($nested);

        $expected = [
            'user_profile_first_name' => 'John',
            'user_profile_last_name'  => 'Doe',
            'user_active'             => true,
            'status'                  => 'ok',
        ];

        $this->assertSame($expected, $flattened);
    }

    /**
     * Tests convertObjToArr extracting initialized properties from object instance.
     *
     * @return void
     */
    public function testConvertObjToArr(): void
    {
        $obj = new TestDummyObject();
        $arr = convertObjToArr($obj);

        $this->assertIsArray($arr);
        $this->assertArrayHasKey('publicProp', $arr);
        $this->assertArrayHasKey('protectedProp', $arr);
        $this->assertArrayHasKey('privateProp', $arr);
        $this->assertSame('publicValue', $arr['publicProp']);
        $this->assertSame(42, $arr['protectedProp']);
        $this->assertTrue($arr['privateProp']);
    }

    /**
     * Tests getObjectProperty accessing property values.
     *
     * @return void
     */
    public function testGetObjectProperty(): void
    {
        $obj = new TestDummyObject();

        $this->assertSame('publicValue', getObjectProperty($obj, 'publicProp'));
        $this->assertSame(42, getObjectProperty($obj, 'protectedProp'));
        $this->assertTrue(getObjectProperty($obj, 'privateProp'));
        $this->assertNull(getObjectProperty($obj, 'nonExistentProperty'));
    }

    /**
     * Tests xmlToArray parsing XML strings and files.
     *
     * @return void
     */
    public function testXmlToArray(): void
    {
        $xmlString = '<root><user id="1"><name>Alice</name><email>alice@example.com</email></user></root>';
        $parsed = xmlToArray($xmlString, 'string');

        $this->assertIsArray($parsed);
        $this->assertArrayHasKey('user', $parsed);
        $this->assertSame('Alice', $parsed['user']['name']);
        $this->assertSame('alice@example.com', $parsed['user']['email']);

        $xmlFile = $this->tempDir . '/test.xml';
        file_put_contents($xmlFile, $xmlString);

        $parsedFile = xmlToArray($xmlFile, 'file');
        $this->assertIsArray($parsedFile);
        $this->assertSame('Alice', $parsedFile['user']['name']);

        $this->assertFalse(xmlToArray($this->tempDir . '/non_existent.xml', 'file'));
        $this->assertFalse(xmlToArray('invalid <xml syntax', 'string'));
    }

    /**
     * Tests checkCtype validating alnum, alpha, and digits strings.
     *
     * @return void
     */
    public function testCheckCtype(): void
    {
        $this->assertSame('AlphaNumeric123', checkCtype('AlphaNumeric123', 'alnum'));
        $this->assertSame('AlphaNumeric', checkCtype('Alpha Numeric &amp; .', 'alnum', null, true));
        $this->assertSame('Alpha Numeric &amp; .', checkCtype('Alpha Numeric &amp; .', 'alnum', null, false));

        $this->assertSame('OnlyLetters', checkCtype('OnlyLetters', 'alpha'));
        $this->assertFalse(checkCtype('Letters123', 'alpha'));

        $this->assertSame('123456', checkCtype('123456', 'digits'));
        $this->assertFalse(checkCtype('123a456', 'digits'));
        $this->assertFalse(checkCtype('invalid!', 'unknown_rule'));
    }

    /**
     * Tests msort sorting multi-dimensional arrays by columns and preserving keys.
     *
     * @return void
     */
    public function testMsort(): void
    {
        $records = [
            ['id' => 3, 'name' => 'Charlie', 'score' => 80],
            ['id' => 1, 'name' => 'Alice',   'score' => 95],
            ['id' => 2, 'name' => 'Bob',     'score' => 90],
        ];

        $sortedAsc = msort($records, 'id', SORT_REGULAR, SORT_ASC);
        $this->assertSame('Alice', $sortedAsc[0]['name']);
        $this->assertSame('Bob', $sortedAsc[1]['name']);
        $this->assertSame('Charlie', $sortedAsc[2]['name']);

        $sortedDesc = msort($records, 'score', SORT_REGULAR, SORT_DESC);
        $this->assertSame('Alice', $sortedDesc[0]['name']);
        $this->assertSame(95, $sortedDesc[0]['score']);
        $this->assertSame('Charlie', $sortedDesc[2]['name']);

        $multiKeySort = msort($records, ['name', 'id'], SORT_REGULAR, SORT_ASC);
        $this->assertSame('Alice', $multiKeySort[0]['name']);

        $assocRecords = [
            'row3' => ['id' => 3, 'name' => 'C'],
            'row1' => ['id' => 1, 'name' => 'A'],
        ];
        $preserved = msort($assocRecords, 'id', SORT_REGULAR, SORT_ASC, true);
        $this->assertSame(['row1', 'row3'], array_keys($preserved));
    }

    /**
     * Tests array_merge_recursive_ex combining arrays with scalar overwrites.
     *
     * @return void
     */
    public function testArrayMergeRecursiveEx(): void
    {
        $array1 = [
            'name'     => 'Original',
            'settings' => ['theme' => 'dark', 'cache' => true],
            'tags'     => ['php', 'phalcon'],
        ];

        $array2 = [
            'name'     => 'Overridden',
            'settings' => ['theme' => 'light', 'debug' => false],
            'tags'     => ['phalcon', 'framework'],
        ];

        $merged = array_merge_recursive_ex($array1, $array2);

        $this->assertSame('Overridden', $merged['name']);
        $this->assertSame('light', $merged['settings']['theme']);
        $this->assertTrue($merged['settings']['cache']);
        $this->assertFalse($merged['settings']['debug']);
        $this->assertSame(['php', 'phalcon', 'framework'], $merged['tags']);
    }

    /**
     * Tests array_merge_recursive_distinct merging multiple arrays distinctly.
     *
     * @return void
     */
    public function testArrayMergeRecursiveDistinct(): void
    {
        $arr1 = ['a' => 1, 'nested' => ['k1' => 'v1']];
        $arr2 = ['a' => 2, 'nested' => ['k2' => 'v2'], 'b' => 3];
        $arr3 = ['c' => 4];

        $merged = array_merge_recursive_distinct($arr1, $arr2, $arr3);

        $this->assertSame(2, $merged['a']);
        $this->assertSame(3, $merged['b']);
        $this->assertSame(4, $merged['c']);
        $this->assertSame(['k1' => 'v1', 'k2' => 'v2'], $merged['nested']);
    }

    /**
     * Tests drupal_array_merge_deep and drupal_array_merge_deep_array helpers.
     *
     * @return void
     */
    public function testDrupalArrayMergeDeep(): void
    {
        $a = ['k' => ['sub' => 'one'], 'list' => [1, 2]];
        $b = ['k' => ['sub' => 'two', 'extra' => true], 'list' => [3]];

        $merged = drupal_array_merge_deep($a, $b);

        $this->assertSame('two', $merged['k']['sub']);
        $this->assertTrue($merged['k']['extra']);
        $this->assertSame([1, 2, 3], $merged['list']);
    }

    /**
     * Tests prefix_get_next_key_array locating following array key.
     *
     * @return void
     */
    public function testPrefixGetNextKeyArray(): void
    {
        $arr = ['first' => 1, 'second' => 2, 'third' => 3];

        $this->assertSame('second', prefix_get_next_key_array($arr, 'first'));
        $this->assertSame('third', prefix_get_next_key_array($arr, 'second'));
        $this->assertNull(prefix_get_next_key_array($arr, 'third'));
        $this->assertNull(prefix_get_next_key_array($arr, 'nonexistent'));
    }

    /**
     * Tests recursive_array_search finding values in nested structures.
     *
     * @return void
     */
    public function testRecursiveArraySearch(): void
    {
        $haystack = [
            'top1' => 'val1',
            'top2' => [
                'sub1' => 'targetValue',
                'sub2' => 'other',
            ],
            'top3' => [
                'deep' => ['leaf' => 'deepValue'],
            ],
        ];

        $this->assertSame('top1', recursive_array_search('val1', $haystack));
        $this->assertSame('top2', recursive_array_search('targetValue', $haystack));
        $this->assertSame('top3', recursive_array_search('deepValue', $haystack));
        $this->assertSame('top2', recursive_array_search('targetValue', $haystack, 'sub1'));
        $this->assertFalse(recursive_array_search('missingValue', $haystack));
    }

    /**
     * Tests array_diff_assoc_recursive calculating multi-dimensional array differences.
     *
     * @return void
     */
    public function testArrayDiffAssocRecursive(): void
    {
        $arr1 = [
            'same'   => 1,
            'diff'   => 'old',
            'nested' => ['k1' => 10, 'k2' => 20],
        ];

        $arr2 = [
            'same'   => 1,
            'diff'   => 'new',
            'nested' => ['k1' => 10, 'k2' => 99],
        ];

        $diff = array_diff_assoc_recursive($arr1, $arr2);

        $this->assertIsArray($diff);
        $this->assertSame('old', $diff['diff']);
        $this->assertSame(['k2' => 20], $diff['nested']);

        $identical = array_diff_assoc_recursive($arr1, $arr1);
        $this->assertSame(0, $identical);
    }

    /**
     * Tests array_get_values_recursive extracting values matching target keys.
     *
     * @return void
     */
    public function testArrayGetValuesRecursive(): void
    {
        $data = [
            'id'       => 1,
            'title'    => 'Post 1',
            'author'   => ['id' => 10, 'name' => 'Author 1'],
            'comments' => [
                ['id' => 100, 'text' => 'Great'],
            ],
        ];

        $ids = array_get_values_recursive(['id'], $data);
        $this->assertIsArray($ids);
        $this->assertCount(3, $ids);

        $single = array_get_values_recursive(['title'], $data);
        $this->assertSame(['title' => 'Post 1'], $single);

        $none = array_get_values_recursive(['nonexistent'], $data);
        $this->assertSame([], $none);
    }

    /**
     * Tests extractLineFromFile reading value by line prefix.
     *
     * @return void
     */
    public function testExtractLineFromFile(): void
    {
        $filePath = $this->tempDir . '/config.txt';
        $content = "name AppName;\nversion 1.2.3;\nenabled true;\n";
        file_put_contents($filePath, $content);

        $this->assertSame('AppName', extractLineFromFile($filePath, 'name'));
        $this->assertSame('1.2.3', extractLineFromFile($filePath, 'version'));
        $this->assertNull(extractLineFromFile($filePath, 'missing'));
        $this->assertNull(extractLineFromFile($this->tempDir . '/non_existent_file.txt', 'name'));
    }

    /**
     * Tests arraySqueeze retaining or removing specified keys.
     *
     * @return void
     */
    public function testArraySqueeze(): void
    {
        $data = ['id' => 1, 'name' => 'Test', 'secret' => '12345', 'token' => 'abc'];

        $kept = arraySqueeze($data, ['id', 'name'], 'keep');
        $this->assertSame(['id' => 1, 'name' => 'Test'], $kept);

        $unset = arraySqueeze($data, ['secret', 'token'], 'unset');
        $this->assertSame(['id' => 1, 'name' => 'Test'], $unset);
    }

    /**
     * Tests arrayFilterKeywords retaining or unsetting array elements by substring keywords.
     *
     * @return void
     */
    public function testArrayFilterKeywords(): void
    {
        $data = [
            'item1' => 'apple_fruit',
            'item2' => 'banana_fruit',
            'item3' => 'carrot_vegetable',
            'item4' => 'potato_vegetable',
        ];

        $fruitOnly = arrayFilterKeywords($data, ['fruit'], 'keep');
        $this->assertSame(['item1' => 'apple_fruit', 'item2' => 'banana_fruit'], $fruitOnly);

        $noVegetable = arrayFilterKeywords($data, ['vegetable'], 'unset');
        $this->assertSame(['item1' => 'apple_fruit', 'item2' => 'banana_fruit'], $noVegetable);
    }

    /**
     * Tests printArrayList formatting nested arrays and JSON strings into HTML unordered lists.
     *
     * @return void
     */
    public function testPrintArrayList(): void
    {
        $data = [
            'title'   => 'Overview',
            'details' => ['views' => 100, 'rating' => 4.5],
            'meta'    => '{"status":"active"}',
        ];

        $html = printArrayList($data);

        $this->assertStringStartsWith('<ul>', $html);
        $this->assertStringEndsWith('</ul>', $html);
        $this->assertStringContainsString('Overview', $html);
        $this->assertStringContainsString('views : 100', $html);
        $this->assertStringContainsString('status : active', $html);
    }

    /**
     * Tests arrayReplace recursively replacing values by key.
     *
     * @return void
     */
    public function testArrayReplace(): void
    {
        $data = [
            'env'    => 'dev',
            'sub'    => ['env' => 'dev', 'port' => 8080],
            'nested' => ['deep' => ['env' => 'dev']],
        ];

        $replaced = arrayReplace($data, 'env', 'prod');

        $this->assertSame('prod', $replaced['env']);
        $this->assertSame('prod', $replaced['sub']['env']);
        $this->assertSame(8080, $replaced['sub']['port']);
        $this->assertSame('prod', $replaced['nested']['deep']['env']);
    }

    /**
     * Tests numberFormatPrecision formatting numbers without rounding.
     *
     * @return void
     */
    public function testNumberFormatPrecision(): void
    {
        $this->assertSame(123.45, numberFormatPrecision(123.4567, 2));
        $this->assertSame(123.456, numberFormatPrecision(123.4567, 3));
        $this->assertSame(123.0, numberFormatPrecision(123.00, 2));
        $this->assertSame(0.0, numberFormatPrecision(0, 2));
        $this->assertSame(0.0, numberFormatPrecision('-0', 2));
        $this->assertSame(123.45, numberFormatPrecision('123,4567', 2, ','));
    }

    /**
     * Tests findKeysByValue locating hierarchical paths containing matching value.
     *
     * @return void
     */
    public function testFindKeysByValue(): void
    {
        $data = [
            'level1' => [
                'level2' => [
                    'targetKey' => 'uniqueValue',
                ],
            ],
            'other'  => 'val',
        ];

        $path = findKeysByValue($data, 'uniqueValue');
        $this->assertSame(['level1', 'level2', 'targetKey'], $path);

        $empty = findKeysByValue($data, 'nonexistent');
        $this->assertSame([], $empty);
    }

    /**
     * Tests findKeyLocation locating hierarchy path for specific target key name.
     *
     * @return void
     */
    public function testFindKeyLocation(): void
    {
        $data = [
            'app' => [
                'database' => [
                    'credentials' => [
                        'password' => 'secret',
                    ],
                ],
            ],
        ];

        $path = findKeyLocation($data, 'password');
        $this->assertSame(['app', 'database', 'credentials', 'password'], $path);

        $this->assertNull(findKeyLocation($data, 'missingKey'));
    }

    /**
     * Tests command_exists evaluating existence of shell executables.
     *
     * @return void
     */
    public function testCommandExists(): void
    {
        $this->assertTrue(command_exists('php'));
        $this->assertTrue(command_exists('sh') || command_exists('bash'));
        $this->assertFalse(command_exists('non_existent_binary_xyz_12345'));
    }
}
