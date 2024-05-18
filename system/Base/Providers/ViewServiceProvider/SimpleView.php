<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Mvc\View\Simple as PhalconView;
use Phalcon\Mvc\View\Engine\Php as PhpTemplateService;

class SimpleView
{
    protected $views;

    public function __construct($views)
    {
        $this->views = $views;
    }

    public function init()
    {
        $phalconView = new PhalconView();

        $phalconView->setViewsDir($this->views->getPhalconViewPath());

        $phalconView->registerEngines(
            [
                '.html'     => 'volt',
                '.phtml'    => PhpTemplateService::class
            ]
        );

        return $phalconView;
    }
}