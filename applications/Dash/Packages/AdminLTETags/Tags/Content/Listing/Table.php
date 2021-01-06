<?php

namespace Applications\Dash\Packages\AdminLTETags\Tags\Content\Listing;

use Applications\Dash\Packages\AdminLTETags\AdminLTETags;
use Applications\Dash\Packages\AdminLTETags\Tags\Content\Listing\Table\DynamicTable;
use Applications\Dash\Packages\AdminLTETags\Tags\Content\Listing\Table\StaticTable;

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