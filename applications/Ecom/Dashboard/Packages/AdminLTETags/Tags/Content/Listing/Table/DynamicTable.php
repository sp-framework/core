<?php

namespace Applications\Ecom\Dashboard\Packages\AdminLTETags\Tags\Content\Listing\Table;

use Applications\Ecom\Dashboard\Packages\AdminLTETags\AdminLTETags;
use Applications\Ecom\Dashboard\Packages\AdminLTETags\Tags\Content\Listing\Filters;
use Phalcon\Helper\Json;

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
        $this->adminLTETags = new AdminLTETags();

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
        if (isset($this->params['dtPrimaryButtons']) || isset($this->params['dtFilter'])) {
            $this->content .=
                '<div class="row mb-2">';

            if (isset($this->params['dtFilter']) && $this->params['dtFilter'] === true) {
                $this->content .=
                    (new Filters($this->view, $this->tag, $this->links, $this->escaper))->getContent($this->params);
            }

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

            $this->content .= '</div>';
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
                    <table id="' . $this->params['componentId'] . '-' . $this->params['sectionId'] . '-table" class="table ' . $this->dtParams['dtStriped'] . ' ' . $this->dtParams['dtBordered'] . $compact . ' dt-responsive" width="100%" cellspacing="0">
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
                $this->escaper->escapeJs(Json::encode([$this->params["dtColumns"]]));
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
            $this->escaper->escapeJs(Json::encode($this->params["dtPostUrlParams"])) :
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

        $this->dtParams["dtStateSave"] =
            isset($this->params["dtStateSave"]) ?
            $this->params["dtStateSave"] :
            false;

        $this->dtParams["dtFixedHeader"] =
            isset($this->params["dtFixedHeader"]) ?
            $this->params["dtFixedHeader"] :
            false;

        $this->dtParams["dtSearching"] =
            isset($this->params["dtSearching"]) ?
            $this->params["dtSearching"] :
            true;

        $this->dtParams["dtResponsive"] =
            isset($this->params["dtResponsive"]) ?
            $this->params["dtResponsive"] :
            true;

        $this->dtParams["dtPaging"] =
            isset($this->params["dtPaging"]) ?
            $this->params["dtPaging"] :
            false;

        $this->dtParams["dtLengthMenu"] =
            isset($this->params["dtLengthMenu"]) ?
            $this->escaper->escapeJs(Json::encode($this->params["dtLengthMenu"])) :
            $this->escaper->escapeJs(Json::encode([20, 40, 60, 80, 100]));

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
            Json::encode($this->params["dtDisableColumnsOrdering"]) :
            Json::encode([]);

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
                                        "stateSave"                         : "' . $this->dtParams["dtStateSave"] . '",
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
        $this->dtParams['dtRowControls'] = false;

        $rowsData = [];

        $this->dtParams['dtNotificationTextFromColumn'] =
            isset($this->params['dtNotificationTextFromColumn']) ?
            $this->params['dtNotificationTextFromColumn'] :
            'id';

        foreach ($this->params['dtRows'] as $rowId => $columns) {
            $rowData = [];

            foreach ($columns as $columnKey => $column) {
                if (isset($this->params['dtReplaceColumns'])) {
                    foreach ($this->params['dtReplaceColumns'] as $replaceColumnKey => $replaceColumn) {
                        if ($replaceColumnKey === $columnKey) {
                            foreach ($replaceColumn as $replaceValueKey => $replaceValue) {
                                if ($replaceValueKey === 'customSwitch') {
                                    if ($column == 1) {
                                        $checked = 'checked';
                                    }
                                    if (isset($replaceValue[0]['switchType']) &&
                                        isset($replaceValue[1]['switchType'])
                                    ) {
                                        $offSwitchType = 'custom-switch-off-' . $replaceValue[0]['switchType'];
                                        $onSwitchType = 'custom-switch-on-' . $replaceValue[1]['switchType'];
                                    }

                                    $customswitch =
                                        '<div class="form-group">' .
                                            '<div class="custom-control custom-switch ' . $offSwitchType . ' ' . $onSwitchType . '">
                                                <input type="checkbox" class="custom-control-input" id="' . $this->params['componentId'] . '-sections-listing-datatable-' . $columnKey . '-' . $rowId . '" data-switchactionurl="' . $replaceValue['actionUrl'] . '" data-switchactionincludecolumnsdata="' . $replaceValue['actionIncludeColumnsData'] . '" data-notificationtextfromcolumn="' . $this->dtParams['dtNotificationTextFromColumn'] . '" data-columnid="' . $columnKey . '" ' . $checked . '>
                                                <label class="custom-control-label" for="' . $this->params['componentId'] . '-sections-listing-datatable-' . $columnKey . '-' . $rowId . '"></label>
                                            </div>
                                        </div>';

                                    $column = $customswitch;
                                } else if ($replaceValueKey === 'radioButtons') {
                                    $radiobuttons =
                                        '<div class="btn-group btn-group-toggle" data-toggle="buttons" role="group" aria-label="' . $columnKey . ' group">';

                                    foreach ($replaceValue as $valueKey => $value) {
                                        if (is_array($value)) {
                                            if ($valueKey == $column) {
                                                $focusActive = 'focus active';
                                                $checked = 'checked';
                                            } else {
                                                $focusActive = '';
                                                $checked = '';
                                            }

                                            if (isset($value['buttonSize'])) {
                                                $buttonSize = $value['buttonSize'];
                                            } else {
                                                $buttonSize = 'sm';
                                            }

                                            if (isset($value['buttonType'])) {
                                                $buttonType = $value['buttonType'];
                                            } else {
                                                $buttonType = 'primary';
                                            }

                                            $radiobuttons .=
                                                '<label class="btn btn-' . $buttonSize . ' btn-outline-' . $buttonType . ' ' . $focusActive . '" style="cursor: pointer;">
                                                <input type="radio" name="' . $columnKey . '-options" id="' . $this->params['componentId'] . '-sections-listing-datatable-' . $columnKey . '-' . $rowId . '-' . $valueKey . '" autocomplete="off" ' . $checked . ' data-radiobuttonsactionurl="' . $replaceValue['actionUrl'] . '" data-radiobuttonsactionincludecolumnsdata="' . $replaceValue['actionIncludeColumnsData'] . '" data-notificationtextfromcolumn="' . $this->dtParams['dtNotificationTextFromColumn'] . '" data-columnid="' . $columnKey . '" data-value="' . $valueKey . '">' . $value['buttonTitle'] . '</label>';
                                        }
                                    }
                                    $radiobuttons .= '</div>';
                                    $column = $radiobuttons;
                                } else if ($replaceValueKey === 'html') {
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

                    foreach ($column as $controlKey => $control) {

                        $this->dtParams['dtControlsLinkClass'] =
                            isset($this->params['dtControlsLinkClass']) ?
                            $this->params['dtControlsLinkClass'] :
                            'contentAjaxLink';

                        if ($controlKey === 'view') {
                            $controlButtons = array_merge($controlButtons,
                                [
                                    $controlKey =>
                                    [
                                        'title'             => 'VIEW',
                                        'additionalClass'   => 'rowView ' . $this->dtParams['dtControlsLinkClass'],
                                        'icon'              => 'eye',
                                        'buttonType'        => 'info',
                                        'link'              => $control
                                    ]
                                ]
                            );
                        } else if ($controlKey === 'edit' || $controlKey === 'update') {
                            $controlButtons = array_merge($controlButtons,
                                [
                                    $controlKey =>
                                    [
                                        'title'             => 'EDIT',
                                        'additionalClass'   => 'rowEdit ' . $this->dtParams['dtControlsLinkClass'],
                                        'icon'              => 'edit',
                                        'buttonType'        => 'warning',
                                        'link'              => $control
                                    ]
                                ]
                            );
                        } else if ($controlKey === 'remove' || $controlKey === 'delete') {
                            $controlButtons = array_merge($controlButtons,
                                [
                                    $controlKey =>
                                    [
                                        'title'             => 'REMOVE',
                                        'additionalClass'   => 'rowRemove',
                                        'icon'              => 'trash',
                                        'buttonType'        => 'danger',
                                        'link'              => $control
                                    ]
                                ]
                            );
                        }
                    }

                    if (isset($this->params['dtAdditionControlButtons'])) {
                        $controlButtons = array_merge($controlButtons,
                            [
                                'divider'   => '<div class="dropdown-divider"></div>'
                            ]
                        );
                        foreach ($this->params['dtAdditionControlButtons'] as $additionControlButtonKey => $additionControlButton) {
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
                                            'link'              => $additionControlButton['link']
                                        ]
                                ]
                            );
                        }
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

                    $this->dtParams['dtRowControls'] = true;
                }

                $rowData = array_merge($rowData, [$columnKey => $column]);
            }
            $rowsData = array_merge($rowsData, [$rowData]);
        }

        $this->content .=
            Json::encode(
                [
                    'data'              => $rowsData,
                    'pagination'        => isset($this->params["dtPagination"]) ? $this->params["dtPagination"] : false,
                    'paginationCounters'=> isset($this->params["dtPaginationCounters"]) ? $this->params["dtPaginationCounters"] : false,
                    'rowControls'       => $this->dtParams['dtRowControls'],
                    'replaceColumns'    => isset($this->params["dtReplaceColumns"]) ? $this->params["dtReplaceColumns"] : false
                ]
            );
    }
}