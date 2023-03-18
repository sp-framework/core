<?php

namespace Apps\Dash\Packages\AdminLTETags;

use System\Base\BasePackage;

class AdminLTETags extends BasePackage
{
    public function useTag(string $tagName, array $params)
    {
        $explodedTagName = explode('/', $tagName);

        if (count($explodedTagName) === 1) {
            $tag = 'Apps\\Dash\\Packages\\AdminLTETags\\Tags\\' . ucfirst($explodedTagName[0]);
        } else {
            $tag = 'Apps\\Dash\\Packages\\AdminLTETags\\Tags';

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

    public function get(array $data = [], bool $resetCache = false)
    {
        return;
    }

    public function add(array $data)
    {
        return;
    }

    public function update(array $data)
    {
        return;
    }

    public function remove(array $data)
    {
        return;
    }
}