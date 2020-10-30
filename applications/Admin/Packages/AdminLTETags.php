<?php

namespace Applications\Admin\Packages;

class AdminLTETags
{
    protected $container;

    protected $view;

    protected $tag;

    protected $links;

    protected $compiler;

    public function __construct($view, $tag, $links)
    {
        // $this->container = $container;

        // $this->view = $this->container->getShared('view');
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        // if ($this->view->getRegisteredEngines()['.html'] === 'volt') {
        //     $this->compiler = $this->container->getShared('volt')->getCompiler();
        // }
    }

    public function useTag(string $tagName, array $params)
    {
        $explodedTagName = explode('/', $tagName);

        if (count($explodedTagName) === 1) {
            $tag = 'Applications\\Admin\\Packages\\AdminLTETags\\' . ucfirst($explodedTagName[0]);
        } else {
            $tag = 'Applications\\Admin\\Packages\\AdminLTETags';

            foreach ($explodedTagName as $name) {
                $tag .= '\\' . ucfirst($name);
            }
        }

        try {
            return (new $tag($this->view, $this->tag, $this->links))->getContent($params);
        } catch (\Error $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // protected function getPartial($file, $params)
    // {
    //     if ($this->compiler) {
    //         $compiled = $this->compiler->compile($this->view->getViewsDir() . $file . '.html');
    //         var_dump($this->compiler->getCompiledTemplatePath());
    //         var_dump($this->view->partial('-var-www-html-sp-applications-admin-views-default-html-users-users', ['params' => $params]));
    //     }
    // }
}