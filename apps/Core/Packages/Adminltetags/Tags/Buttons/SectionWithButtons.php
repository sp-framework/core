<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Buttons;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class SectionWithButtons
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

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {
        $this->content .= '<div class="row">';

        if (isset($this->params['sectionSecondaryButtons']) && is_array($this->params['sectionSecondaryButtons']) && count($this->params['sectionSecondaryButtons']) > 0) {
            $this->content .= '<div class="col">';

            $this->content .= $this->adminLTETags->useTag('buttons', $this->params['sectionSecondaryButtons']);

            $this->content .= '</div>';
        }

        if (isset($this->params['sectionButtons']) && is_array($this->params['sectionButtons'])) {
            $this->content .= '<div class="col">';

            $this->generateFormButtonsContent();

            $this->content .= '</div>';
        }
    }

    protected function generateFormButtonsContent()
    {
        if (isset($this->params['sectionButtons']['cancelActionUrl']) ||
            isset($this->params['sectionButtons']['closeActionUrl'])
        ) {
            if (isset($this->params['sectionButtons']['cancelActionUrl'])) {
                $this->buttonParams['cancelActionUrl'] =
                    $this->links->url($this->params['sectionButtons']['cancelActionUrl']);
            }

            if (isset($this->params['sectionButtons']['closeActionUrl'])) {
                $this->buttonParams['closeActionUrl'] =
                    $this->links->url($this->params['sectionButtons']['closeActionUrl']);
            }
        } else {
            throw new \Exception('cancelActionUrl/closeActionUrl missing');
        }

        $this->content .=
            '<div id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-action-buttons">';

        $buttonsArr = [];

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
        if (count($buttonsArr) === 1 && isset($buttonsArr['cancelForm'])) {
            $buttonsArr['cancelForm']['hidden'] = false;
        }

        $this->content .= $this->adminLTETags->useTag('buttons',
            [
                'componentId'            => $this->params['componentId'],
                'sectionId'              => $this->params['sectionId'],
                'buttonLabel'            => false,
                'buttonType'             => 'button',
                'buttons'                => $buttonsArr
            ]
        );

        $this->content .= '</div>';
    }
}