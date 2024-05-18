<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Helper\Arr;

class VoltTools
{
    protected $container;

    protected $volt;

    protected $view;

    protected $viewPath;

    protected $filesCount = null;

    protected $files = [];

    protected $whitelist = ['.', '..', '.gitignore'];

    public function __construct($volt, $view)
    {
        $this->volt = $volt;

        $this->view = $view;

        $this->viewPath  = $this->view->getViewsDir();
    }

    public function reCompile()
    {
        $iterator =
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->viewPath),
                RecursiveIteratorIterator::CHILD_FIRST
            );

        foreach ($iterator as $path) {
            if ($path->isDir() !== true &&
                in_array($path->getFilename(), $this->whitelist) !== true
            ) {
                $this->files[] = $path->getPathname();
            }
        }

        $this->filesCount = count($files);

        foreach ($files as $file) {
            $this->volt->getCompiler()->compile($file);
        }

        return true;
    }

    public function getCompiledFilesCount()
    {
        if ($this->filesCount) {
            return $this->filesCount;
        }

        return 'Run reCompile() first';
    }

    public function getCompiledFiles()
    {
        if (count($this->files) > 0) {
            return $this->files;
        }

        return 'Run reCompile() first';
    }

    public function getCompiler()
    {
        return $this->compiler;
    }
}