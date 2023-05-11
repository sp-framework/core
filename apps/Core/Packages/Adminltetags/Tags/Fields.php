<?php

namespace Apps\Core\Packages\Adminltetags\Tags;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Fields extends Adminltetags
{
    protected $params;

    protected $content = '';

    protected $fieldParams = [];

    public function getContent(array $params)
    {
        $this->params = $params;

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        // fieldType - as per fieldType, code is generated
        if (!isset($this->params['fieldType'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">fieldType missing</span>';
            return;
        }

        $this->buildFieldParamsArr();

        if (isset($this->params['fieldBazJstreeSearch']) &&
            $this->params['fieldBazJstreeSearch'] === true
        ) {
            if ($this->params['fieldType'] === 'checkbox' && $this->fieldParams['fieldLabel'] === false && $this->params['fieldCheckboxLabel'] !== '') {
                $this->content .=
                    '<div ' . $this->fieldParams['jstreeSearchId'] . ' jstree-search="' . strtoupper($this->params['fieldCheckboxLabel']) . '">';
            } else {
                $this->content .=
                    '<div ' . $this->fieldParams['jstreeSearchId'] . ' jstree-search="' . strtoupper($this->fieldParams['fieldLabel']) . '">';
            }
        }

        $this->content .=
            '<div class="form-group ' . $this->fieldParams['fieldAdditionalClass'] . ' ' . $this->fieldParams['fieldHidden'] . '">';

        if ($this->fieldParams['fieldLabel']) {
            if (isset($this->params['fieldLabelLegend']) && $this->params['fieldLabelLegend'] == true) {
                $tag = 'legend';
            } else {
                $tag = 'label';
            }

            $this->content .=
                '<' . $tag . ' ' .  $this->fieldParams['fieldLabelClass'] . '>' . strtoupper($this->fieldParams['fieldLabel']) . '</' . $tag . '> ' .
                $this->fieldParams['fieldHelp'] . ' ' .
                $this->fieldParams['fieldRequired'];
        }

        if ($this->params['fieldType'] !== false) {
            try {
                $field = 'Apps\\Core\\Packages\\Adminltetags\\Tags\\Fields' . $this->getFieldType();

                $this->content .=
                    (new $field($this->view, $this->tag, $this->links, $this->escaper, $this->params, $this->fieldParams))->getContent();

            } catch (\Exception $e) {
                throw $e;
            }
        }

        $this->content .= '</div>';

        if (isset($this->params['fieldBazJstreeSearch']) &&
            $this->params['fieldBazJstreeSearch'] === true
        ) {
            $this->content .= '</div>';
        }

        if (isset($this->params['fieldInputPasswordStrengthMeter']) &&
            $this->params['fieldInputPasswordStrengthMeter'] == true
        ) {
            $this->content .= $this->useTag('fields',
                [
                    'componentId'                           => $this->params['componentId'],
                    'sectionId'                             => $this->params['sectionId'],
                    'fieldId'                               => $this->params['fieldId'] . '-strength-meter',
                    'fieldLabel'                            => false,
                    'fieldHidden'                           => false,
                    'fieldType'                             => 'html',
                    'fieldRequired'                         => false,
                    'fieldBazScan'                          => false,
                    'fieldBazPostOnCreate'                  => false,
                    'fieldBazPostOnUpdate'                  => false
                ]
            );
        }
    }

    protected function getFieldType()
    {
        $path = explode('/', $this->params['fieldType']);

        $fieldClass = '';

        if (count($path) > 1) {
            foreach ($path as $value) {
                $fieldClass .= '\\' . ucfirst($value);
            }
        } else {
            $fieldClass .= '\\' . ucfirst($this->params['fieldType']);
        }

        return $fieldClass;
    }

    protected function buildFieldParamsArr()
    {
        // fieldId - field id is auto generated using componentId-sectionId-fieldId
        if (!isset($this->params['componentId']) ||
            !isset($this->params['sectionId'])
        ) {
            throw new \Exception('componentId or sectionId missing');
            return;
        }

        if (!isset($this->params['fieldId'])) {
            throw new \Exception('fieldId missing');
        }

        $this->fieldParams['idChain'] = $this->params['componentId'] . '-' . $this->params['sectionId'];
        $this->fieldParams['fieldId'] = 'id="' . $this->fieldParams['idChain'] . '-' . $this->params['fieldId'];
        $this->fieldParams['forId'] = 'for="' . $this->fieldParams['idChain'] . '-' . $this->params['fieldId'];
        $this->fieldParams['fieldName'] = 'name="' . $this->fieldParams['idChain'] . '-' . $this->params['fieldId'];
        $this->fieldParams['jstreeSearchId'] = 'id=' . $this->fieldParams['idChain'] . '-' . $this->params['fieldId'] . '-jstreesearch';


        // fieldLabel - field label fall between <label></label> and other places
        if (isset($this->params['fieldLabel'])) {
            $this->fieldParams['fieldLabel'] =
                $this->params['fieldLabel'] ?
                $this->params['fieldLabel'] :
                false;

        } else {
            $this->fieldParams['fieldLabel'] = '<label>missing_fieldLabel</label>';
        }

        $this->fieldParams['fieldLabelClass'] =
            isset($this->params['fieldLabelClass']) ?
            'class="'. $this->params['fieldLabelClass'] . '"' :
            '';

        // fieldPlaceholder - field placeholder, if false, then field label is used.
        if (isset($this->params['fieldPlaceholder'])) {
            $this->fieldParams['fieldPlaceholder'] = $this->params['fieldPlaceholder'];
        } else if (isset($this->params['fieldLabel']) &&
                   $this->params['fieldLabel'] !== false
        ) {
            $this->fieldParams['fieldPlaceholder'] = $this->params['fieldLabel'];
        } else {
            $this->fieldParams['fieldPlaceholder'] = 'Missing_fieldPlaceholder_and_fieldLabel';
        }

        // fieldHelp - If field help is true, a question mark will appear on the right of field label, hover will have tooltip.
        // Tooltip parameters:
            // fieldHelpTooltipTitle : Title of tooltip || Tooltip Title (HTML Allowed)
            // fieldHelpTooltipPosition : Position of tooltip || top

        if (isset($this->params['fieldHelp']) && $this->params['fieldHelp'] !== false) {

            $this->fieldParams['fieldHelpTooltipPosition'] =
                isset($this->params['fieldHelpTooltipPosition']) ?
                $this->params['fieldHelpTooltipPosition'] :
                'auto';

            $this->fieldParams['fieldHelpStyle'] =
                isset($this->params['fieldHelpStyle']) ?
                $this->params['fieldHelpStyle'] :
                'popover';

            $this->fieldParams['fieldHelpTooltipContent'] =
                isset($this->params['fieldHelpTooltipContent']) ?
                $this->params['fieldHelpTooltipContent'] :
                'missing_fieldHelpTooltipContent';

            if ($this->fieldParams['fieldHelpStyle'] === 'popover') {
                $this->fieldParams['fieldHelp'] =
                    '<a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="' .
                    strtoupper($this->fieldParams['fieldLabel']) . '" data-content="' . $this->fieldParams['fieldHelpTooltipContent'] . '" data-placement="' . $this->fieldParams['fieldHelpTooltipPosition'] . '" class="fa fa-fw fa-question-circle fa-1 helper"></a>';
            }

            if ($this->fieldParams['fieldHelpStyle'] === 'tooltip') {
                $this->fieldParams['fieldHelp'] =
                    '<span><i data-toggle="tooltip" data-html="true" data-placement="' . $this->fieldParams['fieldHelpTooltipPosition'] . '" title="' . $this->fieldParams['fieldHelpTooltipContent'] . '" class="fa fa-fw fa-question-circle fa-1 helper"></i></span>';
            }
        } else {
            $this->fieldParams['fieldHelp'] = '';
        }

        // fieldRequired - If field required is true, an exclamation mark will appear on the right of field label, hover will have tooltip.
        // Tooltip parameters:
        //     fieldRequiredTooltipTitle : Title of tooltip || Field cannot be empty. (HTML Allowed)
        //     fieldRequiredTooltipPosition : Position of tooltip || top
        if (isset($this->params['fieldRequired']) && $this->params['fieldRequired'] !== false) {
            $this->fieldParams['fieldRequiredTooltipPosition'] =
                isset($this->params['fieldRequiredTooltipPosition']) ?
                $this->params['fieldRequiredTooltipPosition'] :
                'auto';

            $this->fieldParams['fieldRequiredTooltipTitle'] =
                isset($this->params['fieldRequiredTooltipTitle']) ?
                $this->params['fieldRequiredTooltipTitle'] :
                'Required';

            $this->fieldParams['fieldRequired'] =
                '<span><sup><i data-toggle="tooltip" data-html="true" data-placement="' . $this->fieldParams['fieldRequiredTooltipPosition'] . '" title="' . $this->fieldParams['fieldRequiredTooltipTitle'] . '" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger"></i></sup></span>';
        } else if (isset($this->params['fieldRequired']) && $this->params['fieldRequired'] === false) {
            $this->fieldParams['fieldRequired'] = false;//cascading further down
        } else {
            $this->fieldParams['fieldRequired'] = '';
        }

        $this->fieldParams['fieldAdditionalClass'] =
            isset($this->params['fieldAdditionalClass']) ?
            $this->params['fieldAdditionalClass'] :
            '';

        $this->fieldParams['fieldInputAdditionalClass'] =
            isset($this->params['fieldInputAdditionalClass']) ?
            $this->params['fieldInputAdditionalClass'] :
            '';

        $this->fieldParams['fieldHidden'] =
            isset($this->params['fieldHidden']) && $this->params['fieldHidden'] === true ?
            'd-none' :
            '';

        $this->fieldParams['fieldBazScan'] =
            isset($this->params['fieldBazScan']) && $this->params['fieldBazScan'] === true ?
            'data-bazscantype="' . $this->params['fieldType'] . '"' :
            '';

        $this->fieldParams['fieldDataInputMinNumber'] =
            isset($this->params['fieldDataInputMinNumber']) ?
            'min="' . $this->params['fieldDataInputMinNumber'] . '"':
            '';

        $this->fieldParams['fieldDataInputMaxNumber'] =
            isset($this->params['fieldDataInputMaxNumber']) ?
            'max="' . $this->params['fieldDataInputMaxNumber'] . '"':
            '';

        $this->fieldParams['fieldDataInputMinLength'] =
            isset($this->params['fieldDataInputMinLength']) ?
            'minlength="' . $this->params['fieldDataInputMinLength'] . '"':
            '';

        $this->fieldParams['fieldDataInputMaxLength'] =
            isset($this->params['fieldDataInputMaxLength']) ?
            'maxlength="' . $this->params['fieldDataInputMaxLength'] . '"':
            '';

        $this->fieldParams['fieldDisabled'] =
            isset($this->params['fieldDisabled']) && $this->params['fieldDisabled'] === true ?
            'disabled' :
            '';

        // fieldBazPost: This is if you want BazContentFields.js to grab fields information and act on them. See BazContentFields.js documentation for more information as to what happens with certain field type.
        $this->fieldParams['fieldBazPostOnCreate'] =
            isset($this->params['fieldBazPostOnCreate']) && $this->params['fieldBazPostOnCreate'] === true ?
            'data-bazPostOnCreate="true"' :
            '';

        $this->fieldParams['fieldBazPostOnUpdate'] =
            isset($this->params['fieldBazPostOnUpdate']) && $this->params['fieldBazPostOnUpdate'] === true ?
            'data-bazPostOnUpdate="true"' :
            '';
        //

        if (isset($this->params['fieldDataAttributes']) &&
            is_array($this->params['fieldDataAttributes'])
        ) {
            $this->fieldParams['fieldDataAttributes'] = '';
            foreach ($this->params['fieldDataAttributes'] as $attrKey => $attrValue) {
                $this->fieldParams['fieldDataAttributes'] .= 'data-' . $attrKey . '="' . $attrValue . '" ';
            }
        } else {
            $this->fieldParams['fieldDataAttributes'] = '';
        }

        $this->fieldParams['fieldValue'] =
            isset($this->params['fieldValue']) ?
            $this->params['fieldValue'] :
            '';
    }
}