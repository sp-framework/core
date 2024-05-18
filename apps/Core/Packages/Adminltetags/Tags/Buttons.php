<?php

namespace Apps\Core\Packages\Adminltetags\Tags;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Buttons extends Adminltetags
{
    protected $params;

    protected $content = '';

    protected $buttonParams = [];

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->buttonParams =
            isset($this->params['buttonParams']) ?
            $this->params['buttonParams'] :
            [];

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

        if (isset($this->params['buttonLabel']) && $this->params['buttonLabel'] !== false) {
            $this->content .=
                '<label style="display:block;">' . strtoupper($this->params['buttonLabel']) . '</label>';
        }

        try {
            $button = 'Apps\\Core\\Packages\\Adminltetags\\Tags\\Buttons\\' . ucfirst($this->params['buttonType']);

            $this->content .= (new $button($this->view, $this->tag, $this->links, $this->escaper, $this->params, $this->buttonParams))->getContent();

        } catch (\Exception $e) {
            throw $e;
        }

    }
}