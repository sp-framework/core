<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags\Buttons;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class DatatableButtons extends AdminLTETags
{
    protected $view;

    protected $tag;

    protected $links;

    protected $params;

    protected $buttonParams = [];

    public function __construct($view, $tag, $links, $params, $buttonParams)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->params = $params;

        $this->buttonParams = $buttonParams;
    }

    public function getContent()
    {
        return
            $this->useTag('buttons',
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