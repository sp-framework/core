<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff;

use Closure;
use Exception;
use ReflectionFunction;
use System\Base\Providers\DatabaseServiceProvider\Ff\Classes\IoHelper;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;

class Cache
{
    const DEFAULT_CACHE_DIR = "cache/";

    const NO_LIFETIME_FILE_STRING = "no_lifetime";

    protected $lifetime;

    protected $cachePath = "";

    protected $cacheDir = "";

    protected $tokenArray;

    public function __construct(string $storePath, array &$cacheTokenArray, $cacheLifetime)
    {
        $this->setCachePath($storePath);

        $this->setTokenArray($cacheTokenArray);

        $this->lifetime = $cacheLifetime;
    }

    protected function setCachePath(string $storePath)
    {
        $cachePath = "";

        if (!empty($storePath)) {
            IoHelper::normalizeDirectory($storePath);

            $cachePath = $storePath . $this->getCacheDir();
        }

        $this->cachePath = $cachePath;
    }

    public function getToken(): string
    {
        return md5(json_encode(self::convertClosuresToString($this->tokenArray)));
    }

    public function deleteAll(): bool
    {
        return IoHelper::deleteFiles(glob($this->cachePath . "*"));
    }

    public function deleteAllWithNoLifetime(): bool
    {
        $noLifetimeFileString = self::NO_LIFETIME_FILE_STRING;

        return IoHelper::deleteFiles(glob($this->cachePath . "*.$noLifetimeFileString.json"));
    }

    public function set(array $content)
    {
        $lifetime = $this->lifetime;

        $cachePath = $this->cachePath;

        $token = $this->getToken();

        $noLifetimeFileString = self::NO_LIFETIME_FILE_STRING;

        $cacheFile = $cachePath . $token . ".$noLifetimeFileString.json";

        if (is_int($lifetime)) {
            $cacheFile = $cachePath . $token . ".$lifetime.json";
        }

        IoHelper::writeContentToFile($cacheFile, json_encode($content));
    }

    public function get()
    {
        $cachePath = $this->cachePath;
        $token = $this->getToken();

        $cacheFile = null;

        IoHelper::checkRead($cachePath);

        $cacheFiles = glob($cachePath.$token."*.json");

        if($cacheFiles !== false && count($cacheFiles) > 0){
            $cacheFile = $cacheFiles[0];
        }

        if(!empty($cacheFile)){
            $cacheParts = explode(".", $cacheFile);
            if(count($cacheParts) >= 3){
                $lifetime = $cacheParts[count($cacheParts) - 2];
                if(is_numeric($lifetime)){
                    if($lifetime === "0"){
                        return json_decode(IoHelper::getFileContent($cacheFile), true);
                    }
                    $fileExpiredAfter = filemtime($cacheFile) + (int) $lifetime;
                    if(time() <= $fileExpiredAfter){
                        return json_decode(IoHelper::getFileContent($cacheFile), true);
                    }
                    IoHelper::deleteFile($cacheFile);
                } else if($lifetime === self::NO_LIFETIME_FILE_STRING){
                    return json_decode(IoHelper::getFileContent($cacheFile), true);
                }
            }
        }
        return null;
    }

    public function delete(): bool
    {
        return IoHelper::deleteFiles(glob($this->cachePath . $this->getToken() . "*.json"));
    }

    protected function setTokenArray(array &$tokenArray)
    {
        $this->tokenArray = &$tokenArray;
    }

    protected static function convertClosuresToString($data)
    {
        if (!is_array($data)) {
            if ($data instanceof \Closure) {
                return self::getClosureAsString($data);
            }
            return $data;
        }

        foreach ($data as $key => $token){
            if (is_array($token)) {
                $data[$key] = self::convertClosuresToString($token);
            } else if ($token instanceof \Closure) {
                $data[$key] = self::getClosureAsString($token);
            }
        }

        return $data;
    }

    protected static function getClosureAsString(Closure $closure)
    {
        try {
            $reflectionFunction = new ReflectionFunction($closure); // get reflection object
        } catch (Exception $exception) {
            return false;
        }

        $filePath = $reflectionFunction->getFileName();  // absolute path of php file containing function
        $startLine = $reflectionFunction->getStartLine(); // start line of function
        $endLine = $reflectionFunction->getEndLine(); // end line of function
        $lineSeparator = PHP_EOL; // line separator "\n"

        $staticVariables = $reflectionFunction->getStaticVariables();
        $staticVariables = var_export($staticVariables, true);

        if ($filePath === false || $startLine === false || $endLine === false) {
            return false;
        }

        $startEndDifference = $endLine - $startLine;

        $startLine--; // -1 to use it with the array representation of the file

        if ($startLine < 0 || $startEndDifference < 0) {
            return false;
        }

        // get content of file containing function
        $fp = fopen($filePath, 'rb');
        $fileContent = "";
        if (flock($fp, LOCK_SH)) {
            $fileContent = @stream_get_contents($fp);
        }
        flock($fp, LOCK_UN);
        fclose($fp);

        if (empty($fileContent)) {
            return false;
        }

        // separate the file into an array containing every line as one element
        $fileContentArray = explode($lineSeparator, $fileContent);

        if (count($fileContentArray) < $endLine) {
            return false;
        }

        // return the part of the file containing the function as a string.
        $functionString = implode("", array_slice($fileContentArray, $startLine, $startEndDifference + 1));

        $functionString .= "|staticScopeVariables:".$staticVariables;

        return $functionString;
    }

    protected function setCacheDir(string $cacheDir)
    {
        IoHelper::normalizeDirectory($cacheDir);

        $this->cacheDir = $cacheDir;

        return $this;
    }

    protected function getCacheDir(): string
    {
        return (!empty($this->cacheDir)) ? $this->cacheDir : self::DEFAULT_CACHE_DIR;
    }
}