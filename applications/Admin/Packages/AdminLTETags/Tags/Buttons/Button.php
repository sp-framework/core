<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags\Buttons;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class Button extends AdminLTETags
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

            $this->buttonParams['hidden'] =
                isset($button['hidden']) && $button['hidden'] === true ?
                'hidden' :
                '';

            $this->buttonParams['disabled'] =
                isset($button['disabled']) && $button['disabled'] === true ?
                'disabled' :
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
                if (isset($button['position']) && $button['position'] === 'after') {
                    $this->buttonParams['icon'] =
                        '<i class="fas fa-fw fa-' . $button['icon'] . ' ml-1"></i>';
                    $this->buttonParams['iconPosition'] = 'after';
                } else {
                    $this->buttonParams['icon'] =
                        '<i class="fas fa-fw fa-' . $button['icon'] . ' mr-1"></i>';
                    $this->buttonParams['iconPosition'] = '';
                }
            } else {
                $this->buttonParams['icon'] = '';
                $this->buttonParams['iconPosition'] = '';
            }

            if (isset($this->params['componentId']) && isset($this->params['sectionId'])) {
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

            $this->buttonParams['createActionUrl'] = '';
            $this->buttonParams['createSuccessRedirectUrl'] = '';
            $this->buttonParams['updateActionUrl'] = '';
            $this->buttonParams['updateSuccessRedirectUrl'] = '';
            $this->buttonParams['hasAjax'] = '';

            if ($buttonKey === 'create') {
                if (isset($button['actionUrl'])) {
                    $this->buttonParams['createActionUrl'] = 'actionurl="' . $button['actionUrl'] . '"';
                    $this->buttonParams['createSuccessRedirectUrl'] = '';
                    $this->buttonParams['hasAjax'] = '';
                } else if (isset($button['successRedirectUrl'])) {
                    $this->buttonParams['createActionUrl'] = '';
                    $this->buttonParams['createSuccessRedirectUrl'] = 'href="' . $button['successRedirectUrl'] . '"';
                    $this->buttonParams['hasAjax'] = 'methodPost';
                }
            }

            if ($buttonKey === 'edit') {
                if (isset($button['actionUrl'])) {
                    $this->buttonParams['updateActionUrl'] = 'actionurl="' . $button['actionUrl'] . '"';

                    if (isset($this->params['updateButtonId'])) {
                        $this->buttonParams['updateActionUrl'] =
                            $this->buttonParams['updateActionUrl'] . '&id="' . $this->params['updateButtonId'] . '"';
                    }
                    $this->buttonParams['updateSuccessRedirectUrl'] = '';
                    $this->buttonParams['hasAjax'] = '';
                } else if (isset($button['successRedirectUrl'])) {
                    $this->buttonParams['updateActionUrl'] = '';
                    $this->buttonParams['updateSuccessRedirectUrl'] = 'href="' . $button['successRedirectUrl'] . '"';
                    $this->buttonParams['hasAjax'] = 'methodPost';
                }
            }

            if ($buttonKey === 'cancel' && isset($button['actionUrl'])) {
                $this->buttonParams['cancelActionUrl'] = 'href="' . $button['actionUrl'] . '"';
                $this->buttonParams['hasAjax'] = 'contentAjaxLink';
            } else {
                $this->buttonParams['cancelActionUrl'] = '';
                $this->buttonParams['hasAjax'] = '';
            }

            if ($buttonKey === 'close' && isset($button['actionUrl'])) {
                $this->buttonParams['closeActionUrl'] = 'href="' . $button['actionUrl'] . '"';
                $this->buttonParams['hasAjax'] = 'contentAjaxLink';
            } else {
                $this->buttonParams['closeActionUrl'] = '';
                $this->buttonParams['hasAjax'] = '';
            }

            $this->buttonParams['actionTarget'] =
                isset($button['actionTarget']) ?
                'data-actiontarget="' . $button['actionTarget'] . '"' :
                '';

            if (isset($button['successNotifyMessage']) && $button['successNotifyMessage'] !== '') {
                $this->buttonParams['successNotificationTitle'] =
                    'data-notificationtitle="' . $button['title'] . '"';
                $this->buttonParams['successNotificationMessage'] =
                    'data-notificationmessage="' . $button['successNotifyMessage'] . '"';
            } else {
                $this->buttonParams['successNotificationTitle'] = '';
                $this->buttonParams['successNotificationMessage'] = '';
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
                $this->buttonParams['createActionUrl'] . ' ' .
                $this->buttonParams['updateActionUrl'] . ' ' .
                $this->buttonParams['cancelActionUrl'] . ' ' .
                $this->buttonParams['closeActionUrl'] . ' ' .
                $this->buttonParams['createSuccessRedirectUrl'] . ' ' .
                $this->buttonParams['updateSuccessRedirectUrl'] . ' ' .
                $this->buttonParams['actionTarget'] . ' ' .
                $this->buttonParams['successNotificationTitle'] . ' ' .
                $this->buttonParams['successNotificationMessage'] .
            '" ' .
            'id="' . $this->buttonParams['id'] . '" ' .
            'data-toggle="tooltip" data-html="true" data-placement="' .
                $this->buttonParams['tooltipPosition']. '" title="' .
                $this->buttonParams['tooltipTitle'] . '" ' .
            $this->buttonParams['disabled'] . ' ' .
            $this->buttonParams['hidden'] . ' ' .
            $this->buttonParams['modalButton'] .
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