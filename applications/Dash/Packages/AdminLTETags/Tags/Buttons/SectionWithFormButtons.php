<?php

namespace Applications\Dash\Packages\AdminLTETags\Tags\Buttons;

use Applications\Dash\Packages\AdminLTETags\AdminLTETags;

class SectionWithFormButtons
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

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {
        if (isset($this->params['formButtons']['addActionUrl']) ||
            isset($this->params['formButtons']['updateActionUrl']) ||
            isset($this->params['formButtons']['closeActionUrl'])
        ) {
            if (isset($this->params['formButtons']['addActionUrl'])) {
                $this->buttonParams['addActionUrl'] =
                    $this->links->url($this->params['formButtons']['addActionUrl']);

                if (isset($this->params['formButtons']['addSuccessRedirectUrl'])) {
                    $this->buttonParams['addSuccessRedirectUrl'] =
                        $this->links->url($this->params['formButtons']['addSuccessRedirectUrl']);
                } else {
                    throw new \Exception('addSuccessRedirectUrl missing');
                }
            }

            if (isset($this->params['formButtons']['updateActionUrl'])) {

                $this->buttonParams['updateActionUrl'] =
                    $this->links->url($this->params['formButtons']['updateActionUrl']);

                if (isset($this->params['formButtons']['updateSuccessRedirectUrl'])) {
                    $this->buttonParams['updateSuccessRedirectUrl'] =
                        $this->links->url($this->params['formButtons']['updateSuccessRedirectUrl']);
                } else {
                    throw new \Exception('updateSuccessRedirectUrl missing');
                }
            }

            if (isset($this->params['formButtons']['cancelActionUrl']) ||
                isset($this->params['formButtons']['closeActionUrl'])
            ) {
                if (isset($this->params['formButtons']['cancelActionUrl'])) {
                    $this->buttonParams['cancelActionUrl'] =
                        $this->links->url($this->params['formButtons']['cancelActionUrl']);
                }

                if (isset($this->params['formButtons']['closeActionUrl'])) {
                    $this->buttonParams['closeActionUrl'] =
                        $this->links->url($this->params['formButtons']['closeActionUrl']);
                }
            } else {
                throw new \Exception('cancelActionUrl/closeActionUrl missing');
            }
        } else {
            throw new \Exception('addActionUrl/updateActionUrl/(cancelActionUrl/closeActionUrl) missing');
        }

        $this->buttonParams['actionTarget'] =
            isset($this->params['formButtons']['actionTarget']) ?
            $this->params['formButtons']['actionTarget'] :
            'mainContent';

        $this->buttonParams['updateButtonId'] =
            isset($this->params['formButtons']['updateButtonId']) ?
            $this->params['formButtons']['updateButtonId'] :
            '';

        $this->content .=
            '<div id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-action-buttons">';

        $buttonsArr = [];

        if (isset($this->buttonParams['addActionUrl'])) {
            $buttonsArr =
                array_merge(
                    $buttonsArr,
                    [
                        'addData' =>
                        [
                            'title'                 => 'Add',
                            'position'              => 'right',
                            'icon'                  => 'cog fa-spin',
                            'iconHidden'            => true,
                            'size'                  => 'sm',
                            'hidden'                => true,
                            'buttonAdditionalClass' => 'mr-1 ml-1',
                            'action'                => 'post',
                            'actionTarget'          => $this->buttonParams['actionTarget'],
                            'actionUrl'             => $this->buttonParams['addActionUrl'],
                            'successRedirectUrl'    => $this->buttonParams['addSuccessRedirectUrl'],
                        ]
                    ]
                );
        }

        if (isset($this->buttonParams['updateActionUrl'])) {
            $buttonsArr =
                array_merge(
                    $buttonsArr,
                    [
                        'updateData' =>
                        [
                            'title'                 => 'Update',
                            'position'              => 'right',
                            'icon'                  => 'cog fa-spin',
                            'iconHidden'            => true,
                            'size'                  => 'sm',
                            'hidden'                => true,
                            'buttonAdditionalClass' => 'mr-1 ml-1',
                            'action'                => 'post',
                            'actionTarget'          => $this->buttonParams['actionTarget'],
                            'actionUrl'             => $this->buttonParams['updateActionUrl'],
                            'successRedirectUrl'    => $this->buttonParams['updateSuccessRedirectUrl'],
                        ]
                    ]
                );
        }

        if (isset($this->buttonParams['cancelActionUrl'])) {
            $buttonsArr =
                array_merge(
                    $buttonsArr,
                    [
                        'cancelForm' =>
                        [
                            'title'                 => 'Cancel',
                            'position'              => 'right',
                            'type'                  => 'secondary',
                            'size'                  => 'sm',
                            'hidden'                => true,
                            'buttonAdditionalClass' => 'mr-1 ml-1',
                            'actionUrl'             => $this->buttonParams['cancelActionUrl']
                        ]
                    ]

                );
        }

        if (isset($this->buttonParams['closeActionUrl'])) {
            $buttonsArr =
                array_merge(
                    $buttonsArr,
                    [
                        'closeForm' =>
                        [
                            'title'                 => 'Close',
                            'position'              => 'right',
                            'type'                  => 'secondary',
                            'size'                  => 'sm',
                            'hidden'                => true,
                            'buttonAdditionalClass' => 'mr-1 ml-1',
                            'actionUrl'             => $this->buttonParams['closeActionUrl']
                        ]
                    ]
                );
        }

        if (count($buttonsArr) === 1 && isset($buttonsArr['closeForm'])) {
            $buttonsArr['closeForm']['hidden'] = false;
        }

        $this->content .= $this->adminLTETags->useTag('buttons',
            [
                'componentId'            => $this->params['componentId'],
                'sectionId'              => $this->params['sectionId'],
                'buttonLabel'            => false,
                'buttonType'             => 'button',
                'updateButtonId'         => $this->buttonParams['updateButtonId'],
                'buttons'                => $buttonsArr
            ]
        );

        $this->content .= '</div>';
    }
}