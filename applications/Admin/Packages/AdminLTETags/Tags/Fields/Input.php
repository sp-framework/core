<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags\Fields;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class Input
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
        $this->adminLTETags =
            new AdminLTETags($this->view, $this->tag, $this->links, $this->escaper);

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
        $this->fieldParams['fieldGroupSize'] =
            isset($this->params['fieldGroupSize']) ?
            $this->params['fieldGroupSize'] :
            'sm';

        $this->fieldParams['fieldSize'] =
            isset($this->params['fieldSize']) ?
            'form-control-' . $this->params['fieldSize'] :
            'form-control-sm';

        $this->fieldParams['fieldInputType'] =
            isset($this->params['fieldInputType']) ?
            $this->params['fieldInputType'] :
            'text';

        /*  fieldInputTypeTextFilter (Works with JS)
            available options:      int (only numbers can be negative),
                                    positiveInt (only positive numbers),
                                    positiveIntMax (only positive numbers, works with max="" html5 attribute),
                                    float (float with unlimited numbers after the decimal),
                                    positiveFloat,
                                    positiveFloatMax,
                                    percent (float with only 2 numbers after decimal),
                                    positivePercent,
                                    positivePercentMax,
                                    currency (float or command (for Europe) with only 2 numbers after decimal),
                                    positiveCurrency,
                                    positiveCurrencyMax,
                                    char (only alphabets),
                                    hex (only hex values)
        */
        $this->fieldParams['fieldInputTypeTextFilter'] =
            isset($this->params['fieldInputTypeTextFilter']) ?
            'data-fieldinputfilter=' . $this->params['fieldInputTypeTextFilter'] . '"' :
            '';

        $this->fieldParams['fieldGroupPreAddonTextAdditionalClass'] =
            isset($this->params['fieldGroupPreAddonTextAdditionalClass']) ?
            $this->params['fieldGroupPreAddonTextAdditionalClass'] :
            '';

        if (isset($this->params['fieldGroupPreAddonText']) ||
            isset($this->params['fieldGroupPreAddonIcon']) ||
            isset($this->params['fieldGroupPreAddonDropdown']) ||
            isset($this->params['fieldGroupPreAddonButtonId']) ||
            isset($this->params['fieldGroupPreAddonButtons']) ||
            isset($this->params['fieldGroupPostAddonText']) ||
            isset($this->params['fieldGroupPostAddonIcon']) ||
            isset($this->params['fieldGroupPostAddonDropdown']) ||
            isset($this->params['fieldGroupPostAddonButtonId']) ||
            isset($this->params['fieldGroupPostAddonButtons'])
        ) {
            $this->content .=
                '<div class="input-group input-group-' . $this->fieldParams['fieldGroupSize'] . '">';

            if (isset($this->params['fieldGroupPreAddonText']) ||
                isset($this->params['fieldGroupPreAddonIcon']) ||
                isset($this->params['fieldGroupPreAddonDropdown']) ||
                isset($this->params['fieldGroupPreAddonButtonId']) ||
                isset($this->params['fieldGroupPreAddonButtons'])
            ) {
                $this->preAddon();
            }

            if ($this->fieldParams['fieldInputType'] === 'select') {
                $this->select();
            } else {
                $this->Input();
            }

            if (isset($this->params['fieldGroupPostAddonText']) ||
                isset($this->params['fieldGroupPostAddonIcon']) ||
                isset($this->params['fieldGroupPostAddonDropdown']) ||
                isset($this->params['fieldGroupPostAddonButtonId']) ||
                isset($this->params['fieldGroupPostAddonButtons'])
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
        if (isset($this->params['fieldGroupPreAddonText'])) {
            $this->content .=
                '<div class="input-group-prepend">
                    <span class="input-group-text rounded-0 ' . $this->fieldParams['fieldGroupPreAddonTextAdditionalClass'] . '">' . $this->params['fieldGroupPreAddonText'] . '</span>
                </div>';

        }

        if (isset($this->params['fieldGroupPreAddonIcon'])) {
            $this->content .=
                '<div class="input-group-prepend">
                    <span class="input-group-text rounded-0">
                        <i class="fas fa-fw fa-' . $this->params['fieldGroupPreAddonIcon'] . '"></i>
                    </span>
                </div>';

        }

        if (isset($this->params['fieldGroupPreAddonDropdown'])) {

            $this->fieldParams['fieldGroupPreAddonDropdownButtonClass'] =
                isset($this->params['fieldGroupPreAddonDropdownButtonClass']) ?
                $this->params['fieldGroupPreAddonDropdownButtonClass'] :
                'primary';

            $this->fieldParams['fieldGroupPreAddonDropdownButtonDisabled'] =
                isset($this->params['fieldGroupPreAddonDropdownButtonDisabled']) &&
                $this->params['fieldGroupPreAddonDropdownButtonDisabled'] === true ?
                'disabled' :
                '';

            $this->fieldParams['fieldGroupPreAddonDropdownButtonTitle'] =
                isset($this->params['fieldGroupPreAddonDropdownButtonTitle']) ?
                $this->params['fieldGroupPreAddonDropdownButtonTitle'] :
                'MISSING BUTTON TITLE';

            $this->fieldParams['fieldGroupPreAddonDropdownButtonListTitle'] =
                isset($this->params['fieldGroupPreAddonDropdownButtonListTitle']) ?
                $this->params['fieldGroupPreAddonDropdownButtonListTitle'] :
                null;

            $this->content .=
                '<div class="input-group-prepend">
                    <button type="button" ' . $this->fieldParams['fieldId'] . '-prepend-dropdown-button" class="btn btn-' . $this->fieldParams['fieldGroupPreAddonDropdownButtonClass'] . ' dropdown-toggle rounded-0" data-toggle="dropdown" aria-expanded="false" '. $this->fieldParams['fieldGroupPreAddonDropdownButtonDisabled'] . '>
                        <span>' . $this->fieldParams['fieldGroupPreAddonDropdownButtonTitle'] . '</span>
                    </button>
                    <ul class="dropdown-menu">';

            if ($this->fieldParams['fieldGroupPreAddonDropdownButtonListTitle']) {
                foreach ($this->fieldParams['fieldGroupPreAddonDropdownButtonListTitle'] as $title) {
                    if ($title === 'divider') {
                        $this->content .=
                            '<li class="dropdown-divider"></li>';
                    } else {
                        $this->content .=
                            '<li class="dropdown-item">
                                <a ' . $this->fieldParams['fieldId'] . '-' . strtolower($title) . '" href="#">' . $title . '</a>
                            </li>';
                    }
                }
            }

            $this->content .= '<ul></div>';

        }

        if (isset($this->params['fieldGroupPreAddonButtonId']) &&
                   isset($this->params['fieldGroupPreAddonButtonValue'])
        ) {
            $this->fieldParams['fieldGroupPreAddonButtonClass'] =
                isset($this->params['fieldGroupPreAddonButtonClass']) ?
                $this->params['fieldGroupPreAddonButtonClass'] :
                'primary';

            $this->fieldParams['fieldGroupPreAddonButtonTooltipPosition'] =
                isset($this->params['fieldGroupPreAddonButtonTooltipPosition']) ?
                $this->params['fieldGroupPreAddonButtonTooltipPosition'] :
                'auto';

            $this->fieldParams['fieldGroupPreAddonButtonTooltipTitle'] =
                isset($this->params['fieldGroupPreAddonButtonTooltipTitle']) ?
                $this->params['fieldGroupPreAddonButtonTooltipTitle'] :
                'Tooltip Title missing';

            $this->fieldParams['fieldGroupPreAddonButtonDisabled'] =
                isset($this->params['fieldGroupPreAddonButtonDisabled']) &&
                $this->params['fieldGroupPreAddonButtonDisabled'] === true ?
                'disabled' :
                '';

            if (isset($this->params['fieldGroupPreAddonButtonIcon'])) {
                if ($this->params['fieldGroupPreAddonButtonIcon'] === 'after') {
                    $this->params['fieldGroupPreAddonButtonValue'] =
                        strtoupper($this->params['fieldGroupPreAddonButtonValue']) . ' ' .
                        '<i class="fas fa-fw fa-' . $this->params['fieldGroupPreAddonButtonIcon'] . ' ml-1"></i>';
                } else {
                    $this->params['fieldGroupPreAddonButtonValue'] =
                        '<i class="fas fa-fw fa-' . $this->params['fieldGroupPreAddonButtonIcon'] . ' mr-1"></i>' . ' ' .
                        strtoupper($this->params['fieldGroupPreAddonButtonValue']);
                }
            }

            $this->content .=
                '<div class="input-group-prepend">
                    <button ' . $this->fieldParams['fieldId'] . '-' . $this->params['fieldGroupPreAddonButtonId'] . '" class="btn btn-'. $this->fieldParams['fieldGroupPreAddonButtonClass'] . ' rounded-0" type="button" data-toggle="tooltip" data-html="true" data-placement="' . $this->fieldParams['fieldGroupPreAddonButtonTooltipPosition']. '" title="' . $this->fieldParams['fieldGroupPreAddonButtonTooltipTitle'] . '" ' . $this->fieldParams['fieldGroupPreAddonButtonDisabled'] . '>' . $this->params['fieldGroupPreAddonButtonValue'] . '</button>
                </div>' ;
        }

        if (isset($this->params['fieldGroupPreAddonButtons'])) {

            $this->content .=
                '<div class="input-group-append">';

            $this->content .=
                $this->adminLTETags->useTag(
                        'buttons',
                        [
                            'componentId'           => $this->params['componentId'],
                            'sectionId'             => $this->params['sectionId'],
                            'buttonType'            => 'button',
                            'buttons'               => $this->params['fieldGroupPreAddonButtons'],
                        ]
                    );

            $this->content .= '</div>';
        }
    }

    protected function Input()
    {
        $this->content .=
            '<input '. $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' type="' . $this->fieldParams['fieldInputType'] . '" class="form-control ' . $this->fieldParams['fieldSize'] . ' rounded-0" ' . $this->fieldParams['fieldId'] . '" ' . $this->fieldParams['fieldName'] . '"  placeholder="' . strtoupper($this->fieldParams['fieldPlaceholder']) . '" ' . $this->fieldParams['fieldDataAttributes'] . ' ' . $this->fieldParams['fieldDataInputMinNumber'] . ' ' . $this->fieldParams['fieldDataInputMaxNumber'] . ' ' . $this->fieldParams['fieldDataInputMinLength'] . ' ' . $this->fieldParams['fieldDataInputMaxLength'] . ' ' . $this->fieldParams['fieldDisabled'] . ' ' . $this->fieldParams['fieldInputTypeTextFilter'] . ' value="' . $this->fieldParams['fieldValue'] . '" />';
    }

    protected function select()
    {
        $this->fieldParams['fieldDataSelectTreeData'] =
            isset($this->params['fieldDataSelectOptionsArray']) && $this->params['fieldDataSelectOptionsArray'] === true ?
            [$this->params['fieldDataSelectOptions']] :
            $this->params['fieldDataSelectOptions'];

        $this->fieldParams['fieldDataSelectOptionsKey'] =
            isset($this->params['fieldDataSelectOptionsKey']) ?
            $this->params['fieldDataSelectOptionsKey'] :
            '';

        $this->fieldParams['fieldDataSelectOptionsValue'] =
            isset($this->params['fieldDataSelectOptionsValue']) ?
            $this->params['fieldDataSelectOptionsValue'] :
            '';

        $this->fieldParams['fieldDataSelectOptionsSelected'] =
            isset($this->params['fieldDataSelectOptionsSelected']) ?
            $this->params['fieldDataSelectOptionsSelected'] :
            '';

        $this->content .=
            '<select ' . $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' class="custom-select rounded-0" ' . $this->fieldParams['fieldId'] . '" ' . $this->fieldParams['fieldName'] . '" ' . $this->fieldParams['fieldDataAttributes'] . ' ' . $this->fieldParams['fieldDisabled'] . '>';

        if (isset($this->params['fieldDataSelectOptionsZero']) && $this->params['fieldDataSelectOptionsZero'] === true) {
            if (isset($this->params['fieldDataSelectOptionsZeroTitle'])) {
                $this->content .=
                    '<option data-value="0" value="0">' . $this->params['fieldDataSelectOptionsZeroTitle'] . '</option>';
            } else {
                $this->content .=
                    '<option></option>';
            }
        }

        if ($this->fieldParams['fieldDataSelectTreeData']) {

            $this->content .=
                $this->adminLTETags->useTag(
                        'tree',
                        [
                            'treeMode'      =>  'select',
                            'treeData'      =>  $this->fieldParams['fieldDataSelectTreeData'],
                            'fieldParams'   =>  $this->fieldParams
                        ],
                    );
        }

        $this->content .= '</select>';
    }

    protected function postAddon()
    {
        if (isset($this->params['fieldGroupPostAddonText'])) {

            $this->content .=
                '<div class="input-group-append">
                    <span class="input-group-text rounded-0">{{fieldGroupPostAddonText|raw}}</span>
                </div>';
        }
        if (isset($this->params['fieldGroupPostAddonIcon'])) {

            $this->content .=
                '<div class="input-group-append">
                    <span class="input-group-text rounded-0">
                        <i class="fas fa-fw fa-' . $this->params['fieldGroupPostAddonIcon'] . '"></i>
                    </span>
                </div>';
        }
        if (isset($this->params['fieldGroupPostAddonDropdown'])) {

            $this->fieldParams['fieldGroupPostAddonDropdownButtonClass'] =
                isset($this->params['fieldGroupPostAddonDropdownButtonClass']) ?
                $this->params['fieldGroupPostAddonDropdownButtonClass'] :
                'primary';

            $this->fieldParams['fieldGroupPostAddonDropdownButtonDisabled'] =
                isset($this->params['fieldGroupPostAddonDropdownButtonDisabled']) &&
                $this->params['fieldGroupPostAddonDropdownButtonDisabled'] === true ?
                'disabled' :
                '';

            $this->fieldParams['fieldGroupPostAddonDropdownButtonTitle'] =
                isset($this->params['fieldGroupPostAddonDropdownButtonTitle']) ?
                $this->params['fieldGroupPostAddonDropdownButtonTitle'] :
                'MISSING BUTTON TITLE';

            $this->fieldParams['fieldGroupPostAddonDropdownButtonListTitle'] =
                isset($this->params['fieldGroupPostAddonDropdownButtonListTitle']) ?
                $this->params['fieldGroupPostAddonDropdownButtonListTitle'] :
                null;

            $this->content .=
                '<div class="input-group-append">
                    <button type="button" id="' . $this->fieldParams['fieldId'] . '-append-dropdown-button" class="btn btn-' . $this->fieldParams['fieldGroupPostAddonDropdownButtonClass'] . ' dropdown-toggle rounded-0" data-toggle="dropdown" aria-expanded="false" ' . $this->fieldParams['fieldGroupPostAddonDropdownButtonDisabled'] . '>
                        <span>' . $this->fieldParams['fieldGroupPostAddonDropdownButtonTitle'] . '</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">';

            if ($this->fieldParams['fieldGroupPostAddonDropdownButtonListTitle']) {
                foreach ($this->fieldParams['fieldGroupPostAddonDropdownButtonListTitle'] as $title) {
                    if ($title === 'divider') {
                        $this->content .=
                            '<li class="dropdown-divider"></li>';
                    } else {
                        $this->content .=
                            '<li class="dropdown-item">
                                <a ' . $this->fieldParams['fieldId'] . '-' . strtolower($title) . '" href="#">' . $title . '</a>
                            </li>';
                    }
                }
            }

            $this->content .= '<ul></div>';

        }

        if (isset($this->params['fieldGroupPostAddonButtonId']) &&
            isset($this->params['fieldGroupPostAddonButtonValue'])
        ) {
            $this->fieldParams['fieldGroupPostAddonButtonClass'] =
                isset($this->params['fieldGroupPostAddonButtonClass']) ?
                $this->params['fieldGroupPostAddonButtonClass'] :
                'primary';

            $this->fieldParams['fieldGroupPostAddonButtonTooltipPosition'] =
                isset($this->params['fieldGroupPostAddonButtonTooltipPosition']) ?
                $this->params['fieldGroupPostAddonButtonTooltipPosition'] :
                'auto';

            $this->fieldParams['fieldGroupPostAddonButtonTooltipTitle'] =
                isset($this->params['fieldGroupPostAddonButtonTooltipTitle']) ?
                $this->params['fieldGroupPostAddonButtonTooltipTitle'] :
                'Tooltip Title missing';

            $this->fieldParams['fieldGroupPostAddonButtonDisabled'] =
                isset($this->params['fieldGroupPostAddonButtonDisabled']) &&
                $this->params['fieldGroupPostAddonButtonDisabled'] === true ?
                'disabled' :
                '';

            if (isset($this->params['fieldGroupPostAddonButtonIcon'])) {
                $iconHidden =
                    (isset($this->params['fieldGroupPostAddonButtonIconHidden'])) &&
                        $this->params['fieldGroupPostAddonButtonIconHidden'] === true ?
                    'hidden' :
                    '';

                if ($this->params['fieldGroupPostAddonButtonIcon'] === 'after') {
                    $this->params['fieldGroupPostAddonButtonValue'] =
                        strtoupper($this->params['fieldGroupPostAddonButtonValue']) . ' ' .
                        '<i class="fas fa-fw fa-' . $this->params['fieldGroupPostAddonButtonIcon'] . ' ml-1" ' . $iconHidden . '></i>';
                } else {
                    $this->params['fieldGroupPostAddonButtonValue'] =
                        '<i class="fas fa-fw fa-' . $this->params['fieldGroupPostAddonButtonIcon'] . ' mr-1" ' . $iconHidden . '></i>' . ' ' .
                        strtoupper($this->params['fieldGroupPostAddonButtonValue']);
                }
            }

            $this->content .=
                '<div class="input-group-append">
                    <button ' . $this->fieldParams['fieldId'] . '-' . $this->params['fieldGroupPostAddonButtonId'] . '" class="btn btn-'. $this->fieldParams['fieldGroupPostAddonButtonClass'] . ' rounded-0" type="button" data-toggle="tooltip" data-html="true" data-placement="' . $this->fieldParams['fieldGroupPostAddonButtonTooltipPosition']. '" title="' . $this->fieldParams['fieldGroupPostAddonButtonTooltipTitle'] . '" ' . $this->fieldParams['fieldGroupPostAddonButtonDisabled'] . '>' . $this->params['fieldGroupPostAddonButtonValue'] . '</button>
                </div>' ;

        }

        if (isset($this->params['fieldGroupPostAddonButtons'])) {

            $this->content .=
                '<div class="input-group-append">';

            $this->content .=
                $this->adminLTETags->useTag(
                        'buttons',
                        [
                            'componentId'           => $this->params['componentId'],
                            'sectionId'             => $this->params['sectionId'],
                            'buttonType'            => 'button',
                            'buttons'               => $this->params['fieldGroupPostAddonButtons'],
                        ]
                    );

            $this->content .= '</div>';
        }
    }
}