<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Fields;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;

class Colorpicker
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $params;

    protected $fieldParams;

    protected $content;

    protected $adminLTETags;

    public function __construct($view, $tag, $links, $escaper, $params, $fieldParams)
    {
        $this->adminLTETags = new AdminLTETags();

        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->params = $params;

        $this->fieldParams = $fieldParams;

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {
        $this->fieldParams['fieldColorpickerSize'] =
            isset($this->params['fieldColorpickerSize']) ?
            $this->params['fieldColorpickerSize'] :
            'sm';

        $this->fieldParams['fieldSize'] =
            isset($this->params['fieldSize']) ?
            'form-control-' . $this->params['fieldSize'] :
            'form-control-sm';

        if (isset($this->params['fieldColorpickerPreAddonColorSquare']) ||
            isset($this->params['fieldColorpickerPostAddonColorSquare'])
        ) {
            $this->content .=
                '<div ' . $this->fieldParams['fieldId'] . '-colorpicker" class="input-group input-group-' . $this->fieldParams['fieldColorpickerSize'] . '">';

            if (isset($this->params['fieldColorpickerPreAddonColorSquare']) &&
                $this->params['fieldColorpickerPreAddonColorSquare'] === true
            ) {
                $this->preAddon();
            }

            $this->Input();

            if (isset($this->params['fieldColorpickerPostAddonColorSquare']) &&
                $this->params['fieldColorpickerPostAddonColorSquare'] === true
            ) {
                $this->postAddon();

                $this->content .= '</div>';
            } else {
                $this->content .= '</div>';
            }
        } else {
            $this->Input();
        }
    }

    protected function preAddon()
    {
        $this->content .=
            '<span class="input-group-prepend">
                <span class="input-group-text rounded-0 colorpicker-input-addon">
                    <i></i>
                </span>
            </span>';
    }

    protected function Input()
    {
        $this->content .=
            '<input '. $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' type="text" class="form-control ' . $this->fieldParams['fieldSize'] . ' rounded-0 ' . $this->fieldParams['fieldInputAdditionalClass'] .'" ' . $this->fieldParams['fieldId'] . '" ' . $this->fieldParams['fieldName'] . '"  placeholder="' . strtoupper($this->fieldParams['fieldPlaceholder']) . '" ' . $this->fieldParams['fieldDataAttributes'] . ' ' . $this->fieldParams['fieldDisabled'] . ' value="' . $this->fieldParams['fieldValue'] . '" />';
    }

    protected function postAddon()
{
        $this->content .=
            '<span class="input-group-append">
                <span class="input-group-text rounded-0 colorpicker-input-addon">
                    <i></i>
                </span>
            </span>';
    }
}