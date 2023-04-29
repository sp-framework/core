<?php

namespace Apps\Core\Packages\AdminLTETags\Tags\Buttons;

use Apps\Core\Packages\AdminLTETags\AdminLTETags;

class DropdownSplitButtons
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

        $this->adminLTETags = new AdminLTETags();

        $this->params = $params;

        $this->buttonParams = $buttonParams;

        $this->buildButtonParamsArr();

        $this->buildButton();
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

        $this->buttonParams['dropdownSplitButtonsSplit'] =
            isset($this->params['dropdownSplitButtonsSplit']) && $this->params['dropdownSplitButtonsSplit'] === true ?: false;

        //Whole Button
        $this->buttonParams['buttonSize'] =
            isset($this->params['buttonSize']) ?
            'btn-' . $this->params['buttonSize'] :
            'btn-sm';

        $this->buttonParams['buttonFlat'] =
            isset($this->params['buttonFlat']) && $this->params['buttonFlat'] === true ?
            'btn-flat' :
            '';

        $this->buttonParams['buttonPosition'] =
            isset($this->params['buttonPosition']) ?
            'float-' . $this->params['buttonPosition'] :
            '';

        //Whole Dropdown
        $this->buttonParams['dropdownHover'] =
            isset($this->params['dropdownHover']) ?
            'dropdown-hover' :
            '';

        $this->buttonParams['dropdownDirection'] =
            isset($this->params['dropdownDirection']) ?
            'dropup' :
            '';

        $this->buttonParams['dropdownAlign'] =
            isset($this->params['dropdownAlign']) && $this->params['dropdownAlign'] === 'right' ?
            'dropdown-menu-right' :
            '';

        //Dropdown Button (No Split Button)
        if (!$this->buttonParams['dropdownSplitButtonsSplit']) {
            $this->buildButtonParamsArrNoSplit();
        } else {
            $this->buildButtonParamsArrSplit();
        }
    }

    protected function buildButtonParamsArrNoSplit()
    {
        if (isset($this->params['dropdownButtonId'])) {
            $this->buttonParams['dropdownButtonId'] =
                $this->params['componentId'] . '-' . $this->params['sectionId'] . '-' . $this->params['dropdownButtonId'];
        } else {
            throw new \Exception('Error: dropdownButtonId missing');
        }

        $this->buttonParams['dropdownButtonTitle'] =
            isset($this->params['dropdownButtonTitle']) ?
            $this->params['dropdownButtonTitle'] :
            'missing_dropdownButtonTitle';

        $this->buttonParams['dropdownButtonAdditionalClass'] =
            isset($this->params['dropdownButtonAdditionalClass']) ?
            $this->params['dropdownButtonAdditionalClass'] : '';

        $this->buttonParams['dropdownButtonType'] =
            isset($this->params['dropdownButtonType']) ?
            'btn-' . $this->params['dropdownButtonType'] :
            'btn-primary';

        $this->buttonParams['dropdownButtonHidden'] =
            isset($this->params['dropdownButtonHidden']) ?
            'hidden' :
            '';

        $this->buttonParams['dropdownButtonDisabled'] =
            isset($this->params['dropdownButtonDisabled']) ?
            'disabled' :
            '';

        if (isset($this->params['dropdownButtonIcon']) && isset($this->params['dropdownButtonTitle'])) {
            if (isset($this->params['dropdownButtonIconHidden']) && $this->params['dropdownButtonIconHidden'] === true) {
                $dropdownButtonIconHidden = 'hidden';
            } else {
                $dropdownButtonIconHidden = '';
            }
            if (isset($this->params['dropdownButtonIconPosition']) && $this->params['dropdownButtonIconPosition'] === 'after') {
                $this->buttonParams['dropdownButtonIcon'] =
                    '<i class="fas fa-fw fa-' . $this->params['dropdownButtonIcon'] . '" ' . $dropdownButtonIconHidden . '></i>';
                $this->buttonParams['dropdownButtonIconPosition'] = 'after';
            } else {
                $this->buttonParams['dropdownButtonIcon'] =
                    '<i class="fas fa-fw fa-' . $this->params['dropdownButtonIcon'] . '" ' . $dropdownButtonIconHidden . '></i>';
                $this->buttonParams['dropdownButtonIconPosition'] = '';
            }
        } else {
            $this->buttonParams['dropdownButtonIcon'] = '';
            $this->buttonParams['dropdownButtonIconPosition'] = '';
        }

        $this->buttonParams['dropdownButtonTooltipPosition'] =
            isset($button['dropdownButtonTooltipPosition']) ?
            $button['dropdownButtonTooltipPosition'] :
            'auto';

        $this->buttonParams['dropdownButtonTooltipTitle'] =
            isset($button['dropdownButtonTooltipTitle']) ?
            $button['dropdownButtonTooltipTitle'] :
            '';
    }

    protected function buildButtonParamsArrSplit()
    {
        //Main Button (Split Button)
        if (isset($this->params['splitMainButtonId'])) {
            $this->buttonParams['splitMainButtonId'] =
                $this->params['componentId'] . '-' . $this->params['sectionId'] . '-' . $this->params['splitMainButtonId'];
        } else {
            throw new \Exception('Error: splitMainButtonId missing');
        }

        $this->buttonParams['splitMainButtonTitle'] =
            isset($this->params['splitMainButtonTitle']) && $this->params['splitMainButtonTitle'] !== false ?
            $this->params['splitMainButtonTitle'] :
            'missing_split_main_button_title';


        $this->buttonParams['splitMainButtonUrl'] =
            isset($this->params['splitMainButtonUrl']) ?
            $this->params['splitMainButtonUrl'] :
            '#';

        $this->buttonParams['splitMainButtonAdditionalClass'] =
            isset($this->params['splitMainButtonAdditionalClass']) ?
            $this->params['splitMainButtonAdditionalClass'] :
            '';

        $this->buttonParams['splitMainButtonType'] =
            isset($this->params['splitMainButtonType']) ?
            'btn-' . $this->params['splitMainButtonType'] :
            'btn-primary';

        $this->buttonParams['splitMainButtonHidden'] =
            isset($this->params['splitMainButtonHidden']) && $this->params['splitMainButtonHidden'] === true ?
            'hidden' :
            '';

        $this->buttonParams['splitMainButtonDisabled'] =
            isset($this->params['splitMainButtonDisabled']) && $this->params['splitMainButtonDisabled'] === true?
            'disabled' :
            '';

        if (isset($this->params['splitMainButtonIcon']) && isset($this->params['splitMainButtonTitle'])) {
            if (isset($this->params['splitMainButtonIconHidden']) && $this->params['splitMainButtonIconHidden'] === true) {
                $splitMainButtonIconHidden = 'hidden';
            } else {
                $splitMainButtonIconHidden = '';
            }
            if (isset($this->params['splitMainButtonIconPosition']) && $this->params['splitMainButtonIconPosition'] === 'after') {
                $this->buttonParams['splitMainButtonIcon'] =
                    '<i class="fas fa-fw fa-' . $this->params['splitMainButtonIcon'] . '" ' . $splitMainButtonIconHidden . '></i>';
                $this->buttonParams['splitMainButtonIconPosition'] = 'after';
            } else {
                $this->buttonParams['splitMainButtonIcon'] =
                    '<i class="fas fa-fw fa-' . $this->params['splitMainButtonIcon'] . '" ' . $splitMainButtonIconHidden . '></i>';
                $this->buttonParams['splitMainButtonIconPosition'] = '';
            }
        } else {
            $this->buttonParams['splitMainButtonIcon'] = '';
            $this->buttonParams['splitMainButtonIconPosition'] = '';
        }

        $this->buttonParams['splitMainButtonTooltipPosition'] =
            isset($button['splitMainButtonTooltipPosition']) ?
            $button['splitMainButtonTooltipPosition'] :
            'auto';

        $this->buttonParams['splitMainButtonTooltipTitle'] =
            isset($button['splitMainButtonTooltipTitle']) ?
            $button['splitMainButtonTooltipTitle'] :
            '';

        //Dropdown Button (Split secondary button)
        $this->buttonParams['splitDropdownButtonType'] =
            isset($this->params['splitDropdownButtonType']) ?
            'btn-' . $this->params['splitDropdownButtonType'] :
            'btn-default';

        $this->buttonParams['splitDropdownButtonHidden'] =
            isset($this->params['splitDropdownButtonHidden']) ?
            'hidden' :
            '';

        $this->buttonParams['splitDropdownButtonDisabled'] =
            isset($this->params['splitDropdownButtonDisabled']) ?
            'disabled' :
            '';
    }

    protected function buildButton()
    {
        $this->content .=
            '<div class="btn-group ' . $this->buttonParams['dropdownDirection'] . ' ' . $this->buttonParams['buttonPosition'] . '">';

        if (!$this->buttonParams['dropdownSplitButtonsSplit']) {
            $this->content .=
                '<button class="btn ' .
                    $this->buttonParams['buttonSize'] . ' ' .
                    $this->buttonParams['buttonFlat'] . ' ' .
                    $this->buttonParams['dropdownButtonType'] . ' ' .
                    $this->buttonParams['dropdownButtonAdditionalClass'] . ' ' .
                    'dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ' .
                    'data-toggle="tooltip" data-html="true" data-placement="' .
                    $this->buttonParams['dropdownButtonTooltipPosition']. '" title="' .
                    $this->buttonParams['dropdownButtonTooltipTitle'] . '" ' .
                    'id="' . $this->buttonParams['dropdownButtonId'] . '" ' .
                    $this->buttonParams['dropdownButtonDisabled'] . ' ' .
                    $this->buttonParams['dropdownButtonHidden'] . ' >';

                    if ($this->buttonParams['dropdownButtonIcon'] !== '') {
                        if ($this->buttonParams['dropdownButtonIconPosition'] === 'after') {
                            $this->content .=
                                strtoupper($this->buttonParams['dropdownButtonTitle']) . ' ' . $this->buttonParams['dropdownButtonIcon'];
                        } else {
                            $this->content .=
                                $this->buttonParams['dropdownButtonIcon'] . ' ' . strtoupper($this->buttonParams['dropdownButtonTitle']);
                        }
                    } else {
                        $this->content .=
                            strtoupper($this->buttonParams['dropdownButtonTitle']);
                    }

            $this->content .=
                '</button>';
        } else {
            $this->content .=
                '<a class="btn ' .
                    $this->buttonParams['buttonSize'] . ' ' .
                    $this->buttonParams['buttonFlat'] . ' ' .
                    $this->buttonParams['splitMainButtonType'] . ' ' .
                    $this->buttonParams['splitMainButtonAdditionalClass'] . ' ' .
                    '" type="button" aria-haspopup="true" aria-expanded="false" ' .
                    'data-toggle="tooltip" data-html="true" data-placement="' .
                    $this->buttonParams['splitMainButtonTooltipPosition']. '" title="' .
                    $this->buttonParams['splitMainButtonTooltipTitle'] . '" ' .
                    'id="' . $this->buttonParams['splitMainButtonId'] . '" ' .
                    $this->buttonParams['splitMainButtonDisabled'] . ' ' .
                    $this->buttonParams['splitMainButtonHidden'] . ' href="' . $this->buttonParams['splitMainButtonUrl'] .'">';
                    if ($this->buttonParams['splitMainButtonIcon'] !== '') {
                        if ($this->buttonParams['splitMainButtonIconPosition'] === 'after') {
                            $this->content .=
                                strtoupper($this->buttonParams['splitMainButtonTitle']) . ' ' . $this->buttonParams['splitMainButtonIcon'];
                        } else {
                            $this->content .=
                                $this->buttonParams['splitMainButtonIcon'] . ' ' . strtoupper($this->buttonParams['splitMainButtonTitle']);
                        }
                    } else {
                        $this->content .=
                            strtoupper($this->buttonParams['splitMainButtonTitle']);
                    }

            $this->content .=
                '</a>
                <button class="btn ' .
                $this->buttonParams['buttonSize'] . ' ' .
                $this->buttonParams['buttonFlat'] . ' ' .
                $this->buttonParams['splitDropdownButtonType'] . ' ' .
                'dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ' .
                $this->buttonParams['splitDropdownButtonHidden'] . ' ' .
                '><span class="sr-only">Toggle Dropdown</span></button>';
        }

        $this->content .=
            '<div class="dropdown-menu ' . $this->buttonParams['dropdownAlign'] . ' ">';
            foreach ($this->params['buttons'] as $index => $links) {
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
        $this->content .= '</div></div>';
    }
}