<?php

namespace Applications\Admin\Packages\AdminLTETags;

use Applications\Admin\Packages\AdminLTETags;

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