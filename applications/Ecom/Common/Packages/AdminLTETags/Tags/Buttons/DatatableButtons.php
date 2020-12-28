<?php

namespace Applications\Ecom\Common\Packages\AdminLTETags\Tags\Buttons;

use Applications\Ecom\Common\Packages\AdminLTETags\AdminLTETags;

class DatatableButtons
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
    }

    public function getContent()
    {
        return
            $this->adminLTETags->useTag('buttons',
                [
                    'componentId'            => $this->params['componentId'],
                    'sectionId'              => $this->params['sectionId'],
                    'buttonLabel'            => false,
                    'buttonType'             => 'button',
                    'buttons'                =>
                    [
                        'update-button' =>
                        [
                            'title'         => 'Update',
                            'position'      => 'right',
                            'icon'          => 'plus',
                            'size'          => 'xs',
                            'hidden'        => true
                        ],
                        'cancel-button' =>
                        [
                            'title'         => 'Cancel',
                            'type'          => 'default',
                            'position'      => 'right',
                            'icon'          => 'times',
                            'size'          => 'xs',
                            'hidden'        => true
                        ],
                        'assign-button' =>
                        [
                            'title'         => 'Assign',
                            'position'      => 'right',
                            'icon'          => 'plus',
                            'size'          => 'xs',
                            'hidden'        => false,
                        ]
                    ]
                ]
            );
    }
}