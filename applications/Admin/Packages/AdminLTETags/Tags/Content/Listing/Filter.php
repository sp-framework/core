<?php

namespace Applications\Admin\Packages\AdminLTETags\Tags\Content\Listing;

use Applications\Admin\Packages\AdminLTETags\AdminLTETags;

class Filter extends AdminLTETags
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
        $this->content .=
            '<div class="col">' .
                $this->useTag(
                    'fields',
                    [
                        'componentId'                         => $this->params['componentId'],
                        'sectionId'                           => $this->params['sectionId'] . '-filter',
                        'fieldId'                             => 'filters',
                        'fieldLabel'                          => false,
                        'fieldType'                           => 'input',
                        'fieldAdditionalClass'                => 'mb-1',
                        'fieldGroupPreAddonIcon'              => 'filter',
                        'fieldGroupPostAddonButtons'          =>
                            [
                                'apply-saved' => [
                                    'title'                   => 'Apply',
                                    'noMargin'                => true,
                                    'disabled'                => true
                                ],
                                'reset' => [
                                    'title'                   => false,
                                    'noMargin'                => true,
                                    'icon'                    => 'undo',
                                    'type'                    => 'secondary',
                                    'tooltipTitle'            => 'Reset Filters',
                                ],
                                'add'   => [
                                    'title'                   => false,
                                    'type'                    => 'success',
                                    'tooltipTitle'            => 'Add New Filter',
                                    'icon'                    => 'plus',
                                    'noMargin'                => true,
                                    'buttonAdditionalClass'   => 'rounded-0',
                                    'position'                => 'right'
                                ],
                                'edit'   => [
                                    'title'                   => false,
                                    'type'                    => 'warning',
                                    'tooltipTitle'            => 'Edit Selected Filter',
                                    'icon'                    => 'edit',
                                    'noMargin'                => true,
                                    'disabled'                => true,
                                    'buttonAdditionalClass'   => 'rounded-0 text-white',
                                    'position'                => 'right'
                                ],
                                'delete'   => [
                                    'title'                   => false,
                                    'type'                    => 'danger',
                                    'tooltipTitle'            => 'Delete Selected Filter',
                                    'icon'                    => 'trash',
                                    'noMargin'                => true,
                                    'disabled'                => true,
                                    'buttonAdditionalClass'   => 'rounded-0',
                                    'position'                => 'right'
                                ],
                                'share'   => [
                                    'title'                   => false,
                                    'type'                    => 'primary',
                                    'tooltipTitle'            => 'Share Selected Filter',
                                    'icon'                    => 'share-alt',
                                    'noMargin'                => true,
                                    'disabled'                => true,
                                    'buttonAdditionalClass'   => 'rounded-0',
                                    'position'                => 'right'
                                ]

                            ],
                        'fieldInputType'                      => 'select',
                        'fieldHelp'                           => false,
                        'fieldDataSelectOptionsZero'          => 'Select Filter',
                        'fieldDataSelectOptions'              => $this->params['dtFilters'],
                        'fieldDataSelectOptionsArray'         => true,
                        'fieldDataSelectOptionsKey'           => 'id',
                        'fieldDataSelectOptionsValue'         => 'name'
                    ]
                ) .
            '</div>';

            $this->content .= $this->getModalContent();

            $this->content .= $this->inclJs();

    }

    protected function getModalContent()
    {
        $this->fieldParams['dtFilterColumns'] =
            isset($this->params['dtFilterColumns']) ?
            $this->params['dtFilterColumns'] :
            $this->params['dtColumns'];

        $modalContent =
            '<section id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter" class="sectionWithListingFilter">
                <form autocomplete="off" class="mt-1" data-validateon="section" id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-form">
                    <fieldset id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-fieldset">
                        <div class="row vdivide">
                            <div class="col">
                                <div class="row">
                                    <div class="col">' .
                                        $this->useTag('fields',
                                            [
                                                'componentId'                         => $this->params['componentId'],
                                                'sectionId'                           => $this->params['sectionId'] . '-filter',
                                                'fieldId'                             => 'andor',
                                                'fieldLabel'                          => 'And/Or',
                                                'fieldType'                           => 'select2',
                                                'fieldHelp'                           => true,
                                                'fieldHelpTooltipContent'             => 'Select And/Or<br>Not included in first/only filter.',
                                                'fieldBazScan'                        => true,
                                                'fieldRequired'                       => false,
                                                'fieldDataSelect2Options'             =>
                                                    [
                                                        'and'            =>
                                                            [
                                                                'id'            => 'and',
                                                                'name'          => 'And'
                                                            ],
                                                        'or'            =>
                                                            [
                                                                'id'            => 'or',
                                                                'name'          => 'Or'
                                                            ]
                                                    ],
                                                'fieldDataSelect2OptionsKey'           => 'id',
                                                'fieldDataSelect2OptionsValue'         => 'name',
                                                'fieldDataSelect2OptionsArray'         => true,
                                                'fieldDataSelect2OptionsSelected'      => 'and',
                                            ]
                                        ) .
                                    '</div>
                                </div>
                                <div class="row">
                                    <div class="col">' .
                                        $this->useTag('fields',
                                            [
                                                'componentId'                         => $this->params['componentId'],
                                                'sectionId'                           => $this->params['sectionId'] . '-filter',
                                                'fieldId'                             => 'field',
                                                'fieldLabel'                          => 'Field',
                                                'fieldType'                           => 'select2',
                                                'fieldHelp'                           => true,
                                                'fieldHelpTooltipContent'             => 'Select the field to filter',
                                                'fieldRequired'                       => true,
                                                'fieldBazScan'                        => true,
                                                'fieldDataSelect2Options'             => $this->fieldParams['dtFilterColumns'],
                                                'fieldDataSelect2OptionsArray'        => true,
                                                'fieldDataSelect2OptionsKey'          => 'id',
                                                'fieldDataSelect2OptionsValue'        => 'name'
                                            ]
                                        ) .
                                    '</div>
                                </div>
                                <div class="row">
                                    <div class="col">' .
                                        $this->useTag('fields',
                                            [
                                                'componentId'                         => $this->params['componentId'],
                                                'sectionId'                           => $this->params['sectionId'] . '-filter',
                                                'fieldId'                             => 'operator',
                                                'fieldLabel'                          => 'Operator',
                                                'fieldType'                           => 'select2',
                                                'fieldHelp'                           => true,
                                                'fieldHelpTooltipContent'             => 'Select filter operator.',
                                                'fieldBazScan'                        => true,
                                                'fieldRequired'                       => true,
                                                'fieldDataSelect2Options'             =>
                                                    [
                                                        'equals'            =>
                                                            [
                                                                'id'            => 'equals',
                                                                'name'          => 'Equals to'
                                                            ],
                                                        'notequals'            =>
                                                            [
                                                                'id'            => 'notequals',
                                                                'name'          => 'Not equals to'
                                                            ],
                                                        'lessthan'          =>
                                                            [
                                                                'id'            => 'lessthan',
                                                                'name'          => 'Less than'
                                                            ],
                                                        'lessthanequals'     =>
                                                            [
                                                                'id'            => 'lessthanequals',
                                                                'name'          => 'Less than equals to'
                                                            ],
                                                        'greaterthan'       =>
                                                            [
                                                                'id'            => 'greaterthan',
                                                                'name'          => 'Greater than'
                                                            ],
                                                        'greaterthanequals'  =>
                                                            [
                                                                'id'            => 'greaterthanequals',
                                                                'name'          => 'Greater than equals to'
                                                            ],
                                                        'like'              =>
                                                            [
                                                                'id'            => 'like',
                                                                'name'          => 'Like'
                                                            ],
                                                        'notlike'           =>
                                                            [
                                                                'id'            => 'notlike',
                                                                'name'          => 'Not Like'
                                                            ],
                                                        'between'              =>
                                                            [
                                                                'id'            => 'between',
                                                                'name'          => 'Is Between'
                                                            ],
                                                        'notbetween'        =>
                                                            [
                                                                'id'            => 'notbetween',
                                                                'name'          => 'Is not between'
                                                            ],
                                                        'empty'             =>
                                                            [
                                                                'id'            => 'empty',
                                                                'name'          => 'Is Empty'
                                                            ],
                                                        'notempty'          =>
                                                            [
                                                                'id'            => 'notempty',
                                                                'name'          => 'Is not empty'
                                                            ],
                                                    ],
                                                'fieldDataSelect2OptionsKey'           => 'id',
                                                'fieldDataSelect2OptionsValue'         => 'name',
                                                'fieldDataSelect2OptionsArray'         => true
                                            ]
                                        ) .
                                    '</div>
                                </div>
                                <div class="row">
                                    <div class="col">' .
                                        $this->useTag('fields',
                                            [
                                                'componentId'                         => $this->params['componentId'],
                                                'sectionId'                           => $this->params['sectionId'] . '-filter',
                                                'fieldId'                             => 'value',
                                                'fieldLabel'                          => 'Filter Value(s)',
                                                'fieldType'                           => 'input',
                                                'fieldInputType'                      => 'text',
                                                'fieldHelp'                           => true,
                                                'fieldHelpTooltipContent'             =>
                                                    'Filter values of the selected field. Separate by comma for OR Operation and AND Operation (for BETWEEN Operator).<br> Ex: user1,user2 - will generate query that will filter user = user1 OR user = user2',
                                                'fieldRequired'                       => true,
                                                'fieldBazScan'                        => true,
                                                'fieldBazJstreeSearch'                => false,
                                                'fieldDataInputMinLength'             => 1,
                                                'fieldDataInputMaxLength'             => 100
                                            ]
                                        ) .
                                    '</div>
                                </div>
                                <div class="row">
                                    <div class="col">' .
                                        $this->useTag('buttons',
                                            [
                                                'componentId'            => $this->params['componentId'],
                                                'sectionId'              => $this->params['sectionId'] . '-filter',
                                                'buttonLabel'            => false,
                                                'buttonType'             => 'datatableButtons'
                                            ]
                                        ) .
                                    '</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div data-validateOn="section" id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-table"></div>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-fieldset-save-apply">
                        <div class="row">
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                           => $this->params['componentId'],
                                        'sectionId'                             => $this->params['sectionId'] . '-filter',
                                        'fieldId'                               => 'name',
                                        'fieldLabel'                            => 'Save Filter',
                                        'fieldType'                             => 'input',
                                        'fieldInputType'                        => 'text',
                                        'fieldHelp'                             => true,
                                        'fieldDisabled'                         => true,
                                        'fieldHelpTooltipContent'               => 'Filter name to save the above filters',
                                        'fieldBazScan'                          => true,
                                        'fieldBazPostOnCreate'                  => false,
                                        'fieldBazPostOnUpdate'                  => true,
                                        'fieldDataInputMinLength'               => 1,
                                        'fieldDataInputMaxLength'               => 50,
                                        'fieldGroupPostAddonButtons'            =>
                                            [
                                                'save' => [
                                                    'title'                   => 'Save',
                                                    'disabled'                => true,
                                                    'icon'                    => 'save'
                                                ],
                                                'saveapply'   => [
                                                    'title'                   => 'Save & Apply',
                                                    'type'                    => 'secondary',
                                                    'noMargin'                => true,
                                                    'disabled'                => true,
                                                    'buttonAdditionalClass'   => 'rounded-0',
                                                    'position'                => 'right'
                                                ],
                                                'apply-new' => [
                                                    'title'                   => 'Apply',
                                                    'disabled'                => true,
                                                    'icon'                    => 'filter',
                                                ],
                                                'cancel'   => [
                                                    'title'                   => 'Cancel',
                                                    'type'                    => 'secondary',
                                                    'buttonAdditionalClass'   => 'rounded-0',
                                                    'position'                => 'right'
                                                ]
                                            ]
                                    ]
                                ) .
                            '</div>
                        </div>
                    </fieldset>
                </form>
            </section>';

        return $this->useTag('modal',
            [
                'modalId'           => $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-modal',
                'modalBodyContent'  => $modalContent,
                'modalSize'         => 'xl',
                'modalHeader'       => true,
                'modalTitle'        => '<i class="fa fas fa-fw fa-filter"></i> ' . strtoupper($this->params['componentName'] . ' Filter'),
                'modalEscClose'     => 'false'
            ]
        );
    }

    protected function inclJs()
    {
        return
            '<script type="text/javascript">' .
                'if (!window["dataCollection"]["' . $this->params['componentId'] . '"]) {
                    window["dataCollection"]["' . $this->params['componentId'] . '"] = { };
                }
                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter"] =
                    $.extend(
                        window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter"],
                        {
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-andor" : {
                                placeholder: "SELECT AND/OR",
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-field" : {
                            placeholder: "SELECT FIELD TO FILTER",
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-operator" : {
                            placeholder: "SELECT FILTER OPERATOR",
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-name" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-datatables" : [
                                "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-table"
                            ],
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-table" : {
                                "tableTitle"        : "Filters",
                                "datatable"         : {
                                    "responsive"        : true,
                                    "rowReorder"        : false,
                                    "searching"         : false,
                                    "paging"            : false,
                                    "ordering"          : false
                                },
                                "bazdatatable"      : {
                                    "rowButtons"        : {
                                        "canEdit"           : true,
                                        "canDelete"         : true,
                                    },
                                    "compareData"       : "rows",
                                    "keepFieldsData"    : ["' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-andor"]
                                },
                                "postExtraction": function(datatable, extractedData) {
                                    "use strict";

                                    var fieldData = extractedData[0][1]["extractedData"];

                                    extractedData[0][1]["extractedData"] = fieldData.replace(" (Numeric)", "");
                                }
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-form" : {
                                "rules"     : {
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-andor" : "required",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-field" : "required",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-operator" : "required",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value" : "required"
                                },
                                messages: {
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-field" : "Please select a field",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-field" : "Please select either And/Or",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-operator" : "Please select an operator",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value" : "Please enter value. Numeric Fields only accept numbers or comma or decimal point"
                                }
                            }
                        }
                    );
            </script>';
    }
}