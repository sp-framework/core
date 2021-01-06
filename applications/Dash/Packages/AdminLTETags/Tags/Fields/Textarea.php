<?php

namespace Applications\Dash\Packages\AdminLTETags\Tags\Fields;

use Applications\Dash\Packages\AdminLTETags\AdminLTETags;

class Textarea
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
        $this->fieldParams['fieldTextareaRows'] =
            isset($this->params['fieldTextareaRows']) ?
            'rows="' . $this->params['fieldTextareaRows'] . '"' :
            'rows="4"';

        $this->fieldParams['fieldTextareaCols'] =
            isset($this->params['fieldTextareaCols']) ?
            'cols="' . $this->params['fieldTextareaCols'] . '"' :
            '';

        $this->content .=
            '<textarea ' . $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' class="form-control form-control-sm rounded-0 ' . $this->fieldParams['fieldInputAdditionalClass'] .'" ' . $this->fieldParams['fieldId'] . '" ' . $this->fieldParams['fieldName'] . '" placeholder="' . strtoupper($this->fieldParams['fieldPlaceholder']) . '" ' . $this->fieldParams['fieldDataInputMinLength'] . ' ' . $this->fieldParams['fieldDataInputMaxLength'] . ' ' . $this->fieldParams['fieldDisabled'] . ' ' . $this->fieldParams['fieldTextareaRows'] . ' ' . $this->fieldParams['fieldTextareaCols'] . '>' . $this->fieldParams['fieldValue'] . '</textarea>';
    }
}