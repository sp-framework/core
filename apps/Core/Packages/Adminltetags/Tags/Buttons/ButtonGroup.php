<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Buttons;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class ButtonGroup
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

        // groupButtonType : horizontal, vertical, radio
        $this->buttonParams['groupButtonType'] = 'btn-group';
        if (isset($this->params['groupButtonType'])) {
            if ($this->params['groupButtonType'] === 'vertical') {
                $this->buttonParams['groupButtonType'] = 'btn-group-vertical';
            }
        }

        $this->buttonParams['groupButtonSize'] =
            isset($this->params['groupButtonSize']) ?
            $this->params['groupButtonSize'] :
            'md';

        $this->buttonParams['groupButtonFlat'] =
            isset($this->params['groupButtonFlat']) && $this->params['groupButtonFlat'] === true ?
            'btn-flat' :
            '';

        $this->buttonParams['groupButtonBlock'] =
            isset($this->params['groupButtonBlock']) && $this->params['groupButtonBlock'] === true ?
            'btn-block' :
            '';

        $this->buttonParams['groupButtonPosition'] =
            isset($this->params['groupButtonPosition']) ?
            'float-' . $this->params['groupButtonPosition'] :
            '';
    }

    protected function buildButton()
    {
        if ($this->params['groupButtonType'] === 'radio') {
            $this->buttonParams['groupButtonType'] = 'btn-group btn-group-toggle';

            $this->content .= '<div class="'. $this->buttonParams['groupButtonType'] . ' ' . $this->buttonParams['groupButtonFlat'] . ' ' . $this->buttonParams['groupButtonBlock'] . ' btn-group-' . $this->buttonParams['groupButtonSize'] . ' ' . $this->buttonParams['groupButtonPosition'] . '" data-toggle="buttons">';
            foreach ($this->params['buttons'] as $buttonKey => $button) {

                $this->params['groupRadioButtonType'] = 'primary';
                if (isset($button['type'])) {
                    $this->params['groupRadioButtonType'] = $button['type'];
                }
                $this->buttonParams['groupRadioButtonType'] = 'bg-' . $this->params['groupRadioButtonType'];

                if (isset($this->params['groupRadioButtonStyle'])) {
                    if ($this->params['groupRadioButtonStyle'] === 'outline') {
                        $this->buttonParams['groupRadioButtonType'] = 'btn-outline-' . $this->params['groupRadioButtonType'];
                    } else if ($this->params['groupRadioButtonStyle'] === 'gradient') {
                        $this->buttonParams['groupRadioButtonType'] = 'bg-gradient-' . $this->params['groupRadioButtonType'];
                    }
                }

                if (array_key_exists('title', $button) && $button['title'] !== false) {
                    $hasButtonTitle = $button['title'];

                    if ($button['title'] === false) {
                        $hasButtonTitle = '';
                    }
                } else {
                    $hasButtonTitle = 'missing_button_title';
                }

                if (isset($button['icon'])) {
                    if (isset($button['title'])) {
                        if (isset($button['iconPosition']) && $button['iconPosition'] === 'after') {
                            $hasButtonIcon = '<i class="fas fa-fw fa-' . $button['icon'] . ' ml-1"></i>';
                        } else {
                            $hasButtonIcon = '<i class="fas fa-fw fa-' . $button['icon'] . ' mr-1"></i>';
                        }
                    } else {
                        $hasButtonIcon = '<i class="fas fa-fw fa-' . $button['icon'] . '"></i>';
                    }
                } else {
                    $hasButtonIcon = '';
                }

                if (isset($this->params['groupRadioButtonChecked'])) {
                    if ($this->params['groupRadioButtonChecked'] === $button['dataValue']) {
                        $hasButtonChecked = 'checked';
                        $hasButtonCheckedClasses = 'active focus';
                        $hasButtonCheckedBgClass = 'bg-' . $button['groupRadioButtonType'];
                    } else {
                        $hasButtonChecked = '';
                        $hasButtonCheckedClasses = '';
                        $hasButtonCheckedBgClass = '';
                    }
                } else {
                    if (isset($button['checked']) && $button['checked'] === true) {
                        $hasButtonChecked = 'checked';
                        $hasButtonCheckedClasses = 'active focus';
                        $hasButtonCheckedBgClass = 'bg-' . $button['groupRadioButtonType'];
                    } else {
                        $hasButtonChecked = '';
                        $hasButtonCheckedClasses = '';
                        $hasButtonCheckedBgClass = '';
                    }
                }

                if (isset($buton['disabled']) && $buton['disabled'] === true) {
                    $hasButtonDisabled = 'disabled';
                    $hasButtonCursor = 'style=cursor:default;';
                } else {
                    $hasButtonDisabled = '';
                    $hasButtonCursor = 'style=cursor:pointer;';
                }
                if (isset($this->params['componentId']) && isset($this->params['sectionId'])) {
                    $hasButtonId = $this->params['componentId'] . '-' . $this->params['sectionId'] . '-' . $buttonKey;
                } else {
                    $hasButtonId = $buttonKey;
                }

                $hasButtonAdditionalClass = '';
                if (isset($button['buttonAdditionalClass'])) {
                    $hasButtonAdditionalClass = $button['buttonAdditionalClass'];
                }

                $this->content .=
                    '<label class="btn ' . $this->buttonParams['groupButtonFlat'] . ' ' . $this->buttonParams['groupRadioButtonType'] . ' ' . $hasButtonCheckedClasses . ' ' . $hasButtonDisabled . ' ' . $hasButtonAdditionalClass . ' '  . $hasButtonCheckedBgClass . '" ' . $hasButtonCursor . '>
                        <input type="radio" name="options" id="' . $hasButtonId . '" autocomplete="off" data-value="' . $button['value'] . '" ' . $hasButtonChecked . '>';

                        if (isset($button['iconPosition'])) {
                            if ($button['iconPosition'] === 'after') {
                                $this->content .= $hasButtonTitle . $hasButtonIcon;
                            } else {
                                $this->content .= $hasButtonIcon . $hasButtonTitle;
                            }
                        } else {
                            $this->content .= $hasButtonIcon . $hasButtonTitle;
                        }
                $this->content .= '</label>';
            }
        } else {
            $this->content .= '<div class="'. $this->buttonParams['groupButtonType'] . ' btn-group-' . $this->buttonParams['groupButtonSize'] . ' ' . $this->buttonParams['groupButtonPosition'] . '">';

            foreach ($this->params['buttons'] as $buttonKey => $button) {
                if (isset($button['dropdowns'])) {
                    if (isset($button['dropdownHover']) && $button['dropdownHover'] === true) {
                        $button['dropdownHover'] = 'dropdown-hover';
                    } else {
                        $button['dropdownHover'] = '';
                    }

                    $button['dropdownDirection'] =
                        isset($button['dropdownDirection']) ?
                        $button['dropdownDirection'] :
                        '';//either dropup or ''

                    $button['dropdownButtonType'] =
                        isset($button['dropdownButtonType']) ?
                        $button['dropdownButtonType'] :
                        'primary';

                    $this->content .= '<div class="btn-group">';
                    $this->content .= '<button class="btn btn-' . $button['dropdownButtonType'] . ' dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>';

                    $params = $this->params;
                    $params['buttonType'] = 'Dropdown';
                    $params['dropdowns'] = $button['dropdowns'];

                    $this->content .= $this->adminLTETags->useTag('buttons', $params);

                    $this->content .= '</div>';
                } else {
                    if (isset($button['title'])) {
                        if ($button['title'] === false) {
                            $this->buttonParams['title'] = '';
                        } else {
                            $this->buttonParams['title'] = $button['title'];
                        }
                    } else {
                        $this->buttonParams['title'] = 'Missing Button Title';
                    }

                    if (isset($button['icon']) && isset($button['title'])) {
                        if (isset($button['iconHidden']) && $button['iconHidden'] === true) {
                            $iconHidden = 'hidden';
                        } else {
                            $iconHidden = '';
                        }
                        if (isset($button['iconPosition']) && $button['iconPosition'] === 'after') {
                            $this->buttonParams['icon'] =
                                '<i class="fas fa-fw fa-' . $button['icon'] . '" ' . $iconHidden . '></i>';
                            $this->buttonParams['iconPosition'] = 'after';
                        } else {
                            $this->buttonParams['icon'] =
                                '<i class="fas fa-fw fa-' . $button['icon'] . '" ' . $iconHidden . '></i>';
                            $this->buttonParams['iconPosition'] = '';
                        }
                    } else {
                        $this->buttonParams['icon'] = '';
                        $this->buttonParams['iconPosition'] = '';
                    }

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

                    if ($this->buttonParams['url'] !== '') {
                        $this->content .=
                            '<a href="' . $this->buttonParams['url'] . '" ';
                    } else {
                        $this->content .=
                            '<button ';
                    }

                    $this->buttonParams['type'] =
                        isset($button['type']) ?
                        'btn-' . $button['type'] :
                        'btn-primary';

                    $this->content .=
                        'class="btn btn-' .
                            $this->buttonParams['groupButtonSize'] . ' ' .
                            $this->buttonParams['groupButtonFlat'] . ' ' .
                            $this->buttonParams['type'] . ' ' .
                            $this->buttonParams['additionalClass'] . ' ' .
                        '" id="' . $this->buttonParams['id'] . '" ' .
                        'data-toggle="tooltip" data-html="true" data-placement="' .
                            $this->buttonParams['tooltipPosition']. '" title="' .
                            $this->buttonParams['tooltipTitle'] . '" ' .
                        $this->buttonParams['disabled'] . ' ' .
                        $this->buttonParams['hidden'] . ' ' .
                        ' role="button">';

                        if ($this->buttonParams['icon'] !== '') {
                            if ($this->buttonParams['iconPosition'] === 'after') {
                                $this->content .=
                                    strtoupper($this->buttonParams['title']) . ' ' . $this->buttonParams['icon'];
                            } else {
                                $this->content .=
                                    $this->buttonParams['icon'] . ' ' . strtoupper($this->buttonParams['title']);
                            }
                        } else {
                            $this->content .=
                                strtoupper($this->buttonParams['title']);
                        }

                    if ($this->buttonParams['url'] !== '') {
                        $this->content .=
                            '</a>';
                    } else {
                        $this->content .=
                            '</button>';
                    }
                }
            }
        }

        $this->content .= '</div>';
    }
