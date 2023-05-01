<?php

namespace Apps\Core\Packages\AdminLTETags\Tags\Fields;

use Apps\Core\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Json;

class Jstree
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
        $this->adminLTETags = new AdminLTETags();

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
        if (!isset($this->params['fieldJstreeDataSrcArr']) || !is_array($this->params['fieldJstreeDataSrcArr'])) {
            throw new \Exception('fieldJstreeDataSrcArr for jstree not set. Example: "fieldJstreeDataSrcArr":["permissions":["srcArr":components]]');
        }

        $this->content .=
            '<input type="text" class="form-control form-control-sm jstreevalidate rounded-0" ' . $this->fieldParams['fieldId'] . '-validate" ' . $this->fieldParams['fieldName'] . '-tree-validate" placeholder="' . strtoupper($this->fieldParams['fieldPlaceholder']) . '" hidden />
            <div ' . $this->fieldParams['fieldId'] . '-tree-tools" class="mb-2 float-right">
                <a href="#" ' . $this->fieldParams['fieldId'] . '-tools-add" class="text-primary" data-container="body" data-placement="left" data-toggle="tooltip" data-html="true" title="Add new ' . $this->params['fieldLabel'] . '" hidden>
                    <i class="fa fa-fw fa-plus text-success"></i>
                </a>
                <a href="#" ' . $this->fieldParams['fieldId'] . '-tools-edit" class="text-primary ml-1" data-container="body" data-placement="left" data-toggle="tooltip" data-html="true" title="Edit selected ' . $this->params['fieldLabel'] . '" hidden>
                    <i class="fa fa-fw fa-edit text-warning"></i>
                </a>
                <a href="#" ' . $this->fieldParams['fieldId'] . '-tools-collapse" class="text-primary ml-1" data-container="body" data-placement="left" data-toggle="tooltip" data-html="true" title="Collapse All" hidden>
                    <i class="fa fa-fw fa-compress-arrows-alt text-primary"></i>
                </a>
                <a href="#" ' . $this->fieldParams['fieldId'] . '-tools-expand" class="text-primary ml-1" data-container="body" data-placement="left" data-toggle="tooltip" data-html="true" title="Expand All" hidden>
                    <i class="fa fa-fw fa-expand-arrows-alt text-primary"></i>
                </a>
            </div>';

            if (isset($this->params['fieldJstreeSearch']) && $this->params['fieldJstreeSearch'] === true) {
                $this->content .=
                    $this->adminLTETags->useTag(
                        'fields',
                        [
                            'component'                               => $this->params['component'],
                            'componentName'                           => $this->params['componentName'],
                            'componentId'                             => $this->params['componentId'],
                            'sectionId'                               => $this->params['sectionId'],
                            'fieldId'                                 => $this->params['fieldId'] . '-tree-search-input',
                            'fieldLabel'                              => false,
                            'fieldType'                               => 'input',
                            'fieldHelp'                               => false,
                            'fieldAdditionalClass'                    => 'mb-1',
                            'fieldGroupSize'                          => 'sm',
                            'fieldRequired'                           => false,
                            'fieldBazScan'                            => false,
                            'fieldHidden'                             => false,
                            'fieldPlaceholder'                        => 'Search ' . $this->params['fieldLabel'] . '...',
                            'fieldGroupPostAddonIcon'                 => 'search'
                        ]
                    );
            }

            $this->content .=
                $this->adminLTETags->useTag(
                    'fields',
                    [
                        'component'                                => $this->params['component'],
                        'componentName'                            => $this->params['componentName'],
                        'componentId'                              => $this->params['componentId'],
                        'sectionId'                                => $this->params['sectionId'],
                        'fieldId'                                  => $this->params['fieldId'] . '-tree-add-input',
                        'fieldLabel'                               => false,
                        'fieldType'                                => 'input',
                        'fieldHelp'                                => false,
                        'fieldAdditionalClass'                     => 'mb-1',
                        'fieldGroupSize'                           => 'sm',
                        'fieldRequired'                            => false,
                        'fieldPlaceholder'                         => 'Add...',
                        'fieldBazScan'                             => false,
                        'fieldHidden'                              => true,
                        'fieldGroupPreAddonButtonId'               => 'cancel',
                        'fieldGroupPreAddonButtonValue'            => '<i class="fa fa-fw fa-times"></i>',
                        'fieldGroupPreAddonButtonClass'            => 'danger',
                        'fieldGroupPostAddonButtonId'              => 'success',
                        'fieldGroupPostAddonButtonValue'           => '<i class="fa fa-fw fa-plus"></i>',
                        'fieldGroupPostAddonButtonClass'           => 'success'
                    ]
                );

            $this->content .=
                $this->adminLTETags->useTag(
                    'fields',
                    [
                        'component'                                => $this->params['component'],
                        'componentName'                            => $this->params['componentName'],
                        'componentId'                              => $this->params['componentId'],
                        'sectionId'                                => $this->params['sectionId'],
                        'fieldId'                                  => $this->params['fieldId'] . '-tree-edit-input',
                        'fieldLabel'                               => false,
                        'fieldType'                                => 'input',
                        'fieldHelp'                                => false,
                        'fieldAdditionalClass'                     => 'mb-1',
                        'fieldGroupSize'                           => 'sm',
                        'fieldRequired'                            => false,
                        'fieldPlaceholder'                         => 'Edit...',
                        'fieldBazScan'                             => false,
                        'fieldHidden'                              => true,
                        'fieldGroupPreAddonButtonId'               => 'cancel',
                        'fieldGroupPreAddonButtonValue'            => '<i class="fa fa-fw fa-times"></i>',
                        'fieldGroupPreAddonButtonClass'            => 'danger',
                        'fieldGroupPostAddonButtonId'              => 'success',
                        'fieldGroupPostAddonButtonValue'           => '<i class="fa fa-fw fa-edit"></i>',
                        'fieldGroupPostAddonButtonClass'           => 'success'
                    ]
                );

            if (!isset($this->params['fieldJstreeAdditionalClass'])) {
                $this->fieldParams['fieldJstreeAdditionalClass'] = 'height-control-400';
            } else {
                $this->fieldParams['fieldJstreeAdditionalClass'] = $this->params['fieldJstreeAdditionalClass'] . ' height-control-400';
            }

            $this->content .=
                '<div class="' . $this->fieldParams['fieldJstreeAdditionalClass'] . ' p-1 border mt-1" ' . $this->fieldParams['fieldId'] . '-tree-div" style="font-size: 0.75rem !important;">
                    <div '. $this->fieldParams['fieldBazPostOnCreate'] . ' ' . $this->fieldParams['fieldBazPostOnUpdate'] . ' ' . $this->fieldParams['fieldBazScan'] . ' ' . $this->fieldParams['fieldId'] . '">';

                        $this->fieldParams['fieldJstreeRootIcon'] =
                            isset($this->params['fieldJstreeRootIcon']) ?
                            '{"icon" : "fa fa-fw fa-' . $this->params['fieldJstreeRootIcon'] . ' text-sm"}' :
                            '{"icon" : "fas fa-fw fa-circle-dot text-sm"}';

                        if (isset($this->params['fieldJstreeIncludeRootInPath']) &&
                            $this->params['fieldJstreeIncludeRootInPath'] === true
                        ) {
                            $this->content .= '<ul>';
                        }

                        $counter = 0;
                        foreach ($this->params['fieldJstreeDataSrcArr'] as $dataArrKey => $dataArr) {
                            if (is_array($dataArr)) {
                                if (isset($this->params['fieldJstreeIncludeRootInPath']) &&
                                    $this->params['fieldJstreeIncludeRootInPath'] === true
                                ) {

                                    $this->content .=
                                        '<li data-id="' . $counter . '" data-jstree=\'' . $this->fieldParams['fieldJstreeRootIcon'] . '\'>' . strtoupper($dataArrKey);

                                    $counter++;
                                }
                                $this->content .=
                                    '<ul>';

                                if (isset($dataArr['srcArr'])) {
                                    $treeData = $dataArr['srcArr'];
                                } else {
                                    throw new \Exception('srcArr for jstree not set. Example: "fieldJstreeDataSrcArr":["permissions":["srcArr":components]]');
                                }

                                $groupIcon =
                                    isset($dataArr['groupIcon']) ?
                                    $groupIcon = '{"icon" : "fa fa-fw fa-' . $dataArr['groupIcon'] . ' text-sm"}':
                                    $groupIcon = '{"icon" : "fa fa-fw fa-plus text-sm"}';

                                $itemIcon =
                                    isset($dataArr['itemIcon']) ?
                                    $itemIcon = '{"icon" : "fa fa-fw fa-' . $dataArr['itemIcon'] . ' text-sm"}':
                                    $itemIcon = '{"icon" : "fas fa-fw fa-circle-dot text-sm"}';

                                $this->content .=
                                    $this->adminLTETags->useTag(
                                            'tree',
                                            [
                                                'treeMode'      => 'jstree',
                                                'treeData'      => $treeData,
                                                'groupIcon'     => $groupIcon,
                                                'itemIcon'      => $itemIcon
                                            ]
                                        );

                                $this->content .=
                                    '</ul>';

                                if (isset($this->params['fieldJstreeIncludeRootInPath']) &&
                                    $this->params['fieldJstreeIncludeRootInPath'] === true
                                ) {
                                    $this->content .=
                                        '</li>';
                                }
                            }
                        }

                        if (isset($this->params['fieldJstreeIncludeRootInPath']) &&
                            $this->params['fieldJstreeIncludeRootInPath'] === true
                        ) {
                            $this->content .=
                                '</ul>';
                        }

            $this->content .=
                    '</div>
                </div>';

            $this->content .= $this->inclJs();
    }

    protected function inclJs()
    {
        $this->fieldParams['fieldJstreeAdd'] =
            isset($this->params['fieldJstreeAdd']) ?
            $this->params['fieldJstreeAdd'] :
            false;

        $this->fieldParams['fieldJstreeEdit'] =
            isset($this->params['fieldJstreeEdit']) ?
            $this->params['fieldJstreeEdit'] :
            false;

        $this->fieldParams['fieldJstreeSearch'] =
            isset($this->params['fieldJstreeSearch']) ?
            $this->params['fieldJstreeSearch'] :
            false;

        $this->fieldParams['fieldJstreeExpand'] =
            isset($this->params['fieldJstreeExpand']) ?
            $this->params['fieldJstreeExpand'] :
            false;

        $this->fieldParams['fieldJstreeCollapse'] =
            isset($this->params['fieldJstreeCollapse']) ?
            $this->params['fieldJstreeCollapse'] :
            false;

        $this->fieldParams['fieldJstreeFirstOpen'] =
            isset($this->params['fieldJstreeFirstOpen']) ?
            $this->params['fieldJstreeFirstOpen'] :
            false;

        $this->fieldParams['fieldJstreeAllOpen'] =
            isset($this->params['fieldJstreeAllOpen']) ?
            $this->params['fieldJstreeAllOpen'] :
            false;

        $this->fieldParams['fieldJstreeAllChecked'] =
            isset($this->params['fieldJstreeAllChecked']) ?
            $this->params['fieldJstreeAllChecked'] :
            false;

        $this->fieldParams['fieldJstreeToggleAllChildren'] =
            isset($this->params['fieldJstreeToggleAllChildren']) ?
            $this->params['fieldJstreeToggleAllChildren'] :
            false;

        $this->fieldParams['fieldJstreeIncludeRootInPath'] =
            isset($this->params['fieldJstreeIncludeRootInPath']) ?
            $this->params['fieldJstreeIncludeRootInPath'] :
            false;

        $this->fieldParams['fieldJstreeSelectEndNodeOnly'] =
            isset($this->params['fieldJstreeSelectEndNodeOnly']) ?
            $this->params['fieldJstreeSelectEndNodeOnly'] :
            false;

        $this->fieldParams['fieldJstreeHideJstreeIcons'] =
            isset($this->params['fieldJstreeHideJstreeIcons']) ?
            $this->params['fieldJstreeHideJstreeIcons'] :
            false;

        $this->fieldParams['fieldJstreeTheme'] =
            isset($this->params['fieldJstreeTheme']) ?
            $this->params['fieldJstreeTheme'] :
            'default';

        $this->fieldParams['fieldJstreeShowDots'] =
            isset($this->params['fieldJstreeShowDots']) ?
            $this->params['fieldJstreeShowDots'] :
            false;

        $this->fieldParams['fieldJstreeDoubleClickToggle'] =
            isset($this->params['fieldJstreeDoubleClickToggle']) ?
            $this->params['fieldJstreeDoubleClickToggle'] :
            false;

        $this->fieldParams['fieldJstreeMultiple'] =
            isset($this->params['fieldJstreeMultiple']) ?
            $this->params['fieldJstreeMultiple'] :
            false;

        $this->fieldParams['fieldJstreeSearchShowOnlyMatches'] =
            isset($this->params['fieldJstreeSearchShowOnlyMatches']) ?
            $this->params['fieldJstreeSearchShowOnlyMatches'] :
            false;

        $this->fieldParams['fieldJstreeSearchShowOnlyMatchesChildren'] =
            isset($this->params['fieldJstreeSearchShowOnlyMatchesChildren']) ?
            $this->params['fieldJstreeSearchShowOnlyMatchesChildren'] :
            false;

        $this->fieldParams['fieldJstreeSearchCaseSensitive'] =
            isset($this->params['fieldJstreeSearchCaseSensitive']) ?
            $this->params['fieldJstreeSearchCaseSensitive'] :
            false;

        $this->fieldParams['fieldJstreeReplaceIdWithDataField'] =
            isset($this->params['fieldJstreeReplaceIdWithDataField']) ?
            $this->params['fieldJstreeReplaceIdWithDataField'] :
            false;

        $this->fieldParams['fieldJstreePlugins'] =
            isset($this->params['fieldJstreePlugins']) ?
            Json::encode($this->params['fieldJstreePlugins']) :
            Json::encode(["search", "types", "dnd"]);

        return
        '<script type="text/javascript" charset="utf-8">
            if (!window["dataCollection"]["' . $this->params['componentId'] . '"]) {
                window["dataCollection"]["' . $this->params['componentId'] . '"] = { };
            }
            window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->params['componentId'] . '-'. $this->params['sectionId'] . '"] = $.extend(window["dataCollection"]["' . $this->params['componentId'] . '"]["' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '"], {
                "' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-' . $this->params['fieldId'] . '" : {
                    "bazJstreeOptions": {
                        "rootIcon": \'' . $this->fieldParams['fieldJstreeRootIcon'] . '\',
                        "treeName": "' . strtoupper($this->fieldParams['fieldLabel']) . '",
                        "treePathSeparator": " <i class=\"fa fa-angle-right text-sm\"></i> ",
                        "add": "' . $this->fieldParams['fieldJstreeAdd'] . '",
                        "addFunction": function() {
                            "use strict";
                        },
                        "edit": "' . $this->fieldParams['fieldJstreeEdit'] . '",
                        "editFunction": function() {
                            "use strict";
                        },
                        "search" : "' . $this->fieldParams['fieldJstreeSearch'] . '",
                        "expand": "' . $this->fieldParams['fieldJstreeExpand'] . '",
                        "collapse": "' . $this->fieldParams['fieldJstreeCollapse'] . '",
                        "firstOpen": "' . $this->fieldParams['fieldJstreeFirstOpen'] . '",
                        "allOpen" : "' . $this->fieldParams['fieldJstreeAllOpen'] . '",
                        "allChecked" : "' . $this->fieldParams['fieldJstreeAllChecked'] . '",
                        "toggleAllChildren": "' . $this->fieldParams['fieldJstreeToggleAllChildren'] . '",
                        "inclRoot": "' . $this->fieldParams['fieldJstreeIncludeRootInPath'] . '",
                        "selectEndNodeOnly": "' . $this->fieldParams['fieldJstreeSelectEndNodeOnly'] . '",
                        "hideJstreeIcon": "' . $this->fieldParams['fieldJstreeHideJstreeIcons'] . '",
                        "replaceIdWithDataField": "' . $this->fieldParams['fieldJstreeReplaceIdWithDataField'] . '"
                    },
                    "core": {
                        "themes": {
                            "name": "' . $this->fieldParams['fieldJstreeTheme'] . '",
                            "dots": "' . $this->fieldParams['fieldJstreeShowDots'] . '"
                        },
                        "dblclick_toggle": "' . $this->fieldParams['fieldJstreeDoubleClickToggle'] . '",
                        "check_callback": true,
                        "multiple": "' . $this->fieldParams['fieldJstreeMultiple'] . '",
                    },
                    "plugins": ' . $this->fieldParams['fieldJstreePlugins'] . ',
                    "search": {
                        "show_only_matches": "' . $this->fieldParams['fieldJstreeSearchShowOnlyMatches'] . '",
                        "show_only_matches_children": "' . $this->fieldParams['fieldJstreeSearchShowOnlyMatchesChildren'] . '",
                        "case_sensitive" : "' . $this->fieldParams['fieldJstreeSearchCaseSensitive'] . '"
                    },
                    "checkbox": {
                        "whole_node" : false,
                        "keep_selected_style" : false,
                        "tie_selection" : false
                    }
                }
            });
        </script>';
    }
}