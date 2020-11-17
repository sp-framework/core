<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags\Fields;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class Checkbox
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
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->params = $params;

        $this->fieldParams = $fieldParams;

        $this->adminLTETags =
            new AdminLTETags($this->view, $this->tag, $this->links, $this->escaper);

        $this->generateContent();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateContent()
    {
        // fieldCheckboxInline : If true, checkbox text will be inline with checkbox
        // fieldCheckboxChecked : It true, check has checked attribute set to true

        $this->fieldParams['fieldCheckboxType'] =
            isset($this->params['fieldCheckboxType']) ?
            $this->params['fieldCheckboxType'] :
            'primary';

        $this->fieldParams['fieldCheckboxInline'] =
            isset($this->params['fieldCheckboxInline']) ?
            ' icheck-inline' :
            '';

        $this->fieldParams['fieldCheckboxChecked'] =
            isset($this->params['fieldCheckboxChecked']) && $this->params['fieldCheckboxChecked'] === true ?
            'checked' :
            '';

        $this->fieldParams['fieldCheckboxLabel'] =
            isset($this->params['fieldCheckboxLabel']) ?
            $this->params['fieldCheckboxLabel'] :
            ' ';

        $this->fieldParams['fieldCheckboxAdditionClass'] =
            isset($this->params['fieldCheckboxAdditionClass']) ?
            $this->params['fieldCheckboxAdditionClass'] :
            '';

        $this->fieldParams['showFieldCheckboxLabelHelpAndRequired'] =
            $this->params['fieldLabel'] === false ?
            '<span>' . $this->fieldParams['fieldHelp'] . ' ' . $this->fieldParams['fieldRequired'] . '</span>' :
            '';

        $this->content .=
            '<div class="icheck-' . $this->fieldParams['fieldCheckboxType'] . $this->fieldParams['fieldCheckboxInline'] . ' ' . $this->fieldParams['fieldCheckboxAdditionClass'] . '">
                <input ' . $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' type="checkbox" ' . $this->fieldParams['fieldId'] . '" ' . $this->fieldParams['fieldName'] . '" ' . $this->fieldParams['fieldDisabled']  . ' ' . $this->fieldParams['fieldDataAttributes'] . ' ' . $this->fieldParams['fieldCheckboxChecked'] . ' />
                    <label ' . $this->fieldParams['forId'] . '" >' . $this->fieldParams['fieldCheckboxLabel'] . '</label>' .
                    $this->fieldParams['showFieldCheckboxLabelHelpAndRequired'] . '
            </div>';
    }
}