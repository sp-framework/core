<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags\Buttons;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class DropdownSplitButtons extends AdminLTETags
{
    protected $view;

    protected $tag;

    protected $links;

    protected $params;

    protected $buttonParams = [];

    protected $content;

    public function __construct($view, $tag, $links, $params, $buttonParams)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

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

            // var_dump($this->params);
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
            if (isset($this->params['dropdownButtonIconPosition']) && $this->params['dropdownButtonIconPosition'] === 'after') {
                $this->buttonParams['dropdownButtonIcon'] =
                    '<i class="fas fa-fw fa-' . $this->params['dropdownButtonIcon'] . '"></i>';
                $this->buttonParams['dropdownButtonIconPosition'] = 'after';
            } else {
                $this->buttonParams['dropdownButtonIcon'] =
                    '<i class="fas fa-fw fa-' . $this->params['dropdownButtonIcon'] . '"></i>';
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
            isset($this->params['splitMainButtonTitle']) ?
            $this->params['splitMainButtonTitle'] :
            '';

        $this->buttonParams['splitMainButtonAdditionalClass'] =
            isset($this->params['splitMainButtonAdditionalClass']) ?
            $this->params['splitMainButtonAdditionalClass'] :
            '';

        $this->buttonParams['splitMainButtonType'] =
            isset($this->params['splitMainButtonType']) ?
            'btn-' . $this->params['splitMainButtonType'] :
            'btn-primary';

        $this->buttonParams['splitMainButtonHidden'] =
            isset($this->params['splitMainButtonHidden']) ?
            'hidden' :
            '';

        $this->buttonParams['splitMainButtonDisabled'] =
            isset($this->params['splitMainButtonDisabled']) ?
            'disabled' :
            '';

        if (isset($this->params['splitMainButtonIcon']) && isset($this->params['splitMainButtonTitle'])) {
            if (isset($this->params['splitMainButtonIconPosition']) && $this->params['splitMainButtonIconPosition'] === 'after') {
                $this->buttonParams['splitMainButtonIcon'] =
                    '<i class="fas fa-fw fa-' . $this->params['splitMainButtonIcon'] . '"></i>';
                $this->buttonParams['splitMainButtonIconPosition'] = 'after';
            } else {
                $this->buttonParams['splitMainButtonIcon'] =
                    '<i class="fas fa-fw fa-' . $this->params['splitMainButtonIcon'] . '"></i>';
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
            '<div class="btn-group ' . $this->buttonParams['dropdownDirection'] . '">';

        $this->content .=
            '<button class="btn ' .
                $this->buttonParams['buttonSize'] . ' ' .
                $this->buttonParams['buttonFlat'] . ' ' .
                $this->buttonParams['buttonPosition'] . ' ';

        if (!$this->buttonParams['dropdownSplitButtonsSplit']) {
            $this->content .=
                $this->buttonParams['dropdownButtonType'] . ' ' .
                $this->buttonParams['dropdownButtonAdditionalClass'] . ' ' .
                'dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ' .
                'data-toggle="tooltip" data-html="true" data-placement="' .
                $this->buttonParams['dropdownButtonTooltipPosition']. '" title="' .
                $this->buttonParams['dropdownButtonTooltipTitle'] . '" ' .
                'id="' . $this->buttonParams['dropdownButtonId'] . '" ' .
                $this->buttonParams['dropdownButtonDisabled'] . ' ' .
                $this->buttonParams['dropdownButtonHidden'] . ' >' .
                $this->buttonParams['dropdownButtonTitle'] .
                '</button>';
        // var_dump($this->content);
        } else {
            $this->content .=
                $this->buttonParams['splitMainButtonType'] . ' ' .
                $this->buttonParams['splitMainButtonAdditionalClass'] . ' ' .
                '" type="button" aria-haspopup="true" aria-expanded="false" ' .
                'data-toggle="tooltip" data-html="true" data-placement="' .
                $this->buttonParams['splitMainButtonTooltipPosition']. '" title="' .
                $this->buttonParams['splitMainButtonTooltipTitle'] . '" ' .
                'id="' . $this->buttonParams['splitMainButtonId'] . '" ' .
                $this->buttonParams['splitMainButtonDisabled'] . ' ' .
                $this->buttonParams['splitMainButtonHidden'] . ' >' .
                $this->buttonParams['splitMainButtonTitle'] .
                '</button>
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

    // <div class="btn-group {{hasSplitDropdownDirection}} {{hasSplitDropdownPosition}}">
    //     {% if splitMainButtonUrl %}
    //         <a href="{{splitMainButtonUrl}}" id="{{hasMainSplitButtonId}}" class="text-white btn btn-{{splitMainButtonType|default('primary')}} btn-{{hasSplitButtonSize}} {{splitMainButtonAdditionalClass}}" {{hasSplitMainButtonDisabled}} {{hasSplitMainButtonHidden}} role="button" {{hasButtonHidden}}>
    //             {{hasSplitMainButtonIcon|raw}} {{splitButtonIdMissing|upper}}{{hasSplitMainButtonTitle|upper}}
    //         </a>
    //     {% else %}
    //         <button type="button" id="{{hasMainSplitButtonId}}" class="text-white btn btn-{{splitMainButtonType|default('primary')}} btn-{{hasSplitButtonSize}} {{splitMainButtonAdditionalClass}}" {{hasSplitMainButtonDisabled}} {{hasSplitMainButtonHidden}} {{hasButtonHidden}}>
    //             {{hasSplitMainButtonIcon|raw}} {{splitButtonIdMissing|upper}}{{hasSplitMainButtonTitle|upper}}
    //         </button>
    //     {% endif %}
    //     <button type="button" id="{{hasMainSplitButtonId}}-split-dropdown" class="btn btn-{{splitDropdownButtonType|default('default')}} btn-{{hasSplitButtonSize}} dropdown-toggle {{hasSplitDropdownButtonHover}} dropdown-icon {{splitDropdownButtonAdditionalClass}}" data-toggle="dropdown" {{hasSplitDropdownButtonDisabled}} {{hasSplitDropdownButtonHidden}}>
    //         <div class="dropdown-menu {{hasSplitDropdownAlign}}" role="menu">
    //         {% for index, links in splitDropdownButtonListLinks %}
    //             {% if links.icon %}
    //                 {% set hasLinkIcon = '<i class="fas fa-fw fa-' ~ links.icon ~ ' mr-1"></i>' %}
    //             {% else %}
    //                 {% set hasLinkIcon = '' %}
    //             {% endif %}
    //             {% if componentId and sectionId %}
    //                 {% set hasLinkId = componentId ~ '-' ~ sectionId ~ '-' ~ index %}
    //             {% elseif sectionId %}
    //                 {% set hasLinkId = sectionId ~ '-' ~ index %}
    //             {% else %}
    //                 {% set hasLinkId = index %}
    //             {% endif %}
    //             {% if index == 'divider' %}
    //                 <div class="dropdown-divider"></div>
    //             {% else %}
    //                 <a id="{{hasLinkId}}" class="dropdown-item {{links.additionalClass}}" href="{{links.url|default('#')}}">{{hasLinkIcon|raw}} {{links.title}}</a>
    //             {% endif %}
    //         {% endfor %}
    //         </div>
    //     </button>
    // </div>