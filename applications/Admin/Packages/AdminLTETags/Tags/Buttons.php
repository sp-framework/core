<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class Buttons extends AdminLTETags
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
        // buttonType - as per buttonType, code is generated
        if (!isset($this->params['buttonType'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">Error: buttonType missing</span>';
            return;
        }

        $this->buildButtonParamsArr();

        try {
            $button = 'Applications\\Admin\\Packages\\AdminLTETags\\Tags\\Buttons\\' . ucfirst($this->params['buttonType']);

            $this->content .= (new $button($this->view, $this->tag, $this->links, $this->params, $this->buttonParams))->getContent();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    protected function buildButtonParamsArr()
    {
        if (isset($this->params['buttonLabel'])) {
            if ($this->params['buttonLabel'] === false) {
                $this->content .=
                    '<label style="display:block; margin-bottom:29px;"></label>';
            } else {
                $this->content .=
                    '<label style="display:block;">' . strtoupper($this->params['buttonLabel']) . '</label>';
            }
        }
    }
}