// Example:
//{{adminltetags.useTag('buttons',
//     [
//         'componentId'                       : 'core',
//         'sectionId'                         : 'main',
//         'buttonType'                        : 'ButtonGroup',
//         'groupButtonType'                   : 'horizontal',
//         'groupButtonSize'                   : 'btn-xs',
//         'groupButtonFlat'                   : false,
//         'groupButtonPosition'               : 'left',
//         'buttons'                           :
//             {
//                 'apps-dropdowns'                         :
//                 {
//                     'dropdowns' :
//                         {
//                             'apptypes' : {
//                                 'title'                   : 'App Type',
//                                 'type'                    : 'primary',
//                                 'additionalClass'         : 'contentAjaxLink',
//                                 'url'                     : ''
//                             },
//                         }
//                 },
//                 'add-widgets' : {
//                     'title'                   : 'Add Widgets',
//                     'size'                    : 'xs',
//                     'type'                    : 'primary',
//                     'position'                : 'right',
//                     'icon'                    : 'plus'
//                 },
//                 'save-widgets' : {
//                     'title'                   : 'Save',
//                     'size'                    : 'xs',
//                     'type'                    : 'primary',
//                     'position'                : 'right',
//                     'icon'                    : 'save'
//                 },
//                 'divider-dropdowns'                           :
//                 {
//                     'dropdowns' :
//                         {
//                             'subviews' : {
//                                 'title'                   : 'Sub View',
//                                 'type'                    : 'primary',
//                                 'additionalClass'         : 'contentAjaxLink',
//                                 'url'                     : ''
//                             },
//                             'divider' : 'divider',
//                             'bundles' : {
//                                 'title'                   : 'Modules Bundle',
//                                 'type'                    : 'primary',
//                                 'additionalClass'         : 'contentAjaxLink',
//                                 'url'                     : ''
//                             }
//                         }
//                 }
//             }
//     ]
// )}}
// radio
// {{adminltetags.useTag('buttons',
//     [
//         'componentId'                       : 'core',
//         'sectionId'                         : 'main',
//         'buttonType'                        : 'ButtonGroup',
//         'groupButtonType'                   : 'radio',
//         'groupButtonSize'                   : 'sm',
//         'groupButtonFlat'                   : false,
//         'groupButtonPosition'               : 'left',
//         'groupRadioButtonStyle'             : 'outline',
//         'buttons'                           :
//             {
//                 'add-widgets' : {
//                     'title'                   : 'Add Widgets',
//                     'icon'                    : 'plus',
//                     'type'                    : 'success',
//                     'value'                   : 'add'
//                 },
//                 'edit-widgets' : {
//                     'title'                   : 'Edit Widgets',
//                     'icon'                    : 'edit',
//                     'type'                    : 'danger',
//                     'value'                   : 'edit',
//                     'disabled'                : true
//                 },
//                 'save-widgets' : {
//                     'title'                   : 'Save',
//                     'type'                    : 'info',
//                     'value'                   : 'save',
//                     'icon'                    : 'save'
//                 }
//             }
//     ]
// )}}
}