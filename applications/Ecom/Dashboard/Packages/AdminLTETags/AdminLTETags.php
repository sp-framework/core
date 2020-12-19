<?php

namespace Applications\Ecom\Dashboard\Packages\AdminLTETags;

use System\Base\BasePackage;

class AdminLTETags extends BasePackage
{
    // protected $view;

    // protected $tag;

    // protected $links;

    // protected $escaper;

    // public function __construct($view, $tag, $links, $escaper)
    // {
    //     $this->view = $view;

    //     $this->tag = $tag;

    //     $this->links = $links;

    //     $this->escaper = $escaper;
    // }

    public function useTag(string $tagName, array $params)
    {
        $explodedTagName = explode('/', $tagName);

        if (count($explodedTagName) === 1) {
            $tag = 'Applications\\Ecom\\Dashboard\\Packages\\AdminLTETags\\Tags\\' . ucfirst($explodedTagName[0]);
        } else {
            $tag = 'Applications\\Ecom\\Dashboard\\Packages\\AdminLTETags\\Tags';

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