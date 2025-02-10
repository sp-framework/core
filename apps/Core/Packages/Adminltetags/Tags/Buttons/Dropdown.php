<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Buttons;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Dropdown
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

        $this->buildDropdownParamsArr();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildDropdownParamsArr()
    {
        if (!isset($this->params['dropdowns'])) {
            $this->content .= 'Error: dropdowns (array) missing';

            return;
        }

        $this->buttonParams['dropdownAlign'] =
            isset($this->params['dropdownAlign']) && $this->params['dropdownAlign'] === 'right' ?
            'dropdown-menu-right' :
            '';

        $this->buildDropdown();
    }

    protected function buildDropdown()
    {
        $this->content .=
            '<div class="dropdown-menu ' . $this->buttonParams['dropdownAlign'] . ' ">';
            foreach ($this->params['dropdowns'] as $index => $links) {
                if ($index === 'divider') {
                    $this->content .= '<div class="dropdown-divider"></div>';
                } else {
                    if (isset($links['icon']) && isset($links['title'])) {
                        if (isset($links['iconPosition']) && $links['iconPosition'] === 'after') {
                            $icon['linkIcon'] =
                                '<i class="fas fa-fw fa-' . $links['icon'] . '"></i>';
                            $icon['linkIconPosition'] = 'after';
                        } else {
                            $icon['linkIcon'] =
                                '<i class="fas fa-fw fa-' . $links['icon'] . '"></i>';
                            $icon['linkIconPosition'] = '';
                        }
                    } else {
                        $icon['linkIcon'] = '';
                        $icon['linkIconPosition'] = '';
                    }

                    $linkId = $this->params['componentId'] . '-' . $this->params['sectionId'] . '-' . $index;
                    $linkDisabled = isset($links['disabled']) && $links['disabled'] === true ? 'disabled' : '';
                    $linkAdditionalClass = isset($links['additionalClass']) ? $links['additionalClass'] : '';
                    $linkUrl = isset($links['url']) ? $links['url'] : '#';
                    $link = isset($links['title']) ? $links['title'] : 'missing_button_title';

                    $this->content .=
                        '<a id="' . $linkId .'" class="dropdown-item ' . $linkDisabled . ' ' . $linkAdditionalClass . ' " href="'. $linkUrl . '">';

                    if ($icon['linkIcon'] !== '') {
                        if ($icon['linkIconPosition'] === 'after') {
                            $this->content .=
                                strtoupper($link) . ' ' . $icon['linkIcon'];
                        } else {
                            $this->content .=
                                $icon['linkIcon'] . ' ' . strtoupper($link);
                        }
                    } else {
                        $this->content .=
                            strtoupper($link);
                    }

                    $this->content .= '</a>';
                }
            }
        $this->content .= '</div>';
    }
}