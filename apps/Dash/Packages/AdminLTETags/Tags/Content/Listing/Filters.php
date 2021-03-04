<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Content\Listing;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Arr;

class Filters extends AdminLTETags
{
    protected $params;

    protected $content = '';

    protected $fieldParams = [];

    protected $compSecId;

    public function getContent($params)
    {
        $this->params = $params;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->generateContent();

        return $this->content;
    }

    protected function generateContent()
    {
        $defaultFilter = null;

        $account = $this->auth->account();

        $filters = [];

        if (!$this->auth->account()) {
            $sharedHidden = true;
        } else {
            $sharedHidden = false;
        }

        $employeesPackage = $this->init()->checkPackage('Apps\Dash\Packages\Hrms\Employees\Employees');

        if ($employeesPackage) {
            $employeesPackage = new \Apps\Dash\Packages\Hrms\Employees\Employees;
        }

        foreach ($this->params['dtFilters'] as $filterKey => $filter) {
            $filters[$filterKey] = $filter;

            if (isset($filter['data']['queryFilterId'])) {
                if ($filter['data']['queryFilterId'] === $filter['id']) {
                    $defaultFilter = $filterKey;
                } else {
                    $defaultFilter = 0;
                }
                $queryFilterId = $filter['data']['queryFilterId'];
            } else if (($account && ($account['id'] === $filter['account_id'])) &&
                $filter['is_default'] === '1'
            ) {
                if (!$defaultFilter) {
                    $defaultFilter = $filterKey;
                }

                $filters[$filterKey]['name'] =
                    $filters[$filterKey]['name'] . ' (Default)';
            }

            if ($filter['filter_type'] === '0') {
                $filters[$filterKey]['name'] = $filters[$filterKey]['name'] . ' (System)';
            // } else if ($filter['filter_type'] === '1') {
            //     $filters[$filterKey]['name'] = $filters[$filterKey]['name'] . ' (Shared)';
            } else if ($filter['shared'] == '1' && $filter['shared_ids']) {
                if ($this->auth->account()['id'] != $filter['account_id']) {
                    if ($employeesPackage) {
                        $employee = $employeesPackage->searchByAccountId($filter['account_id']);

                        $filters[$filterKey]['name'] = $filters[$filterKey]['name'] . ' (Shared by ' . $employee['full_name'] . ')';
                    } else {
                        $filters[$filterKey]['name'] = $filters[$filterKey]['name'] . ' (Shared by ' . $filter['account_email'] . ')';
                    }
                }
            }
        }
        if ($defaultFilter === null) {
            $defaultFilter = Arr::firstKey($filters);
        }

        if ($defaultFilter === 0) {
            $this->content .=
                '</div><div id="' . $this->compSecId . '-filter-alert" class="alert alert-danger alert-dismissible animated fadeIn rounded-0 mb-3">
                    <button id="admin-filters-main-alert-dismiss" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="icon fa fa-ban"></i>Filter with id ' . $queryFilterId . ' not found. Please select filter from list below.
                </div><div class="row mb-2">';
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
                                        'position'                => 'right',
                                        'url'                     => $this->links->url('system/filters/remove')
                                    ],
                                    'share'   => [
                                        'title'                   => false,
                                        'type'                    => 'primary',
                                        'tooltipTitle'            => 'Share Selected Saved Filter',
                                        'icon'                    => 'share-alt',
                                        'noMargin'                => true,
                                        'disabled'                => true,
                                        'hidden'                  => $sharedHidden,
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
                                        'position'                => 'right',
                                        'url'                     => $this->links->url('system/filters/add')
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
                            'fieldDataSelectOptions'              => $filters,
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
            '<section id="' . $this->compSecId . '-filter" class="sectionWithListingFilter">
                <form autocomplete="off" class="mt-1" data-validateon="section" id="' . $this->compSecId . '-filter-form">
                    <fieldset id="' . $this->compSecId . '-filter-fieldset">
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
                                <div data-validateOn="section" id="' . $this->compSecId . '-filter-table"></div>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset id="' . $this->compSecId . '-filter-fieldset-save-apply">
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
                                        'fieldDataAttributes'                   => ['href' => $this->links->url('system/filters/getdefaultfilter')],
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
                                    'url'                   => $this->links->url('system/filters/add')
                                ],
                                'save-update' => [
                                    'title'                 => 'Update',
                                    'disabled'              => true,
                                    'hidden'                => true,
                                    'icon'                  => 'save',
                                    'url'                   => $this->links->url('system/filters/update')
                                ],
                            ]
                    ]
            ]
        );
    }

    protected function getShareModalContent()
    {
        $modalContent =
            '<section id="' . $this->compSecId . '-filter-sharing" class="sectionWithForm">
                <form autocomplete="off" class="mt-1" data-validateon="section" id="' . $this->compSecId . '-filter-sharing-form">
                    <fieldset id="' . $this->compSecId . '-filter-sharing-fieldset">
                        <div class="row">
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter-sharing',
                                        'fieldId'                             => 'direct-url',
                                        'fieldLabel'                          => 'Direct URL',
                                        'fieldType'                           => 'input',
                                        'fieldBazScan'                        => true,
                                        'fieldGroupPostAddonButtons'          =>
                                            [
                                                'direct-url-copy'   => [
                                                    'title'                   => false,
                                                    'type'                    => 'primary',
                                                    'icon'                    => 'copy',
                                                    'noMargin'                => true,
                                                    'buttonAdditionalClass'   => 'rounded-0',
                                                    'position'                => 'right',
                                                ]
                                            ]
                                    ]
                                ) .
                            '</div>
                        </div>
                        <div class="row">
                            <div class="col">' .
                                $this->useTag('fields',
                                    [
                                        'componentId'                         => $this->params['componentId'],
                                        'sectionId'                           => $this->params['sectionId'] . '-filter-sharing',
                                        'fieldId'                             => 'rids',
                                        'fieldLabel'                          => 'Role(s)',
                                        'fieldType'                           => 'select2',
                                        'fieldHelp'                           => true,
                                        'fieldHelpTooltipContent'             => 'Select Roles to share filter with',
                                        'fieldBazScan'                        => true,
                                        'fieldRequired'                       => false,
                                        'fieldDataSelect2Options'             => [],
                                        'fieldDataSelect2Multiple'            => true,
                                        'fieldDataSelect2OptionsKey'          => 'id',
                                        'fieldDataSelect2OptionsValue'        => 'name',
                                        'fieldDataSelect2OptionsArray'        => true
                                    ]
                                ) .
                            '</div>
                        </div>';
                        //Check if employees package exists we show employees. We can extract the Account Ids from them.
                        $employeesPackage = $this->init()->checkPackage('Apps\Dash\Packages\Hrms\Employees\Employees');

                        if ($employeesPackage) {
                            $modalContent .= '<div class="row">
                                <div class="col">' .
                                    $this->useTag('fields',
                                        [
                                            'componentId'                         => $this->params['componentId'],
                                            'sectionId'                           => $this->params['sectionId'] . '-filter-sharing',
                                            'fieldId'                             => 'eids',
                                            'fieldLabel'                          => 'Employee(s)',
                                            'fieldType'                           => 'select2',
                                            'fieldHelp'                           => true,
                                            'fieldHelpTooltipContent'             => 'Select Employees to share filter with',
                                            'fieldBazScan'                        => true,
                                            'fieldRequired'                       => false,
                                            'fieldDataSelect2Options'             => [],
                                            'fieldDataSelect2Multiple'            => true,
                                            'fieldDataSelect2OptionsKey'          => 'id',
                                            'fieldDataSelect2OptionsValue'        => 'name',
                                            'fieldDataSelect2OptionsArray'        => true
                                        ]
                                    ) .
                                '</div>
                            </div>';
                        } else {
                            $modalContent .= '<div class="row">
                                <div class="col">' .
                                    $this->useTag('fields',
                                        [
                                            'componentId'                         => $this->params['componentId'],
                                            'sectionId'                           => $this->params['sectionId'] . '-filter-sharing',
                                            'fieldId'                             => 'aids',
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
                                            'fieldDataSelect2OptionsArray'        => true
                                        ]
                                    ) .
                                '</div>
                            </div>';
                        }

                    $modalContent .=
                    '</fieldset>
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
                'modalEscClose'             => 'false',
                'modalFooterButtons'        =>
                    [
                        'componentId'                       => $this->params['componentId'],
                        'sectionId'                         => $this->params['sectionId'] . '-filter',
                        'buttonType'                        => 'button',
                        'buttonSize'                        => 'sm',
                        'buttons'                           =>
                            [
                                'share-filter' => [
                                    'title'                 => 'Share',
                                    'disabled'              => true,
                                    'icon'                  => 'share-alt',
                                    'url'                   => $this->links->url('system/filters/update')
                                ]
                            ]
                    ]
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
                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '-filter-sharing"] =
                    $.extend(
                        window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '-filter-sharing"],
                        {
                            "' . $this->compSecId . '-filter-sharing-direct-url" : {
                                    afterInit: function() {
                                        var copyText = document.getElementById("' . $this->compSecId . '-filter-sharing-direct-url");

                                        $("#' . $this->compSecId . '-filter-sharing-direct-url-copy").click(function(e) {
                                            e.preventDefault();

                                            copyText.select();
                                            copyText.setSelectionRange(0, 99999);
                                            document.execCommand("copy");
                                        });
                                    }
                                },
                            "' . $this->compSecId . '-filter-sharing-rids"        : {
                                placeholder: "SELECT ROLE(S)",
                                "ajax"          : {
                                    url         : "' . $this->links->url('system/filters/searchRole') . '",
                                    dataType    : "json",
                                    method      : "post",
                                    data: function(params) {
                                        return {
                                            search: params.term
                                        }
                                    },
                                    processResults: function(data) {
                                        if (data.tokenKey && data.token) {
                                            $("#security-token").attr("name", data.tokenKey);
                                            $("#security-token").val(data.token);
                                        }
                                        if (data.roles) {
                                            var rolesData = [];
                                            for (var item of data.roles) {
                                                rolesData.push({
                                                    "id"    : item["id"],
                                                    "text"  : item["name"]
                                                });
                                            }

                                            return {
                                                results: rolesData
                                            }
                                        } else {
                                            return {
                                                results : []
                                            }
                                        }
                                    },
                                    cache: true
                                },
                                minimumInputLength : 2
                            },
                            "' . $this->compSecId . '-filter-sharing-eids"        : {
                                placeholder: "SELECT EMPLOYEE(S)",
                                "ajax"          : {
                                    url         : "' . $this->links->url('system/filters/searchEmployee') . '",
                                    dataType    : "json",
                                    method      : "post",
                                    data: function(params) {
                                        return {
                                            search: params.term
                                        }
                                    },
                                    processResults: function(data) {
                                        if (data.tokenKey && data.token) {
                                            $("#security-token").attr("name", data.tokenKey);
                                            $("#security-token").val(data.token);
                                        }
                                        if (data.employees) {
                                            var employeesData = [];
                                            for (var item of data.employees) {
                                                employeesData.push({
                                                    "id"    : item["id"],
                                                    "text"  : item["full_name"]
                                                });
                                            }

                                            return {
                                                results: employeesData
                                            }
                                        } else {
                                            return {
                                                results : []
                                            }
                                        }
                                    },
                                    cache: true
                                },
                                minimumInputLength : 2
                            },
                            "' . $this->compSecId . '-filter-sharing-aids"        : {
                                placeholder: "SELECT ACCOUNT(S)",
                                "ajax"          : {
                                    url         : "' . $this->links->url('system/filters/searchAccount') . '",
                                    dataType    : "json",
                                    method      : "post",
                                    data: function(params) {
                                        return {
                                            search: params.term
                                        }
                                    },
                                    processResults: function(data) {
                                        if (data.tokenKey && data.token) {
                                            $("#security-token").attr("name", data.tokenKey);
                                            $("#security-token").val(data.token);
                                        }
                                        if (data.accounts) {
                                            var accountsData = [];
                                            for (var item of data.accounts) {
                                                accountsData.push({
                                                    "id"    : item["id"],
                                                    "text"  : item["email"]
                                                });
                                            }

                                            return {
                                                results: accountsData
                                            }
                                        } else {
                                            return {
                                                results : []
                                            }
                                        }
                                    },
                                    cache: true
                                },
                                minimumInputLength : 2
                            },
                        }
                    );
                window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '-filter"] =
                    $.extend(
                        window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->compSecId . '-filter"],
                        {
                            "' . $this->compSecId . '-filter-andor" : {
                                placeholder: "SELECT AND/OR",
                            },
                            "' . $this->compSecId . '-filter-field" : {
                                placeholder: "SELECT FIELD TO FILTER",
                            },
                            "' . $this->compSecId . '-filter-operator" : {
                                placeholder: "SELECT FILTER OPERATOR",
                            },
                            "' . $this->compSecId . '-filter-value" : {
                            },
                            "' . $this->compSecId . '-filter-id" : {
                            },
                            "' . $this->compSecId . '-filter-name" : {
                            },
                            "' . $this->compSecId . '-filter-default" : {
                            },
                            "' . $this->compSecId . '-filter-datatables" : [
                                "' . $this->compSecId . '-filter-table"
                            ],
                            "' . $this->compSecId . '-filter-table" : {
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
                                    "keepFieldsData"    : ["' . $this->compSecId . '-filter-andor"]
                                },
                                "postExtraction": function(datatable, extractedData) {
                                    "use strict";

                                    var fieldData = extractedData[0][1]["extractedData"];

                                    extractedData[0][1]["extractedData"] = fieldData.replace(" (Numeric)", "");
                                }
                            },
                            "' . $this->compSecId . '-filter-form" : {
                                "rules"     : {
                                    "' . $this->compSecId . '-filter-field" : "required",
                                    "' . $this->compSecId . '-filter-operator" : "required",
                                    "' . $this->compSecId . '-filter-value" : "required"
                                },
                                messages: {
                                    "' . $this->compSecId . '-filter-field" : "Please select a field",
                                    "' . $this->compSecId . '-filter-operator" : "Please select an operator",
                                    "' . $this->compSecId . '-filter-value" : "Please enter value. Numeric Fields only accept numbers or comma or decimal point"
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