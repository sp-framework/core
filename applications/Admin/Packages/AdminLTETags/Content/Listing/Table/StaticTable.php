<?php

namespace Applications\Admin\Packages\AdminLTETags\Content\Listing\Table;

class StaticTable
{
    protected $view;

    protected $tag;

    protected $links;

    protected $params;

    protected $content;

    public function __construct($view, $tag, $links, $params)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->params = $params;

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {
    }
}