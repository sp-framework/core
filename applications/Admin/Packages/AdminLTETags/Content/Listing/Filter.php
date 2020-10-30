<?php

namespace Applications\Admin\Packages\AdminLTETags\Content\Listing;

use Applications\Admin\Packages\AdminLTETags;

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
        $this->content =
            '<div id="' . $this->params['componentId'] .'-listing-filter-fields">
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
                                'fieldBazPostOnCreate'                => true,
                                'fieldBazPostOnUpdate'                => true,
                                'fieldDataSelect2Options'             => $this->params['dtColumns'],
                                'fieldDataSelect2OptionsArray'        => true
                            ]
                        ) .
                    '</div>
                    <div class="col">' .
                        $this->useTag('fields',
                            [
                                'componentId'                         => $this->params['componentId'],
                                'sectionId'                           => $this->params['sectionId'] . '-filter',
                                'fieldId'                             => 'operator',
                                'fieldLabel'                          => 'Operator',
                                'fieldType'                           => 'select2',
                                'fieldHelp'                           => true,
                                'fieldHelpTooltipContent'             => 'Select filter operator',
                                'fieldBazScan'                        => true,
                                'fieldBazPostOnCreate'                => true,
                                'fieldBazPostOnUpdate'                => true,
                                'fieldRequired'                       => true,
                                'fieldDataSelect2Options'             =>
                                [
                                    'equals' => 'Equals to',
                                    'lessthan' => 'Less than',
                                    'greaterthan' => 'Greater than',
                                    'lessthanequal' => 'Less than equals to',
                                    'greaterthanequal' => 'Greater than equals to',
                                    'like' => 'Like'
                                ],
                                'fieldDataSelect2OptionsArray'        => true
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
                                'fieldLabel'                          => 'Filter Value',
                                'fieldType'                           => 'input',
                                'fieldInputType'                      => 'text',
                                'fieldHelp'                           => true,
                                'fieldHelpTooltipContent'             => 'Filter value of the selected field',
                                'fieldRequired'                       => true,
                                'fieldBazScan'                        => true,
                                'fieldBazPostOnCreate'                => false,
                                'fieldBazPostOnUpdate'                => true,
                                'fieldBazJstreeSearch'                => false,
                                'fieldDataMinLength'                  => 1,
                                'fieldDataMaxLength'                  => 50
                            ]
                        ) .
                    '</div>
                </div>
                <div class="row">
                    <div class="col-md-4">' .
                        $this->useTag('fields',
                            [
                                'componentId'                         => $this->params['componentId'],
                                'sectionId'                           => $this->params['sectionId'] . '-filter',
                                'fieldId'                             => 'condition',
                                'fieldLabel'                          => 'Condition',
                                'fieldType'                           => 'select2',
                                'fieldHelp'                           => true,
                                'fieldHelpTooltipContent'             => 'Select filter operator',
                                'fieldBazScan'                        => true,
                                'fieldBazPostOnCreate'                => true,
                                'fieldBazPostOnUpdate'                => true,
                                'fieldRequired'                       => true,
                                'fieldDataSelect2Options'             =>
                                    [
                                        'and' => 'AND',
                                        'or' => 'OR'
                                    ],
                                'fieldDataSelect2OptionsArray'        => true
                            ]
                        ) .
                    '</div>
                    <div class="col-md-8">' .
                        $this->useTag('buttons',
                            [
                                'componentId'           => $this->params['componentId'],
                                'sectionId'             => $this->params['sectionId'] . '-filter',
                                'buttonType'            => 'datatable-buttons'
                            ]
                        ) .
                    '</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-9">
                        <div class="row vdivide">
                            <div class="col-md-6">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter',
                                        'fieldId'                             => 'saved-filters',
                                        'fieldLabel'                          => 'Saved Filters',
                                        'fieldType'                           => 'select2',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Select a saved filter and apply',
                                        'fieldBazScan'                        => true,
                                        'fieldBazPostOnCreate'                => true,
                                        'fieldBazPostOnUpdate'                => true,
                                        'fieldRequired'                       => true,
                                        'fieldDataSelect2Options'             => [],
                                        'fieldDataSelect2OptionsArray'        => true
                                    ]
                                ) .
                            '</div>
                            <div class="col-md-6">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter',
                                        'fieldId'                             => 'name',
                                        'fieldLabel'                          => 'Filter Name',
                                        'fieldType'                           => 'input',
                                        'fieldInputType'                      => 'text',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Filter name to save the above filters',
                                        'fieldRequired'                       => true,
                                        'fieldBazScan'                        => true,
                                        'fieldBazPostOnCreate'                => false,
                                        'fieldBazPostOnUpdate'                => true,
                                        'fieldBazJstreeSearch'                => false,
                                        'fieldDataMinLength'                  => 1,
                                        'fieldDataMaxLength'                  => 50
                                    ]
                                ) .
                            '</div>
                        </div>
                    </div>
                    <div class="col-md-3" style="margin-top:23px;">' .
                        $this->useTag('buttons',
                            [
                                'componentId'                         => $this->params['componentId'],
                                'sectionId'                           => $this->params['sectionId'] . '-filter',
                                'buttonType' => 'button',
                                'buttons' => [
                                    'save' => [
                                        'id' => 'save',
                                        'icon'=> 'save',
                                        'title' => 'Save filters',
                                    ],
                                    'reset' => [
                                        'id' => 'reset',
                                        'type' => 'danger',
                                        'icon' => 'times-circle-o',
                                        'title' => 'Reset filters',
                                    ],
                                    'apply' => [
                                        'id' => 'apply',
                                        'title' => 'Apply filters',
                                    ]
                                ]
                            ]
                        ) .
                    '</div>
                </div>
            </div>' .
            $this->inclJs();
    }

    protected function inclJs()
    {
        return
            '<script type="text/javascript">' .
                'if (!window["dataCollection"]["' . $this->params["componentId"] . '"]) {
                    window["dataCollection"]["' . $this->params["componentId"] . '"] = { };
                }
                window["dataCollection"]["' . $this->params["componentId"] . '"]["' . $this->params["componentId"] . '-listing-filter"] =
                    $.extend(
                        window["dataCollection"]["' . $this->params["componentId"] . '"]["' . $this->params["componentId"] . '-listing-filter"],
                        {
                            "' . $this->params["componentId"] . '-listing-filter-columns" : {
                            placeholder: "SELECT COLUMN TO FILTER",
                            },
                            "' . $this->params["componentId"] . '-listing-filter-operator" : {
                                placeholder: "SELECT FILTER OPERATOR",
                            },
                            "' . $this->params["componentId"] . '-listing-filter-value" : {
                                placeholder: "FILTER VALUE",
                            },
                            "' . $this->params["componentId"] . '-listing-filter-condition" : {
                                placeholder: "FILTER CONDITION",
                            },
                            "' . $this->params["componentId"] . '-listing-filter-saved-filters" : {
                                placeholder: "SELECT SAVED FILTERS AND APPLY",
                            },
                            "' . $this->params["componentId"] . '-listing-filter-name" : {
                                placeholder: "FILTER NAME",
                            },
                            "' . $this->params["componentId"] . '-listing-filter-form" : {
                                rules: {
                                    "' . $this->params["componentId"] . '-listing-filter-columns" : "required",
                                },
                                messages: {
                                    "' . $this->params["componentId"] . '-listing-filter-columns" : "Please select a column",
                                }
                            },
                            "' . $this->params["componentId"] . '-listing-filter-datatable" : {
                                "dataTables" : ["' . $this->params["componentId"] . '-listing-filter-table"]
                            }
                        }
                    );
            </script>';
    }
}