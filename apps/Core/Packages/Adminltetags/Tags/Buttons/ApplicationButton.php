<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Buttons;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class ApplicationButton
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $adminLTETags;

    protected $content;

    protected $params;

    protected $buttonParams = [];

    public function __construct($view, $tag, $links, $escaper, $params, $buttonParams)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->adminLTETags = new Adminltetags();

        $this->params = $params;

        $this->buttonParams = $buttonParams;

        $this->buildButtonParamsArr();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildButtonParamsArr()
    {
        if (isset($this->params['buttons'])) {
            $buttons = $this->params['buttons'];
        } else {
            $this->content .= 'Error: buttons (array) missing';

            return;
        }

        if (!isset($this->params['buttonId'])) {
            $this->content .= 'Error: buttonId missing';

            return;
        }

        foreach ($this->params['buttons'] as $buttonKey => $button) {
            if (isset($button['title'])) {
                if ($button['title'] === false) {
                    $this->buttonParams['title'] = '';
                } else {
                    $this->buttonParams['title'] = '<span class="text-xs">' . $button['title'] . '</span>';
                }
            } else {
                $this->buttonParams['title'] = 'Missing Button Title';
            }

            $this->buttonParams['position'] =
                isset($button['position']) ?
                'float-' . $button['position'] :
                '';

            $this->buttonParams['flat'] =
                isset($button['flat']) && $button['flat'] === true ?
                'btn-flat' :
                '';

            $this->buttonParams['type'] =
                isset($button['type']) ?
                'bg-' . $button['type'] :
                'bg-primary';

            if (isset($button['buttonId'])) {
                $this->buttonParams['id'] = $button['buttonId'];
            } else if (isset($this->params['componentId']) && isset($this->params['sectionId'])) {
                $this->buttonParams['id'] =
                    $this->params['componentId'] . '-' . $this->params['sectionId'] . '-' . $buttonKey;
            } else {
                $this->buttonParams['id'] = $buttonKey;
            }

            $this->buttonParams['url'] =
                isset($button['url']) ?
                $button['url'] :
                '';

            $this->buttonParams['hidden'] =
                isset($button['hidden']) && $button['hidden'] === true ?
                'hidden' :
                '';

            if ($this->buttonParams['url'] === '') {
                $this->buttonParams['disabled'] =
                    isset($button['disabled']) && $button['disabled'] === true ?
                    'disabled' :
                    '';
            } else {
                $this->buttonParams['disabled'] = '';
                if (isset($button['disabled']) && $button['disabled'] === true) {
                    if (isset($button['buttonAdditionalClass'])) {
                        $button['buttonAdditionalClass'] = $button['buttonAdditionalClass'] . ' disabled';
                    } else {
                        $button['buttonAdditionalClass'] = 'disabled';
                    }
                }
            }

            if (isset($button['icon'])) {
                if (isset($button['iconHidden']) && $button['iconHidden'] === true) {
                    $iconHidden = 'hidden';
                } else {
                    $iconHidden = '';
                }
                $this->buttonParams['icon'] = '<i class="fas fa-' . $button['icon'] . '" ' . $iconHidden . '></i>';
            } else {
                $this->buttonParams['icon'] = '';
            }

            $this->buttonParams['additionalClass'] =
                isset($button['buttonAdditionalClass']) ?
                $button['buttonAdditionalClass'] :
                '';

            $this->buttonParams['tooltipPosition'] =
                isset($button['tooltipPosition']) ?
                $button['tooltipPosition'] :
                'auto';

            $this->buttonParams['tooltipTitle'] =
                isset($button['tooltipTitle']) ?
                $button['tooltipTitle'] :
                '';

            $this->buttonParams['badge'] = '';
            if (isset($button['badge'])) {
                $this->buttonParams['badge'] = '<span class="badge bg-' . $button['badge'] . '">';
                if (isset($button['badgeContent'])) {
                    $this->buttonParams['badge'] .= $button['badgeContent'];
                }
                $this->buttonParams['badge'] .= '</span>';
            }

            $this->buildButton();
        }
    }

    protected function buildButton()
    {
        $this->content .=
        '<a href="' . $this->buttonParams['url'] . '" class="btn btn-app ' .
                $this->buttonParams['flat'] . ' ' .
                $this->buttonParams['type'] . ' ' .
                $this->buttonParams['position'] . ' ' .
                $this->buttonParams['additionalClass'] . ' ' .
            '" id="' . $this->buttonParams['id'] . '" data-toggle="tooltip" data-html="true" data-placement="' .
                $this->buttonParams['tooltipPosition']. '" title="' .
                $this->buttonParams['tooltipTitle'] . '" ' .
            $this->buttonParams['disabled'] . ' ' .
            $this->buttonParams['hidden'] . '>' .
            $this->buttonParams['badge'] .
            $this->buttonParams['icon'] .
            $this->buttonParams['title'] .
        '</a>';
    }
}