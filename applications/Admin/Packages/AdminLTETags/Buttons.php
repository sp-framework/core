<?php

namespace Applications\Admin\Packages\AdminLTETags;

use Applications\Admin\Packages\AdminLTETags;

class Buttons extends AdminLTETags
{
    protected $params;

    protected $content = '';

    protected $fieldParams = [];

    public function getContent($params)
    {
        $this->params = $params;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        $this->content = 'buttons';
    }
}