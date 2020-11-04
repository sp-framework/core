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
                                'apply' => [
                                    'title'                   => 'Apply',
                                    'noMargin'                => true,
                                    'disabled'                => true,
                                    'tooltipTitle'            => 'Apply Selected Filter'
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
                                    'buttonAdditionalClass'   => 'rounded-0',
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
                <div class="row vdivide">
                    <div class="col">
                        <div class="row">
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter',
                                        'fieldId'                             => 'columns',
                                        'fieldLabel'                          => 'Columns',
                                        'fieldType'                           => 'select2',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Select the column to filter',
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
                                        'fieldType'                           => 'input',
                                        'fieldInputType'                      => 'select',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Select filter operator. Check NOT for reverse operation.',
                                        'fieldBazScan'                        => true,
                                        'fieldRequired'                       => true,
                                        'fieldGroupPreAddonText'              => '<span class="ml-2 mr-1">NOT</span>' . $this->useTag('fields',
                                                [
                                                    'componentId'                         => $this->params['componentId'],
                                                    'sectionId'                           => $this->params['sectionId'] . '-filter',
                                                    'fieldId'                             => 'not-operator',
                                                    'fieldLabel'                          => false,
                                                    'fieldType'                           => 'checkbox',
                                                    'fieldAdditionalClass'                => 'm-0 ml-1',
                                                    'fieldInputType'                      => 'text',
                                                    'fieldHelp'                           => true,
                                                    'fieldHelpTooltipContent'             => 'Not Operator',
                                                    'fieldRequired'                       => false,
                                                    'fieldBazScan'                        => true,
                                                    'fieldCheckboxType'                   => 'warning',
                                                    'fieldCheckboxInline'                 => true,
                                                    'fieldCheckboxAdditionClass'          => 'mb-0'
                                                ]
                                        ),
                                        'fieldGroupPreAddonTextAdditionalClass' => 'p-0 ml-1',
                                        'fieldDataSelectOptions'                =>
                                            [
                                                'equals'            =>
                                                    [
                                                        'id'            => 'equals',
                                                        'name'          => 'Equals to'
                                                    ],
                                                'lessthan'          =>
                                                    [
                                                        'id'            => 'lessthan',
                                                        'name'          => 'Less than'
                                                    ],
                                                'greaterthan'       =>
                                                    [
                                                        'id'            => 'greaterthan',
                                                        'name'          => 'Greater than'
                                                    ],
                                                'lessthanequal'     =>
                                                    [
                                                        'id'            => 'lessthanequal',
                                                        'name'          => 'Less than equals to'
                                                    ],
                                                'greaterthanequal'  =>
                                                    [
                                                        'id'            => 'greaterthanequal',
                                                        'name'          => 'Greater than equals to'
                                                    ],
                                                'like'              =>
                                                    [
                                                        'id'            => 'like',
                                                        'name'          => 'Like'
                                                    ],
                                                'between'              =>
                                                    [
                                                        'id'            => 'between',
                                                        'name'          => 'Between'
                                                    ]
                                            ],
                                        'fieldDataSelectOptionsKey'           => 'id',
                                        'fieldDataSelectOptionsValue'         => 'name',
                                        'fieldDataSelectOptionsArray'         => true,
                                        'fieldDataSelectOptionsZero'          => 'SELECT OPERATOR'
                                    ]
                                ) .
                            '</div>
                        </div>
                        <div class="row" id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value-text-row">
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter',
                                        'fieldId'                             => 'value-text',
                                        'fieldLabel'                          => 'Filter Value(s)',
                                        'fieldType'                           => 'input',
                                        'fieldInputType'                      => 'text',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             =>
                                            'Filter values of the selected field. Separate by comma for OR Operation. Ex: user1,user2 - will generate query that will filter user = user1 OR user = user2',
                                        'fieldRequired'                       => true,
                                        'fieldBazScan'                        => true,
                                        'fieldBazJstreeSearch'                => false,
                                        'fieldDataInputMinLength'             => 1,
                                        'fieldDataInputMaxLength'             => 50
                                    ]
                                ) .
                            '</div>
                        </div>
                        <div class="row" id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value-number-row">
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter',
                                        'fieldId'                             => 'value-number',
                                        'fieldLabel'                          => 'Filter Numeric Value(s)',
                                        'fieldType'                           => 'input',
                                        'fieldInputType'                      => 'number',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             =>
                                            'Filter numeric values of the selected field. Separate by comma for OR Operation. Ex: 10,20 - will generate query that will filter price = 10 OR price = 20',
                                        'fieldRequired'                       => true,
                                        'fieldBazScan'                        => true,
                                        'fieldBazJstreeSearch'                => false,
                                        'fieldDataInputMinNumber'             => -9999999,
                                        'fieldDataInputMaxNumber'             => 9999999
                                    ]
                                ) .
                            '</div>
                        </div>
                        <div class="row" id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value-between-row">
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter',
                                        'fieldId'                             => 'value-between-start',
                                        'fieldLabel'                          => 'Between Start Value',
                                        'fieldType'                           => 'input',
                                        'fieldInputType'                      => 'text',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Filter value between start of the selected field',
                                        'fieldRequired'                       => true,
                                        'fieldBazScan'                        => true,
                                        'fieldBazJstreeSearch'                => false,
                                        'fieldDataMinLength'                  => 1,
                                        'fieldDataMaxLength'                  => 50
                                    ]
                                ) .
                            '</div>
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter',
                                        'fieldId'                             => 'value-between-end',
                                        'fieldLabel'                          => 'Between End Value',
                                        'fieldType'                           => 'input',
                                        'fieldInputType'                      => 'text',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Filter value between end of the selected field',
                                        'fieldRequired'                       => true,
                                        'fieldBazScan'                        => true,
                                        'fieldBazJstreeSearch'                => false,
                                        'fieldDataMinLength'                  => 1,
                                        'fieldDataMaxLength'                  => 50
                                    ]
                                ) .
                            '</div>
                        </div>
                        <div class="row">
                            <div class="col text-center">' .
                                $this->useTag('buttons',
                                    [
                                        'componentId'           => $this->params['componentId'],
                                        'sectionId'             => $this->params['sectionId'] . '-filter',
                                        'buttonType'            => 'button',
                                        'buttonLabel'           => false,
                                        'buttons'               =>
                                            [
                                                'assign'        =>
                                                [
                                                    'id'            => 'assign',
                                                    'icon'          => 'arrow-right',
                                                    'title'         => 'Assign'
                                                ]
                                            ]
                                    ]
                                ) .
                            '</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row" style="max-height:261px; min-height:261px; overflow:scroll;">
                            <div class="col">
                                <div class="table table-sm">
                                    <table class="table table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Rule</th>
                                                <th>Condition</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>email:like:"guru@bazaari.com.au"</td>
                                                <td>and</td>
                                                <td>
                                                    <button class="btn btn-danger btn-xs">
                                                        <i class="fa fas fa-trash fa-xs"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>can_login:is:"1"</td>
                                                <td></td>
                                                <td>
                                                    <button class="btn btn-danger btn-xs">
                                                        <i class="fa fas fa-trash fa-xs"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
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
                                            'noMargin'                => true,
                                            'disabled'                => true,
                                            'icon'                    => 'save',
                                            'tooltipTitle'            => 'Save Filter'
                                        ],
                                        'saveapply'   => [
                                            'title'                   => 'Save & Apply',
                                            'type'                    => 'secondary',
                                            'tooltipTitle'            => 'Save Filter and Apply',
                                            'noMargin'                => true,
                                            'disabled'                => true,
                                            'buttonAdditionalClass'   => 'rounded-0',
                                            'position'                => 'right'
                                        ]
                                    ]
                            ]
                        ) .
                    '</div>
                    <div class="col">' .
                        $this->useTag('buttons',
                            [
                                'componentId'            => $this->params['componentId'],
                                'sectionId'              => $this->params['sectionId'] . '-filter',
                                'buttonLabel'            => false,
                                'buttonType'             => 'button',
                                'buttons'                =>
                                [
                                    'apply'              =>
                                    [
                                        'id'                 => 'apply',
                                        'title'              => 'Apply filters',
                                        'position'           => 'right'
                                    ]
                                ]
                            ]
                        ) .
                    '</div>
                </div>
            </section>';

        return $this->useTag('modal',
            [
                'modalId'           => $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-modal',
                'modalBodyContent'  => $modalContent,
                'modalSize'         => 'xl',
                'modalHeader'       => true,
                'modalTitle'        => '<i class="fa fas fa-fw fa-filter"></i> ' . strtoupper($this->params['componentName'] . ' Filter'),
                'modalEscClose'     => 'true'
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
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-columns" : {
                            placeholder: "SELECT COLUMN TO FILTER",
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-not-operator" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-operator" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value-text" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value-number" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value-between-start" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-value-between-end" : {
                            },
                            "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-filter-name" : {
                            }
                        }
                    );
            </script>';
    }
}