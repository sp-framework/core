<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class Modal extends AdminLTETags
{
    protected $params;

    protected $content = '';

    protected $buttonParams = [];

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        //
    }
}