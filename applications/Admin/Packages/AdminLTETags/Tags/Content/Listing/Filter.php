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
        $this->content =
            '<div id="' . $this->params['componentId'] .'-listing-filter-fields" class="row vdivide">
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
                                    'fieldBazPostOnCreate'                => true,
                                    'fieldBazPostOnUpdate'                => true,
                                    'fieldDataSelect2Options'             => $this->params['dtColumns'],
                                    'fieldDataSelect2OptionsArray'        => true,
                                    'fieldDataSelect2OptionsKey'          => 'id',
                                    'fieldDataSelect2OptionsValue'        => 'name'
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
                                                ]
                                        ],
                                    'fieldDataSelect2OptionsKey'          => 'id',
                                    'fieldDataSelect2OptionsValue'        => 'name',
                                    'fieldDataSelect2OptionsArray'        => true,
                                    'fieldDataSelect2OptionsSelected'     => 'like'
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
                        <div class="col">' .
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
                                            'and'   =>
                                                [
                                                    'id'    => 'and',
                                                    'name'  => 'AND'
                                                ],
                                            'or'    =>
                                                [
                                                    'id'    => 'or',
                                                    'name'  => 'OR'
                                                ]
                                        ],
                                    'fieldDataSelect2OptionsKey'          => 'id',
                                    'fieldDataSelect2OptionsValue'        => 'name',
                                    'fieldDataSelect2OptionsArray'        => true
                                ]
                            ) .
                        '</div>
                        <div class="col text-center">' .
                            $this->useTag('buttons',
                                [
                                    'componentId'                         => $this->params['componentId'],
                                    'sectionId'                           => $this->params['sectionId'] . '-filter',
                                    'buttonType' => 'button',
                                    'buttonLabel' => false,
                                    'buttons' => [
                                        'assign' => [
                                            'id' => 'assign',
                                            'icon'=> 'arrow-right',
                                            'title' => 'Assign'
                                        ]
                                    ]
                                ]
                            ) .
                        '</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col">' .
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
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row" style="max-height:261px; min-height:261px; overflow:scroll;">
                        <div class="col">
                            <div class="table-responsive-sm">
                                <table class="table table-striped">
                                    <thead class="thead-dark|thead-light">
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
                                            <td><button class="btn btn-danger btn-xs"><i class="fa fas fa-trash fa-xs"></i></button></td>
                                        </tr>
                                        <tr>
                                            <td>can_login:is:"1"</td>
                                            <td></td>
                                            <td><button class="btn btn-danger btn-xs"><i class="fa fas fa-trash fa-xs"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">' .
                            $this->useTag('fields',
                                [
                                    'componentId'                         => $this->params['componentId'],
                                    'sectionId'                           => $this->params['sectionId'] . '-filter',
                                    'fieldId'                             => 'name',
                                    'fieldLabel'                          => 'Save Filter',
                                    'fieldType'                           => 'input',
                                    'fieldInputType'                      => 'text',
                                    'fieldHelp'                           => true,
                                    'fieldHelpTooltipContent'             => 'Filter name to save the above filters',
                                    'fieldBazScan'                        => true,
                                    'fieldBazPostOnCreate'                => false,
                                    'fieldBazPostOnUpdate'                => true,
                                    'fieldDataMinLength'                  => 1,
                                    'fieldDataMaxLength'                  => 50,
                                    'fieldGroupPostAddonButtonTooltipTitle'=> 'Save Filter',
                                    'fieldGroupPostAddonButtonId'         => 'save',
                                    'fieldGroupPostAddonButtonIcon'       => 'save',
                                    'fieldGroupPostAddonButtonValue'      => 'Save'
                                ]
                            ) .
                        '</div>
                        <div class="col">' .
                            $this->useTag('buttons',
                                [
                                    'componentId'                         => $this->params['componentId'],
                                    'sectionId'                           => $this->params['sectionId'] . '-filter',
                                    'buttonLabel'                       => false,
                                    'buttonType' => 'button',
                                    'buttons' => [
                                        'reset' => [
                                            'id' => 'reset',
                                            'type' => 'danger',
                                            'icon' => 'trash',
                                            'title' => 'Reset filters'
                                        ],
                                        'apply' => [
                                            'id' => 'apply',
                                            'title' => 'Apply filters',
                                            'position' => 'right'
                                        ]
                                    ]
                                ]
                            ) .
                        '</div>
                    </div>
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
                            }
                        }
                    );
            </script>';
    }
}