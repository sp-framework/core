<?php

namespace Applications\Ecom\Dashboard\Packages\AdminLTETags\Tags\Content\Listing;

use Applications\Ecom\Dashboard\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Arr;

class Filters extends AdminLTETags
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
        $defaultFilter = null;

        foreach ($this->params['dtFilters'] as $filterKey => $filter) {
            if ($filter['is_default'] === '1') {
                $defaultFilter = $filterKey;
                $this->params['dtFilters'][$filterKey]['name'] =
                    $this->params['dtFilters'][$filterKey]['name'] . ' (Default)';
            }

            if ($filter['permission'] === '0') {
                if (!$defaultFilter) {
                     $defaultFilter = $filterKey;
                }
                $this->params['dtFilters'][$filterKey]['name'] =
                    $this->params['dtFilters'][$filterKey]['name'] . ' (System)';
            } else if ($filter['permission'] === '2') {
                $this->params['dtFilters'][$filterKey]['name'] =
                    $this->params['dtFilters'][$filterKey]['name'] . ' (Shared)';
            }
            if ($filter['shared_ids']) {
                $this->params['dtFilters'][$filterKey]['name'] =
                    $this->params['dtFilters'][$filterKey]['name'] . ' (Sharing)';
            }
        }

        if (!$defaultFilter) {
            $defaultFilter = Arr::firstKey($this->params['dtFilters']);
        }

        $this->content .=
            '<div class="col" id="listing-filters" hidden>
                <form autocomplete="off">' .
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
                                    'edit'   => [
                                        'title'                   => false,
                                        'type'                    => 'warning',
                                        'icon'                    => 'edit',
                                        'noMargin'                => true,
                                        'disabled'                => true,
                                        'buttonAdditionalClass'   => 'rounded-0 text-white',
                                        'position'                => 'right'
                                    ],
                                    'delete'   => [
                                        'title'                   => false,
                                        'type'                    => 'danger',
                                        'icon'                    => 'trash',
                                        'noMargin'                => true,
                                        'disabled'                => true,
                                        'buttonAdditionalClass'   => 'rounded-0',
                                        'position'                => 'right'
                                    ],
                                    'share'   => [
                                        'title'                   => false,
                                        'type'                    => 'primary',
                                        'tooltipTitle'            => 'Share Selected Saved Filter',
                                        'icon'                    => 'share-alt',
                                        'noMargin'                => true,
                                        'disabled'                => true,
                                        'buttonAdditionalClass'   => 'rounded-0',
                                        'position'                => 'right'
                                    ],
                                    'add'   => [
                                        'title'                   => false,
                                        'type'                    => 'success',
                                        'icon'                    => 'plus',
                                        'noMargin'                => true,
                                        'buttonAdditionalClass'   => 'rounded-0 ml-1',
                                        'position'                => 'right'
                                    ],
                                    'clone'   => [
                                        'title'                   => false,
                                        'type'                    => 'info',
                                        'icon'                    => 'copy',
                                        'noMargin'                => true,
                                        'buttonAdditionalClass'   => 'rounded-0 text-white',
                                        'position'                => 'right'
                                    ],
                                    'reset' => [
                                        'title'                   => false,
                                        'noMargin'                => true,
                                        'icon'                    => 'undo',
                                        'type'                    => 'primary',
                                        'buttonAdditionalClass'   => 'rounded-0 ml-1',
                                        'tooltipTitle'            => 'Reset Filters',
                                    ]
                                ],
                            'fieldInputType'                      => 'select',
                            'fieldHelp'                           => false,
                            'fieldDataSelectOptions'              => $this->params['dtFilters'],
                            'fieldDataSelectOptionsArray'         => true,
                            'fieldDataSelectOptionsKey'           => 'id',
                            'fieldDataSelectOptionsValue'         => 'name',
                            'fieldDataSelectOptionsSelected'      => $defaultFilter
                        ]
                    ) .
                '</form>
            </div>';

            $this->content .= $this->getFilterModalContent();

            $this->content .= $this->getShareModalContent();

            $this->content .= $this->inclJs();

    }

    protected function getFilterModalContent()
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
                                                'fieldDisabled'                       => true,
                                                'fieldDataAttributes'                 =>
                                                    [
                                                        'disabledtext'      => '-'
                                                    ],
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
                                        'fieldId'                               => 'id',
                                        'fieldLabel'                            => 'Filter Id',
                                        'fieldType'                             => 'input',
                                        'fieldInputType'                        => 'text',
                                        'fieldPlaceholder'                      => 'Filter Id',
                                        'fieldDisabled'                         => true,
                                        'fieldHidden'                           => true,
                                        'fieldBazScan'                          => true,
                                    ]
                                ) .
                                $this->useTag('fields',
                                    [
                                        'componentId'                           => $this->params['componentId'],
                                        'sectionId'                             => $this->params['sectionId'] . '-filter',
                                        'fieldId'                               => 'name',
                                        'fieldLabel'                            => 'Filter Name',
                                        'fieldType'                             => 'input',
                                        'fieldPlaceholder'                      => 'Enter Filter Name',
                                        'fieldInputType'                        => 'text',
                                        'fieldHelp'                             => true,
                                        'fieldDisabled'                         => true,
                                        'fieldHelpTooltipContent'               => 'Filter name to save the above filters',
                                        'fieldBazScan'                          => true,
                                        'fieldBazPostOnCreate'                  => false,
                                        'fieldBazPostOnUpdate'                  => true,
                                        'fieldDataInputMinLength'               => 1,
                                        'fieldDataInputMaxLength'               => 50,
                                    ]
                                ) .
                            '</div>
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                           => $this->params['componentId'],
                                        'sectionId'                             => $this->params['sectionId'] . '-filter',
                                        'fieldId'                               => 'default',
                                        'fieldLabel'                            => 'Default?',
                                        'fieldType'                             => 'checkbox',
                                        'fieldCheckboxType'                     => 'info',
                                        'fieldHelp'                             => true,
                                        'fieldHelpTooltipContent'               => 'Make this filter as default filter<br>Note: Only saved filters can be made default.',
                                        'fieldBazScan'                          => true,
                                    ]
                                ) .
                            '</div>
                        </div>
                    </fieldset>
                </form>
            </section>';

        return $this->useTag('modal',
            [
                'componentId'               => $this->params['componentId'],
                'sectionId'                 => $this->params['sectionId'],
                'modalId'                   => 'filter-modal',
                'modalBodyContent'          => $modalContent,
                'modalSize'                 => 'xl',
                'modalHeader'               => true,
                'modalFooter'               => true,
                'modalTitle'                => '<i class="fa fas fa-fw fa-filter"></i> ' . strtoupper($this->params['componentName'] . ' Filter'),
                'modalEscClose'             => 'false',
                'modalFooterButtons'        =>
                    [
                        'componentId'                       => $this->params['componentId'],
                        'sectionId'                         => $this->params['sectionId'] . '-filter',
                        'buttonType'                        => 'button',
                        'buttonSize'                        => 'sm',
                        'buttons'                           =>
                            [
                                'save-add' => [
                                    'title'                 => 'Add',
                                    'disabled'              => true,
                                    'icon'                  => 'save',
                                    'url'                   => $this->links->url('filters/add')
                                ],
                                'save-update' => [
                                    'title'                 => 'Update',
                                    'disabled'              => true,
                                    'hidden'                => true,
                                    'icon'                  => 'save',
                                    'url'                   => $this->links->url('filters/update')
                                ],
                            ]
                    ]
            ]
        );
    }

    protected function getShareModalContent()
    {
        $modalContent =
            '<section id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-sharing">
                <form autocomplete="off" class="mt-1" data-validateon="section" id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-sharing-form">
                    <fieldset id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-sharing-fieldset">
                        <div class="row">
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter-sharing',
                                        'fieldId'                             => 'gid',
                                        'fieldLabel'                          => 'Role(s)',
                                        'fieldType'                           => 'select2',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Select Roles to share filter with',
                                        'fieldBazScan'                        => true,
                                        'fieldRequired'                       => false,
                                        'fieldDataSelect2Options'             => $this->roles->roles,
                                        'fieldDataSelect2Multiple'            => true,
                                        'fieldDataSelect2OptionsKey'          => 'id',
                                        'fieldDataSelect2OptionsValue'        => 'name',
                                        'fieldDataSelect2OptionsArray'        => true,
                                    ]
                                ) .
                            '</div>
                        </div>
                        <div class="row">
                            <div class="col">' . //This will be changed to user profiles
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter-sharing',
                                        'fieldId'                             => 'uid',
                                        'fieldLabel'                          => 'Account(s)',
                                        'fieldType'                           => 'select2',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Select Accounts to share filter with',
                                        'fieldBazScan'                        => true,
                                        'fieldRequired'                       => false,
                                        'fieldDataSelect2Options'             => [],
                                        'fieldDataSelect2Multiple'            => true,
                                        'fieldDataSelect2OptionsKey'          => 'id',
                                        'fieldDataSelect2OptionsValue'        => 'name',
                                        'fieldDataSelect2OptionsArray'        => true,
                                    ]
                                ) .
                            '</div>
                        </div>
                    </fieldset>
                </form>
            </section>';

        return $this->useTag('modal',
            [
                'componentId'               => $this->params['componentId'],
                'sectionId'                 => $this->params['sectionId'],
                'modalId'                   => 'filter-sharing-modal',
                'modalBodyContent'          => $modalContent,
                'modalSize'                 => 'lg',
                'modalHeader'               => true,
                'modalFooter'               => true,
                'modalTitle'                => '<i class="fa fas fa-fw fa-share-alt"></i> ' . strtoupper($this->params['componentName'] . ' Filter share'),
                'modalEscClose'             => 'true'
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
                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-sharing"] =
                    $.extend(
                        window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-sharing"],
                        {
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-sharing-gid" : {
                                placeholder: "Select Role(s)",
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-sharing-uid" : {
                                placeholder: "Select Account(s)",
                            },
                        }
                    );
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
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-id" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-name" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-default" : {
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
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-field" : "required",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-operator" : "required",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value" : "required"
                                },
                                messages: {
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-field" : "Please select a field",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-operator" : "Please select an operator",
                                    "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value" : "Please enter value. Numeric Fields only accept numbers or comma or decimal point"
                                }
                            }
                        }
                    );
            </script>';
    }
}
                    //DropdownSplitButtons - Dropdown
                    // [
                    //     'componentId'                       => $this->params['componentId'],
                    //     'sectionId'                         => $this->params['sectionId'],
                    //     'buttonType'                        => 'dropdownSplitButtons',
                    //     'buttonSize'                        => 'sm',
                    //     'dropdownButtonTitle'               => 'Actions',
                    //     'dropdownSplitButtonsSplit'         => true,
                    //     'dropdownButtonId'                  => 'modal-actions',
                    //     'dropdownDirection'                 => 'up',
                    //     'dropdownAlign'                     => 'right',
                    //     'buttons'                           =>
                    //         [
                    //             'save' => [
                    //                 'title'                   => 'Save',
                    //                 'disabled'                => true,
                    //                 'icon'                    => 'save'
                    //             ],
                    //             'apply-new' => [
                    //                 'title'                   => 'Apply',
                    //                 'disabled'                => true,
                    //                 'icon'                    => 'filter',
                    //             ],
                    //             'saveapply'   => [
                    //                 'title'                   => 'Save & Apply',
                    //                 'disabled'                => true,
                    //             ]
                    //         ]
                    // ]