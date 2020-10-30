<?php

namespace Applications\Admin\Packages\AdminLTETags;

use Applications\Admin\Packages\AdminLTETags;

class Fields extends AdminLTETags
{
    protected $params;

    protected $content = '';

    protected $fieldParams = [];

    public function getContent($params)
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

        try {
            $field = 'Applications\\Admin\\Packages\\AdminLTETags\\Fields\\' . ucfirst($this->params['fieldType']);

            $this->content .= (new $field($this->view, $this->tag, $this->links, $this->params, $this->fieldParams))->getContent();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

    protected function buildFieldParamsArr()
    {
        // fieldId - field id is auto generated using componentId-sectionId-fieldId
        if (!isset($this->params['componentId']) ||
            !isset($this->params['sectionId'])
        ) {
            $this->content .=
                '<span class="text-uppercase text-danger">componentId or sectionId missing</span>';
            return;
        }

        if (!isset($this->params['fieldId'])) {
            $this->content .=
                '<span class="text-uppercase text-danger">fieldId missing</span>';
            return;
        }

        $this->fieldParams['idChain'] = $this->params['componentId'] . '-' . $this->params['sectionId'];
        $this->fieldParams['fieldId'] = 'id="' . $this->fieldParams['idChain'] . '-' . $this->params['fieldId'];
        $this->fieldParams['forId'] = 'for="' . $this->fieldParams['idChain'] . '-' . $this->params['fieldId'];
        $this->fieldParams['fieldName'] = 'name="' . $this->fieldParams['idChain'] . '-' . $this->params['fieldId'];

        // $jstreeSearchId = 'id=' ~ idChain ~ fieldId ~ '-jstreesearch';

        // fieldLabel - field label fall between <label></label> and other places
        if (isset($this->params['fieldLabel'])) {
            $this->fieldParams['fieldLabel'] =
                $this->params['fieldLabel'] !== false ?
                $this->params['fieldLabel'] :
                '';

        } else {
            $this->fieldParams['fieldLabel'] = '<label>missing_fieldLabel</label>';
        }

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

        if (isset($this->params['fieldHelp'])) {

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
        if (isset($this->params['fieldRequired'])) {
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
        }

        $this->fieldParams['fieldHidden'] =
            isset($this->params['fieldHidden']) ?
            $this->params['fieldHidden'] :
            '';

        $this->fieldParams['fieldBazScan'] =
            isset($this->params['fieldBazScan']) ?
                $this->params['fieldBazScan'] === true ?
                    'data-bazScanType="' . $this->params['fieldType'] . '"' :
                    ''
            : '';

        $this->fieldParams['fieldDataMinNumber'] =
            isset($this->params['fieldDataMinNumber']) ?
            'min="' . $this->params['fieldDataMinNumber'] . '"':
            '';

        $this->fieldParams['fieldDataMaxNumber'] =
            isset($this->params['fieldDataMaxNumber']) ?
            'max="' . $this->params['fieldDataMaxNumber'] . '"':
            '';

        $this->fieldParams['fieldDataMinLength'] =
            isset($this->params['fieldDataMinLength']) ?
            'minlength="' . $this->params['fieldDataMinLength'] . '"':
            '';

        $this->fieldParams['fieldDataMaxLength'] =
            isset($this->params['fieldDataMaxLength']) ?
            'maxlength="' . $this->params['fieldDataMaxLength'] . '"':
            '';

        $this->fieldParams['fieldDisabled'] =
            isset($this->params['fieldDisabled']) ?
                $this->params['fieldDisabled'] === true ?
                    'disabled' :
                    ''
            : '';

        // fieldBazPost: This is if you want BazContentFields.js to grab fields information and act on them. See BazContentFields.js documentation for more information as to what happens with certain field type.
        $this->fieldParams['fieldBazPostOnCreate'] =
            isset($this->params['fieldBazPostOnCreate']) ?
            'data-bazPostOnCreate="true"' :
            '';

        $this->fieldParams['fieldBazPostOnUpdate'] =
            isset($this->params['fieldBazPostOnUpdate']) ?
            'data-bazPostOnUpdate="true"' :
            '';

        //
    }
}