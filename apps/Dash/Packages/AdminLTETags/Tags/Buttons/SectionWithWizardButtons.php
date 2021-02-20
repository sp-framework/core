<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Buttons;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;

class SectionWithWizardButtons
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
        if (isset($this->buttonParams['wizardCanCancel']) && $this->buttonParams['wizardCanCancel'] === true) {
            if (isset($this->buttonParams['wizardCancelUrl'])) {
                $this->buttonParams['wizardCancelUrl'] =
                    $this->links->url($this->buttonParams['wizardCancelUrl']);
            } else {
                throw new \Exception('wizardCancelUrl missing');
            }
        }

        if (!isset($this->buttonParams['wizardDoneUrl'])) {
            throw new \Exception('wizardDoneUrl missing');
        }

        $this->content .=
            '<div id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-wizard-buttons">';

        $this->content .= $this->adminLTETags->useTag('buttons',
            [
                'componentId'            => $this->params['componentId'],
                'sectionId'              => $this->params['sectionId'],
                'buttonLabel'            => false,
                'buttonType'             => 'button',
                'buttons'                =>
                    [
                        'previous'    => [
                            'title'                 => 'Previous',
                            'position'              => 'left',
                            'icon'                  => 'angle-left',
                            'size'                  => 'xs',
                            'hidden'                => true
                        ],
                        'cancel' => [
                            'title'                 => 'Cancel Wizard',
                            'type'                  => 'danger',
                            'position'              => 'center',
                            'icon'                  => 'times',
                            'size'                  => 'xs',
                            'hidden'                => true,
                            'url'                   => $this->buttonParams['wizardCancelUrl'],
                            'buttonAdditionalClass' => 'contentAjaxLink'
                        ],
                        'next' => [
                            'title'                 => 'Next',
                            'position'              => 'right',
                            'icon'                  => 'angle-right',
                            'size'                  => 'xs',
                            'iconPosition'          => 'after',
                            'hidden'                => true
                        ],
                        'submit' => [
                            'title'                 => 'Submit',
                            'position'              => 'right',
                            'size'                  => 'xs',
                            'iconPosition'          => 'after',
                            'hidden'                => true
                        ],
                        'done' => [
                            'title'                 => 'Done',
                            'position'              => 'right',
                            'size'                  => 'xs',
                            'iconPosition'          => 'after',
                            'hidden'                => true,
                            'url'                   => $this->links->url($this->buttonParams['wizardDoneUrl']),
                            'buttonAdditionalClass' => 'contentAjaxLink'
                        ]
                    ]
            ]
        );

        $this->content .= '</div>';

    }
}