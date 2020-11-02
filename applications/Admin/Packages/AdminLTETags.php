<?php

namespace Applications\Admin\Packages;

class AdminLTETags
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    public function __construct($view, $tag, $links, $escaper)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;
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
            return (new $tag($this->view, $this->tag, $this->links, $this->escaper))->getContent($params);
        } catch (\Error $e) {
            throw $e;
        }
    }
}