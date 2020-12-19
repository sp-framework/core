<?php

namespace Applications\Ecom\Dashboard\Packages\AdminLTETags\Tags\Fields;

use Applications\Ecom\Dashboard\Packages\AdminLTETags\AdminLTETags;

class Select2
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
        //We process array for select to that is wrapped inside an array. List data received from sdk is wrapped inside array. But, if the data is received from component, it will not be wrapped. So, we need to check and re-wrap the component data into an array #}
        $this->fieldParams['fieldDataSelect2TreeData'] =
            isset($this->params['fieldDataSelect2OptionsArray']) && $this->params['fieldDataSelect2OptionsArray'] === true ?
            [$this->params['fieldDataSelect2Options']] :
            $this->params['fieldDataSelect2Options'];

        $this->fieldParams['fieldSelect2Type'] =
            isset($this->params['fieldSelect2Type']) ?
            $this->params['fieldSelect2Type'] :
            'primary';

        $this->fieldParams['fieldDataSelect2Multiple'] =
            isset($this->params['fieldDataSelect2Multiple']) && $this->params['fieldDataSelect2Multiple'] === true ?
            'multiple="multiple"' :
            '';

        $this->fieldParams['fieldDataSelect2MultipleObject'] =
            isset($this->params['fieldDataSelect2MultipleObject']) && $this->params['fieldDataSelect2MultipleObject'] === true ?
            'multiple-object="true"' :
            '';

        $this->fieldParams['fieldDataSelect2OptionsKey'] =
            isset($this->params['fieldDataSelect2OptionsKey']) ?
            $this->params['fieldDataSelect2OptionsKey'] :
            '';

        $this->fieldParams['fieldDataSelect2OptionsValue'] =
            isset($this->params['fieldDataSelect2OptionsValue']) ?
            $this->params['fieldDataSelect2OptionsValue'] :
            '';

        $this->fieldParams['fieldDataSelect2OptionsSelected'] =
            isset($this->params['fieldDataSelect2OptionsSelected']) ?
            $this->params['fieldDataSelect2OptionsSelected'] :
            '';

        $this->content .=
            '<select ' . $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' class="form-control select2 select2-' . $this->fieldParams['fieldSelect2Type'] . '" data-dropdown-css-class="select2-' . $this->fieldParams['fieldSelect2Type'] . '" ' . $this->fieldParams['fieldId'] . '" ' . $this->fieldParams['fieldName'] . '" style="width:100%;" ' . $this->fieldParams['fieldDataSelect2Multiple'] . ' ' .$this->fieldParams['fieldDataSelect2MultipleObject'] . ' ' . $this->fieldParams['fieldDisabled'] . ' ' . $this->fieldParams['fieldDataAttributes'] . '>
                <option></option>';

        if ($this->fieldParams['fieldDataSelect2TreeData']) {

            $this->content .=
                $this->adminLTETags->useTag(
                        'tree',
                        [
                            'treeMode'      =>  'select2',
                            'treeData'      =>  $this->fieldParams['fieldDataSelect2TreeData'],
                            'fieldParams'   =>  $this->fieldParams
                        ],
                    );
        }

        $this->content .= '</select>';
    }
}