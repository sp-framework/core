<?php

namespace Applications\Admin\Packages\AdminLTETags\Content\Listing;

use Applications\Admin\Packages\AdminLTETags;
use Applications\Admin\Packages\AdminLTETags\Content\Listing\Table\DynamicTable;
use Applications\Admin\Packages\AdminLTETags\Content\Listing\Table\StaticTable;

class Table extends AdminLTETags
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
        if (isset($this->params['dtPostUrl']) || isset($this->params['dtRows'])) {
            $this->content .=
                (new DynamicTable($this->view, $this->tag, $this->links, $this->escaper, $this->params))->getContent();
        } else {
            $this->content .=
                (new StaticTable($this->view, $this->tag, $this->links, $this->escaper, $this->params))->getContent();
        }
    }
}