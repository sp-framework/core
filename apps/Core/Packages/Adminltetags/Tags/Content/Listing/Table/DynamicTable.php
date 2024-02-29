<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Content\Listing\Table;

use Apps\Core\Packages\Adminltetags\Adminltetags;
use Apps\Core\Packages\Adminltetags\Tags\Content\Listing\Filters;

class DynamicTable
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $params;

    protected $dtParams;

    protected $content;

    protected $adminLTETags;

    public function __construct($view, $tag, $links, $escaper, $params)
    {
        $this->adminLTETags = new Adminltetags();

        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->params = $params;

        if (isset($this->params['dtColumns'])) {
            $this->generateTableContent();
        } else if (isset($this->params['dtRows'])) {
            $this->generateRowsContent();
        }
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function generateTableContent()
    {

        if (isset($this->params['dtPrimaryButtons']) ||
            isset($this->params['dtSecondaryButtons']) ||
            isset($this->params['dtFilter'])
        ) {
            $this->content .=
                '<div class="row mb-2">';

            if (isset($this->params['dtFilter']) && $this->params['dtFilter'] === true) {
                $this->content .=
                    (new Filters($this->view, $this->tag, $this->links, $this->escaper))->getContent($this->params);
            }
            $this->content .= '<div class="col"><div class="row">';
            if (isset($this->params['dtPrimaryButtons'])) {
                $this->content .=
                    '<div class="col" id="listing-primary-buttons" hidden>';

                $this->content .=
                    $this->adminLTETags->useTag(
                            'buttons',
                            $this->params['dtPrimaryButtons']
                        );

                $this->content .= '</div>';

            }

            if (isset($this->params['dtSecondaryButtons'])) {
                $this->content .=
                    '<div class="col" id="listing-secondary-buttons" hidden>';

                $this->content .=
                    $this->adminLTETags->useTag(
                            'buttons',
                            $this->params['dtSecondaryButtons']
                        );

                $this->content .= '</div>';
            }

            $this->content .= '</div></div></div>';
        }

        $this->dtParams['dtStriped'] =
            isset($this->params['dtStriped']) && $this->params['dtStriped'] === true ?
            'table-striped' :
            '';

        $this->dtParams['dtBordered'] =
            isset($this->params['dtBordered']) && $this->params['dtBordered'] === true ?
            'table-bordered' :
            '';

        $this->dtParams["dtTableCompact"] =
            isset($this->params["dtTableCompact"]) ?
            $this->params["dtTableCompact"] :
            false;
        $compact = $this->dtParams["dtTableCompact"] === true ? ' compact ' : '';

        $this->dtParams["dtResponsive"] =
            isset($this->params["dtResponsive"]) ?
            $this->params["dtResponsive"] :
            true;
        $responsive = $this->dtParams["dtResponsive"] === true ? ' dt-responsive ' : '';

        $this->content .=
            '<div class="row">
                <div class="col">
                    <div class="row m-2 text-center" id="listing-data-loader">
                        <div class="col">
                            <div class="fa-2x">
                                <i class="fa fa-cog fa-spin"></i> Loading...
                            </div>
                        </div>
                    </div>
                    <table id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-table" class="table ' . $this->dtParams['dtStriped'] . ' ' . $this->dtParams['dtBordered'] . $compact . $responsive . '" style="z-index:9999;" width="100%" cellspacing="0">
                        <tbody></tbody>
                    </table>
                </div>
            </div>';

            $this->inclTableJs();
    }

    protected function inclTableJs()
    {
        $this->dtParams['NoOfResultsToShow'] =
            isset($this->params["NoOfResultsToShow"]) ?
            $this->params["NoOfResultsToShow"] :
            20;

        if (isset($this->params["dtColumns"])) {
            $this->dtParams["dtColumns"] =
                $this->escaper->escapeJs($this->helper->encode([$this->params["dtColumns"]]));
        } else {
            throw new \Exception('Datatable columns missing');
        }

        if (isset($this->params["dtPostUrl"])) {
            $this->dtParams["dtPostUrl"] = $this->params["dtPostUrl"];
        } else {
            throw new \Exception('PostUrl missing');
        }

        $this->dtParams["dtPostUrlParams"] =
            isset($this->params["dtPostUrlParams"]) ?
            $this->escaper->escapeJs($this->helper->encode($this->params["dtPostUrlParams"])) :
            null;

        $this->dtParams["dtNoOfColumnsToShow"] =
            isset($this->params["dtNoOfColumnsToShow"]) ?
            $this->params["dtNoOfColumnsToShow"] :
            3;

        $this->dtParams["dtAddIdColumn"] =
            isset($this->params["dtAddIdColumn"]) ?
            $this->params["dtAddIdColumn"] :
            false;

        $this->dtParams["dtHideIdColumn"] =
            isset($this->params["dtHideIdColumn"]) ?
            $this->params["dtHideIdColumn"] :
            true;

        $this->dtParams["dtShowHideColumnsButton"] =
            isset($this->params["dtShowHideColumnsButton"]) ?
            $this->params["dtShowHideColumnsButton"] :
            true;

        $this->dtParams["dtShowHideColumnsButtonType"] =
            isset($this->params["dtShowHideColumnsButtonType"]) ?
            $this->params["dtShowHideColumnsButtonType"] :
            'info';

        $this->dtParams["dtShowHideExportButton"] =
            isset($this->params["dtShowHideExportButton"]) ?
            $this->params["dtShowHideExportButton"] :
            true;

        $this->dtParams["dtColReorder"] =
            isset($this->params["dtColReorder"]) ?
            $this->params["dtColReorder"] :
            false;

        $this->dtParams["dtOrder"] =
            isset($this->params["dtOrder"]) ?
            $this->escaper->escapeJs($this->helper->encode($this->params["dtOrder"])) :
            $this->escaper->escapeJs($this->helper->encode([]));

        $this->dtParams["dtStateSave"] =
            isset($this->params["dtStateSave"]) ?
            $this->params["dtStateSave"] :
            false;

        $this->dtParams["dtHeaderClass"] =
            isset($this->params["dtHeaderClass"]) ?
            $this->params["dtHeaderClass"] :
            'bg-primary';

        $this->dtParams["dtFixedHeader"] =
            isset($this->params["dtFixedHeader"]) ?
            $this->params["dtFixedHeader"] :
            false;

        $this->dtParams["dtSearching"] =
            isset($this->params["dtSearching"]) ?
            $this->params["dtSearching"] :
            true;

        $this->dtParams["dtPaging"] =
            isset($this->params["dtPaging"]) ?
            $this->params["dtPaging"] :
            false;

        $this->dtParams["dtLengthMenu"] =
            isset($this->params["dtLengthMenu"]) ?
            $this->escaper->escapeJs($this->helper->encode($this->params["dtLengthMenu"])) :
            $this->escaper->escapeJs($this->helper->encode([20, 40, 60, 80, 100]));

        $this->dtParams["dtSelect"] =
            isset($this->params["dtSelect"]) ?
            $this->params["dtSelect"] :
            false;

        $this->dtParams["dtSelectAll"] =
            isset($this->params["dtSelectAll"]) ?
            $this->params["dtSelectAll"] :
            false;

        $this->dtParams["dtSelectStyle"] =
            isset($this->params["dtSelectStyle"]) ?
            $this->params["dtSelectStyle"] :
            'single';

        $this->dtParams["dtDisableColumnsOrdering"] =
            isset($this->params["dtDisableColumnsOrdering"]) ?
            $this->helper->encode($this->params["dtDisableColumnsOrdering"]) :
            $this->helper->encode([]);

        $this->dtParams["dtZeroRecords"] =
            isset($this->params["dtZeroRecords"]) ?
            $this->params["dtZeroRecords"] :
            'missing dtZeroRecords';

        $this->dtParams["dtNotificationTextFromColumn"] =
            isset($this->params["dtNotificationTextFromColumn"]) ?
            $this->params["dtNotificationTextFromColumn"] :
            'name';

        $this->dtParams["dtSendConfirmRemove"] =
            isset($this->params["dtSendConfirmRemove"]) ?
            $this->params["dtSendConfirmRemove"] :
            false;

        $this->dtParams["colTextTruncate"] =
            isset($this->params["colTextTruncate"]) ?
            $this->params["colTextTruncate"] :
            true;

        $this->content .=
            '<script type="text/javascript">
            if (!window["dataCollection"]["' . $this->params["componentId"] . '"]) {
                window["dataCollection"]["' . $this->params["componentId"] . '"] = { };
            }

            window["dataCollection"]["' . $this->params["componentId"] . '"]["' . $this->params["componentId"] . '-' . $this->params["sectionId"] . '"] =
                $.extend(
                    window["dataCollection"]["' . $this->params["componentId"] . '"]["' . $this->params["componentId"] . '-' . $this->params["sectionId"] . '"],
                            {
                                "listOptions"       : {
                                    "componentName"         : "' . $this->params["componentName"] . '",
                                    "tableName"             : "' . $this->params["componentId"] . '-' . $this->params["sectionId"] . '-table",
                                    "postUrl"               : "' . $this->dtParams["dtPostUrl"] . '",
                                    "postParams"            : JSON.parse("' . $this->dtParams["dtPostUrlParams"] . '"),
                                    "datatable"     : {
                                        "columns"                           : "' . $this->dtParams["dtColumns"] . '",
                                        "colTextTruncate"                   : "' . $this->dtParams["colTextTruncate"] . '",
                                        "tableCompact"                      : "' . $this->dtParams["dtTableCompact"] . '",
                                        "NoOfColumnsToShow"                 : ' . $this->dtParams["dtNoOfColumnsToShow"] . ',
                                        "addIdColumn"                       : "' . $this->dtParams["dtAddIdColumn"] . '",
                                        "hideIdColumn"                      : "' . $this->dtParams["dtHideIdColumn"] . '",
                                        "showHideColumnsButton"             : "' . $this->dtParams["dtShowHideColumnsButton"] . '",
                                        "showHideColumnsButtonType"         : "' . $this->dtParams["dtShowHideColumnsButtonType"] . '",
                                        "showHideExportButton"              : "' . $this->dtParams["dtShowHideExportButton"] . '",
                                        "colReorder"                        : "' . $this->dtParams["dtColReorder"] . '",
                                        "order"                             : JSON.parse("' . $this->dtParams["dtOrder"] . '"),
                                        "stateSave"                         : "' . $this->dtParams["dtStateSave"] . '",
                                        "headerClass"                       : "' . $this->dtParams["dtHeaderClass"] . '",
                                        "fixedHeader"                       : "' . $this->dtParams["dtFixedHeader"] . '",
                                        "searching"                         : "' . $this->dtParams["dtSearching"] . '",
                                        "responsive"                        : "' . $this->dtParams["dtResponsive"] . '",
                                        "paging"                            : "' . $this->dtParams["dtPaging"] . '",
                                        "lengthMenu"                        : JSON.parse("' . $this->dtParams["dtLengthMenu"] . '"),
                                        "select"                            : "' . $this->dtParams["dtSelect"] . '",
                                        "selectAll"                         : "' . $this->dtParams["dtSelectAll"] . '",
                                        "selectStyle"                       : "' . $this->dtParams["dtSelectStyle"] . '",
                                        "disableColumnsOrdering"            : JSON.parse("' . $this->dtParams["dtDisableColumnsOrdering"] . '"),
                                        "zeroRecords"                       : "' . $this->dtParams["dtZeroRecords"] . '",
                                        "notificationTextFromColumn"        : "' . $this->dtParams["dtNotificationTextFromColumn"] . '",//What text to show on delete message
                                        "sendConfirmRemove"                 : "' . $this->dtParams["dtSendConfirmRemove"] . '"
                                    }
                                },
                                "customFunctions" : {
                                    "beforeTableInit"   : function() { "use strict"; },
                                    "afterTableInit"    : function() { "use strict"; },
                                    "beforeRedraw"      : function() { "use strict"; },
                                    "afterRedraw"       : function() { "use strict"; }
                                }
                            });';

        $this->content .= '</script>';
    }

    protected function generateRowsContent()
    {
        $this->dtParams['dtShowRowControls'] =
            isset($this->params["dtShowRowControls"]) ?
            $this->params["dtShowRowControls"] :
            true;

        $rowsData = [];

        $this->dtParams['dtNotificationTextFromColumn'] =
            isset($this->params['dtNotificationTextFromColumn']) ?
            $this->params['dtNotificationTextFromColumn'] :
            'id';

        foreach ($this->params['dtRows'] as $rowId => $columns) {
            $rowData = [];

            foreach ($columns as $columnKey => $column) {
                if (isset($this->params['dtReplaceColumns']) && is_array($this->params['dtReplaceColumns'])) {
                    foreach ($this->params['dtReplaceColumns'] as $replaceColumnKey => $replaceColumn) {
                        if ($replaceColumnKey === $columnKey) {
                            foreach ($replaceColumn as $replaceValueKey => $replaceValue) {
                                if ($replaceValueKey === 'html') {
                                    foreach ($replaceValue as $valueKey => $value) {
                                        if ($valueKey == $column) {
                                            $column = $value;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($columnKey === '__control') {
                    $controlbuttons = '';
                    $controlButtons = [];

                    if (isset($this->params['dtAdditionControlButtonsBeforeControlButtons']) &&
                        $this->params['dtAdditionControlButtonsBeforeControlButtons'] === true
                    ) {
                        $controlButtons = array_merge($controlButtons, $this->additionalControlButtons($controlButtons, $columns));
                    }

                    foreach ($column as $controlKey => $control) {

                        $this->dtParams['dtControlsLinkClass'] =
                            isset($this->params['dtControlsLinkClass']) ?
                            $this->params['dtControlsLinkClass'] :
                            'contentAjaxLink';

                        if ($controlKey === 'view') {

                            if (is_array($control)) {
                                $control = $control['link'];
                                $title =
                                    isset($control['title']) ?
                                    strtoupper($control['title']) :
                                    'VIEW';
                                $icon =
                                    isset($control['icon']) ?
                                    strtoupper($control['icon']) :
                                    'eye';
                                $type =
                                    isset($control['type']) ?
                                    strtoupper($control['type']) :
                                    'info';

                            } else {
                                $title = 'VIEW';
                                $icon = 'eye';
                                $type = 'info';
                            }

                            $controlButtons = array_merge($controlButtons,
                                [
                                    $controlKey =>
                                    [
                                        'title'             => $title,
                                        'additionalClass'   => 'rowView ' . $this->dtParams['dtControlsLinkClass'],
                                        'icon'              => $icon,
                                        'buttonType'        => $type,
                                        'link'              => $control
                                    ]
                                ]
                            );
                        } else if ($controlKey === 'edit') {

                            if (is_array($control)) {
                                $control = $control['link'];
                                $title =
                                    isset($control['title']) ?
                                    strtoupper($control['title']) :
                                    'EDIT';
                                $icon =
                                    isset($control['icon']) ?
                                    strtoupper($control['icon']) :
                                    'edit';
                                $type =
                                    isset($control['type']) ?
                                    strtoupper($control['type']) :
                                    'primary';

                            } else {
                                $title = 'EDIT';
                                $icon = 'edit';
                                $type = 'primary';
                            }

                            $controlButtons = array_merge($controlButtons,
                                [
                                    $controlKey =>
                                    [
                                        'title'             => $title,
                                        'additionalClass'   => 'rowEdit ' . $this->dtParams['dtControlsLinkClass'],
                                        'icon'              => $icon,
                                        'buttonType'        => $type,
                                        'link'              => $control
                                    ]
                                ]
                            );
                        } else if ($controlKey === 'remove') {

                            if (is_array($control)) {
                                $control = $control['link'];
                                $title =
                                    isset($control['title']) ?
                                    strtoupper($control['title']) :
                                    'REMOVE';
                                $icon =
                                    isset($control['icon']) ?
                                    strtoupper($control['icon']) :
                                    'trash';
                                $type =
                                    isset($control['type']) ?
                                    strtoupper($control['type']) :
                                    'danger';

                            } else {
                                $title = 'REMOVE';
                                $icon = 'trash';
                                $type = 'danger';
                            }

                            $controlButtons = array_merge($controlButtons,
                                [
                                    $controlKey =>
                                    [
                                        'title'             => $title,
                                        'additionalClass'   => 'rowRemove',
                                        'icon'              => $icon,
                                        'buttonType'        => $type,
                                        'link'              => $control
                                    ]
                                ]
                            );
                        }
                    }

                    if (!isset($this->params['dtAdditionControlButtonsBeforeControlButtons']) ||
                        (isset($this->params['dtAdditionControlButtonsBeforeControlButtons']) &&
                               $this->params['dtAdditionControlButtonsBeforeControlButtons'] === false)
                    ) {
                        $controlButtons = array_merge($controlButtons, $this->additionalControlButtons($controlButtons, $columns));
                    }

                    if (count($controlButtons) === 1) {
                        foreach ($controlButtons as $controlButtonKey => $controlButton) {
                            $controlbuttons .=
                                '<a href="' . $controlButton['link'] . '" id="' . $this->params['componentId'] . '-' . $controlButtonKey . '-' . $columnKey . '-' . $rowId . '" type="button" data-id="' . $columns['id'] . '" data-rowid="' . $rowId . '" class="ml-1 mr-1 pl-2 pr-2 text-white btn btn-' . $controlButton['buttonType'] . ' btn-xs ' . $controlButton['additionalClass'] . '" data-notificationtextfromcolumn="' . $this->dtParams['dtNotificationTextFromColumn'] . '"><i class="mr-1 fas fa-fw fa-xs fa-' . $controlButton['icon'] . '"></i><span class="text-xs"> ' . strtoupper($controlButton['title']) . '</span></a>';
                        }
                    } else if (count($controlButtons) > 1) {
                        $count = 0;
                        foreach ($controlButtons as $controlButtonKey => $controlButton) {
                            if ($count === 0) {
                                $controlbuttons .=
                                    '<div class="btn-group"><a href="' . $controlButton['link'] . '" id="' . $this->params['componentId'] . '-' . $controlButtonKey . '-' . $columnKey . '-' . $rowId . '" type="button" data-id="' . $columns['id'] . '" data-rowid="' . $rowId . '" class="pl-2 pr-2 text-white btn btn-' . $controlButton['buttonType'] . ' btn-xs ' . $controlButton['additionalClass'] . '" data-notificationtextfromcolumn="' . $this->dtParams['dtNotificationTextFromColumn'] . '"><i class="mr-1 fas fa-fw fa-xs fa-' . $controlButton['icon'] . '"></i> <span class="text-xs">' . strtoupper($controlButton['title']) . '</span></a><button type="button" class="pl-2 pr-2 btn btn-default btn-xs dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span><div class="dropdown-menu dropdown-menu-right" role="menu">';
                            } else {
                                if ($controlButtonKey === 'divider') {
                                    $controlbuttons .= $controlButton;
                                } else {
                                    $controlbuttons .=
                                        '<a href="' . $controlButton['link'] . '" id="' . $this->params['componentId'] . '-' . $controlButtonKey . '-' . $columnKey . '-' . $rowId . '" data-id="' . $columns['id'] . '" data-rowid="' . $rowId . '" class="dropdown-item text-' . $controlButton['buttonType'] . ' ' . $controlButton['additionalClass'] . '" data-notificationtextfromcolumn="' . $this->dtParams['dtNotificationTextFromColumn'] . '"><i class="mr-1 fas fa-fw fa-xs fa-' . $controlButton['icon'] . '"></i> <span class="text-xs">' . strtoupper($controlButton['title']) . '</span></a>';
                                }
                            }
                            $count++;
                        }
                        $controlbuttons .= '</div></button></div>';
                    }

                    $column = $controlbuttons;

                    // $this->dtParams['dtShowRowControls'] = true;
                }

                $rowData = array_merge($rowData, [$columnKey => $column]);
            }
            $rowsData = array_merge($rowsData, [$rowData]);
        }

        $this->content .=
            $this->helper->encode(
                [
                    'data'              => $rowsData,
                    'pagination'        => isset($this->params["dtPagination"]) ? $this->params["dtPagination"] : false,
                    'paginationCounters'=> isset($this->params["dtPaginationCounters"]) ? $this->params["dtPaginationCounters"] : false,
                    'rowControls'       => $this->dtParams['dtShowRowControls'],
                    'replaceColumns'    => isset($this->params["dtReplaceColumns"]) ? $this->params["dtReplaceColumns"] : false
                ]
            );
    }

    protected function additionalControlButtons($controlButtons, $columns)
    {
        if (!isset($this->params['dtAdditionControlButtons'])) {
            return $controlButtons;
        }

        if (count($controlButtons) > 1) {
            $controlButtons = array_merge($controlButtons,
                [
                    'divider'   => '<div class="dropdown-divider"></div>'
                ]
            );
        }

        foreach ($this->params['dtAdditionControlButtons']['buttons'] as $additionControlButtonKey => $additionControlButton) {
            if (isset($this->params['dtAdditionControlButtons']['includeId'])) {
                if (isset($this->params['dtAdditionControlButtons']['includeQ']) &&
                    $this->params['dtAdditionControlButtons']['includeQ']
                ) {
                    $additionControlButtonLink = $additionControlButton['link'] . '/q/id/' . $columns['id'];
                } else {
                    $additionControlButtonLink = $additionControlButton['link'] . '/id/' . $columns['id'];
                }
            } else {
                $additionControlButtonLink = $additionControlButton['link'];
            }

            $controlButtons = array_merge($controlButtons,
                [
                    $additionControlButtonKey =>
                        [
                            'title'             =>
                                isset($additionControlButton['title']) ?
                                $additionControlButton['title'] :
                                'MISSING TITLE',
                            'additionalClass'   =>
                                isset($additionControlButton['additionalClass']) ?
                                $additionControlButton['additionalClass'] :
                                'contentAjaxLink',
                            'icon'              =>
                                isset($additionControlButton['icon']) ?
                                $additionControlButton['icon'] :
                                'circle',
                            'buttonType'        =>
                                isset($additionControlButton['buttonType']) ?
                                $additionControlButton['buttonType'] :
                                'primary',
                            'link'              => $additionControlButtonLink
                        ]
                ]
            );
        }

        return $controlButtons;
    }
}