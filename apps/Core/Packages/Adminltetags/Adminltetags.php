<?php

namespace Apps\Core\Packages\Adminltetags;

use System\Base\BasePackage;

class Adminltetags extends BasePackage
{
    public function useTag(string $tagName, array $params)
    {
        $explodedTagName = explode('/', $tagName);

        if (count($explodedTagName) === 1) {
            $tag = 'Apps\\Core\\Packages\\Adminltetags\\Tags\\' . ucfirst($explodedTagName[0]);
        } else {
            $tag = 'Apps\\Core\\Packages\\Adminltetags\\Tags';

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