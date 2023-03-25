<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Buttons;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;

class Button
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $adminLTETags;

    protected $params;

    protected $content;

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
            throw new \Exception('Error: buttons (array) missing');
        }

        // if (!$this->params['buttonsNoMargin']) {
        //     $this->buttonParams['margin'] =
        //         count($buttons) > 1 ? 'mr-1' : '';
        // } else {
        //     $this->buttonParams['margin'] = '';
        // }

        foreach ($buttons as $buttonKey => $button) {

            if (isset($button['title'])) {
                if ($button['title'] === false) {
                    $this->buttonParams['title'] = '';
                } else {
                    $this->buttonParams['title'] = $button['title'];
                }
            } else {
                $this->buttonParams['title'] = 'Missing Button Title';
            }

            $this->buttonParams['content'] =
                isset($button['content']) ?
                $button['content'] :
                '';

            $this->buttonParams['position'] =
                isset($button['position']) ?
                'float-' . $button['position'] :
                '';

            if (isset($button['noMargin']) && $button['noMargin'] === true) {
                $this->buttonParams['margin'] = '';
            } else {
                if ($this->buttonParams['position'] === 'float-right') {
                    $this->buttonParams['margin'] = 'ml-1';
                } else {
                    $this->buttonParams['margin'] = 'mr-1';
                }
            }

            $this->buttonParams['size'] =
                isset($button['size']) ?
                'btn-' . $button['size'] :
                'btn-sm';

            $this->buttonParams['block'] =
                isset($button['block']) && $button['block'] === true ?
                'btn-block' :
                '';

            $this->buttonParams['flat'] =
                isset($button['flat']) && $button['flat'] === true ?
                'btn-flat' :
                '';

            $this->buttonParams['type'] =
                isset($button['type']) ?
                'btn-' . $button['type'] :
                'btn-primary';

            if (isset($button['style'])) {
                if ($button['style'] === 'outline') {
                    $this->buttonParams['type'] = 'btn-outline-' .
                        isset($button['type']) ?
                            $button['type'] : 'primary';
                } else if ($button['style'] === 'gradient') {
                    $this->buttonParams['type'] = 'bg-gradient-' .
                        isset($button['type']) ?
                            $button['type'] : 'primary';
                }
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

            if (isset($button['modalButton'])) {
                $this->buttonParams['modalButton'] = 'data-toggle=modal data-target=#' . $button['modalId'] . '-modal';
                $this->buttonParams['id'] = $button['id'];
            } else {
                $this->buttonParams['modalButton'] = '';
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

            $this->buttonParams['addActionUrl'] = '';
            $this->buttonParams['addSuccessRedirectUrl'] = '';
            $this->buttonParams['updateActionUrl'] = '';
            $this->buttonParams['updateSuccessRedirectUrl'] = '';
            $this->buttonParams['cancelActionUrl'] = '';
            $this->buttonParams['cancelSuccessRedirectUrl'] = '';
            $this->buttonParams['closeActionUrl'] = '';
            $this->buttonParams['closeSuccessRedirectUrl'] = '';
            $this->buttonParams['hasAjax'] = '';

            if ($buttonKey === 'addData') {
                // if (isset($button['actionUrl'])) {
                    $this->buttonParams['addActionUrl'] = 'actionurl="' . $button['actionUrl'] . '"';
                //     $this->buttonParams['addSuccessRedirectUrl'] = '';
                //     $this->buttonParams['hasAjax'] = '';
                // } else if (isset($button['successRedirectUrl'])) {
                //     $this->buttonParams['addActionUrl'] = '';
                    $this->buttonParams['addSuccessRedirectUrl'] = 'href="' . $button['successRedirectUrl'] . '"';
                    $this->buttonParams['hasAjax'] = 'addData';
                // }
            } else if ($buttonKey === 'updateData') {
                // if (isset($button['actionUrl']) && isset($this->params['updateButtonId'])) {
                    $this->buttonParams['updateActionUrl'] = 'actionurl="' . $button['actionUrl'] . '"';
                //     $this->buttonParams['updateSuccessRedirectUrl'] = '';
                //     $this->buttonParams['hasAjax'] = '';
                // } else if (isset($button['successRedirectUrl'])) {
                //     $this->buttonParams['updateActionUrl'] = '';
                    $this->buttonParams['updateSuccessRedirectUrl'] = 'href="' . $button['successRedirectUrl'] . '"';
                    $this->buttonParams['hasAjax'] = 'updateData';
                // }
            } else if ($buttonKey === 'cancelForm' && isset($button['actionUrl'])) {
                $this->buttonParams['cancelActionUrl'] = 'href="' . $button['actionUrl'] . '"';
                $this->buttonParams['hasAjax'] = 'cancelForm contentAjaxLink';
            } else if ($buttonKey === 'closeForm' && isset($button['actionUrl'])) {
                $this->buttonParams['closeActionUrl'] = 'href="' . $button['actionUrl'] . '"';
                $this->buttonParams['hasAjax'] = 'closeForm contentAjaxLink';
            }

            $this->buttonParams['actionTarget'] =
                isset($button['actionTarget']) ?
                'data-actiontarget="' . $button['actionTarget'] . '"' :
                '';

            $this->buttonParams['successNotify'] =
                isset($button['successNotify']) && $button['successNotify'] == true ?
                'data-successnotify="true"' :
                '';

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

            $this->buildButton();
        }
    }

    protected function buildButton()
    {
        if ($this->buttonParams['url'] !== '') {
            $this->content .=
                '<a href="' . $this->buttonParams['url'] . '" ';
        } else {
            $this->content .=
                '<button ';
        }

        $this->content .=
            'class="btn ' .
                $this->buttonParams['size'] . ' ' .
                $this->buttonParams['flat'] . ' ' .
                $this->buttonParams['block'] . ' ' .
                $this->buttonParams['type'] . ' ' .
                $this->buttonParams['margin'] . ' ' .
                $this->buttonParams['position'] . ' ' .
                $this->buttonParams['hasAjax'] . ' ' .
                $this->buttonParams['additionalClass'] . ' ' .
            '" ' .
                $this->buttonParams['addActionUrl'] . ' ' .
                $this->buttonParams['updateActionUrl'] . ' ' .
                $this->buttonParams['cancelActionUrl'] . ' ' .
                $this->buttonParams['closeActionUrl'] . ' ' .
                $this->buttonParams['addSuccessRedirectUrl'] . ' ' .
                $this->buttonParams['updateSuccessRedirectUrl'] . ' ' .
                $this->buttonParams['actionTarget'] . ' ' .
                $this->buttonParams['successNotify'] . ' ' .
            ' id="' . $this->buttonParams['id'] . '" ' .
            'data-toggle="tooltip" data-html="true" data-placement="' .
                $this->buttonParams['tooltipPosition']. '" title="' .
                $this->buttonParams['tooltipTitle'] . '" ' .
            $this->buttonParams['disabled'] . ' ' .
            $this->buttonParams['hidden'] . ' ' .
            $this->buttonParams['modalButton'] .
            ' role="button">';

            // var_dump($this->content);
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

            $this->content .=
                $this->buttonParams['content'];

        if ($this->buttonParams['url'] !== '') {
            $this->content .=
                '</a>';
        } else {
            $this->content .=
                '</button>';
        }
    }
}