<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Fields;

use Apps\Core\Packages\Adminltetags\Adminltetags;

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

    protected $width;

    public function __construct($view, $tag, $links, $escaper, $params, $fieldParams)
    {
        $this->adminLTETags = new Adminltetags();

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

        //We process array for select to that is wrapped inside an array (double array).
        if (isset($this->params['fieldDataSelect2OptionsArray']) && $this->params['fieldDataSelect2OptionsArray'] === true) {
            //If data is already in an array
            $this->fieldParams['fieldDataSelect2TreeData'] = [$this->params['fieldDataSelect2Options']];
        } else if (isset($this->params['fieldDataSelect2OptionsArray']) && $this->params['fieldDataSelect2OptionsArray'] === false) {
            //If data is not in an array
            $this->fieldParams['fieldDataSelect2TreeData'] = [[$this->params['fieldDataSelect2Options']]];
        } else {
            //If data has childs
            $this->fieldParams['fieldDataSelect2TreeData'] = $this->params['fieldDataSelect2Options'];
        }

        $this->fieldParams['fieldSelect2Type'] =
            isset($this->params['fieldSelect2Type']) ?
            $this->params['fieldSelect2Type'] :
            'primary';

        $this->fieldParams['fieldDataSelect2Multiple'] =
            isset($this->params['fieldDataSelect2Multiple']) && $this->params['fieldDataSelect2Multiple'] === true ?
            'multiple="multiple"' :
            '';

        $this->fieldParams['fieldDataSelect2Create'] =
            isset($this->params['fieldDataSelect2Create']) && $this->params['fieldDataSelect2Create'] === true ?
            'data-create="true"' :
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

        $this->fieldParams['fieldDataSelect2AddDataAttrFromData'] =
            isset($this->params['fieldDataSelect2AddDataAttrFromData']) ?
            $this->params['fieldDataSelect2AddDataAttrFromData'] :
            null;

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
            $this->width = 'auto';

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

            $this->select2();

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
            $this->width = '100%';
            $this->select2();
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

            $this->fieldParams['fieldGroupPreAddonDropdownButtonHidden'] =
                isset($this->params['fieldGroupPreAddonDropdownButtonHidden']) &&
                $this->params['fieldGroupPreAddonDropdownButtonHidden'] === true ?
                'd-none' :
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
                    <button type="button" ' . $this->fieldParams['fieldId'] . '-prepend-dropdown-button" class="btn btn-' . $this->fieldParams['fieldGroupPreAddonDropdownButtonClass'] . ' dropdown-toggle rounded-0 ' . $this->fieldParams['fieldGroupPreAddonDropdownButtonHidden'] . '" data-toggle="dropdown" aria-expanded="false" '. $this->fieldParams['fieldGroupPreAddonDropdownButtonDisabled'] . '>
                        <span>' . $this->fieldParams['fieldGroupPreAddonDropdownButtonTitle'] . '</span>
                    </button>
                    <div class="dropdown-menu">';

            if ($this->fieldParams['fieldGroupPreAddonDropdownButtonListTitle']) {
                foreach ($this->fieldParams['fieldGroupPreAddonDropdownButtonListTitle'] as $key => $title) {
                    if ($title === 'divider') {
                        $this->content .=
                            '<div class="dropdown-divider"></div>';
                    } else {
                        $this->content .=
                            '<a class="dropdown-item text-uppercase" data-id="' . $key . '" ' . $this->fieldParams['fieldId'] . '-' . $key . '" href="#">' . $title . '</a>';
                    }
                }
            }

            $this->content .= '</div></div>';

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

            $this->fieldParams['fieldGroupPreAddonButtonHidden'] =
                isset($this->params['fieldGroupPreAddonButtonHidden']) &&
                $this->params['fieldGroupPreAddonButtonHidden'] === true ?
                'd-none' :
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
                    <button ' . $this->fieldParams['fieldId'] . '-' . $this->params['fieldGroupPreAddonButtonId'] . '" class="btn btn-'. $this->fieldParams['fieldGroupPreAddonButtonClass'] . ' rounded-0 ' . $this->fieldParams['fieldGroupPreAddonButtonHidden'] . '" type="button" data-toggle="tooltip" data-html="true" data-placement="' . $this->fieldParams['fieldGroupPreAddonButtonTooltipPosition']. '" title="' . $this->fieldParams['fieldGroupPreAddonButtonTooltipTitle'] . '" ' . $this->fieldParams['fieldGroupPreAddonButtonDisabled'] . '>' . $this->params['fieldGroupPreAddonButtonValue'] . '</button>
                </div>' ;
        }

        if (isset($this->params['fieldGroupPreAddonButtons'])) {

            $this->content .=
                '<div class="input-group-prepend">';

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

    protected function select2()
    {
        $this->content .=
            '<select ' . $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' class="form-control select2 select2-' . $this->fieldParams['fieldSelect2Type'] . '" data-dropdown-css-class="select2-' . $this->fieldParams['fieldSelect2Type'] . '" ' . $this->fieldParams['fieldId'] . '" ' . $this->fieldParams['fieldName'] . '" style="width:' . $this->width . ';" ' . $this->fieldParams['fieldDataSelect2Multiple'] . ' ' . $this->fieldParams['fieldDataSelect2Create'] . ' ' . $this->fieldParams['fieldDataSelect2MultipleObject'] . ' ' . $this->fieldParams['fieldDisabled'] . ' ' . $this->fieldParams['fieldDataAttributes'] . '>
                <option></option>';

        if ($this->fieldParams['fieldDataSelect2TreeData']) {
            $this->content .=
                $this->adminLTETags->useTag(
                        'tree',
                        [
                            'treeMode'      => 'select2',
                            'treeData'      => $this->fieldParams['fieldDataSelect2TreeData'],
                            'fieldParams'   => $this->fieldParams
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

            $this->fieldParams['fieldGroupPostAddonDropdownButtonHidden'] =
                isset($this->params['fieldGroupPostAddonDropdownButtonHidden']) &&
                $this->params['fieldGroupPostAddonDropdownButtonHidden'] === true ?
                'd-none' :
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
                    <button type="button" id="' . $this->fieldParams['fieldId'] . '-append-dropdown-button" class="btn btn-' . $this->fieldParams['fieldGroupPostAddonDropdownButtonClass'] . ' dropdown-toggle rounded-0 ' . $this->fieldParams['fieldGroupPostAddonDropdownButtonHidden'] . '" data-toggle="dropdown" aria-expanded="false" ' . $this->fieldParams['fieldGroupPostAddonDropdownButtonDisabled'] . '>
                        <span>' . $this->fieldParams['fieldGroupPostAddonDropdownButtonTitle'] . '</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">';

            if ($this->fieldParams['fieldGroupPostAddonDropdownButtonListTitle']) {
                foreach ($this->fieldParams['fieldGroupPostAddonDropdownButtonListTitle'] as $key => $title) {
                    if ($title === 'divider') {
                        $this->content .=
                            '<div class="dropdown-divider"></div>';
                    } else {
                        $this->content .=
                            '<a class="dropdown-item text-uppercase" data-id="' . $key . '" ' . $this->fieldParams['fieldId'] . '-' . $key . '" href="#">' . $title . '</a>';
                    }
                }
            }

            $this->content .= '</div></div>';

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

            $this->fieldParams['fieldGroupPostAddonButtonHidden'] =
                isset($this->params['fieldGroupPostAddonButtonHidden']) &&
                $this->params['fieldGroupPostAddonButtonHidden'] === true ?
                'd-none' :
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
                    <button ' . $this->fieldParams['fieldId'] . '-' . $this->params['fieldGroupPostAddonButtonId'] . '" class="btn btn-'. $this->fieldParams['fieldGroupPostAddonButtonClass'] . ' rounded-0 ' . $this->fieldParams['fieldGroupPostAddonButtonHidden'] . '" type="button" data-toggle="tooltip" data-html="true" data-placement="' . $this->fieldParams['fieldGroupPostAddonButtonTooltipPosition']. '" title="' . $this->fieldParams['fieldGroupPostAddonButtonTooltipTitle'] . '" ' . $this->fieldParams['fieldGroupPostAddonButtonDisabled'] . '>' . $this->params['fieldGroupPostAddonButtonValue'] . '</button>
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