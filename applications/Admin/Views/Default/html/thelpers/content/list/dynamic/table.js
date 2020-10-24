if (!window['bazDataCollection']['{{componentId}}']) {
    window['bazDataCollection']['{{componentId}}'] = { };
}
window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'] =
    $.extend(window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'], {
        '{{componentId}}-{{sectionId}}-table' : {
            'listOptions'       : {
                'componentName'         : '{{componentName}}',
                'tableName'             : '{{componentId}}-{{sectionId}}-table',
                'postURL'               : '{{table.ajax.url}}',
                'postParams'            : {
                    'operation'             : 'load',
                    'results'               : '{{sectionsListDatatableTableNoOfResultsToShow|default(20)}}'
                },
                'datatable'     : {
                    'columns'                           : JSON.parse('{{sectionsListDatatableTableColumns|json_encode|raw}}'),
                    'NoOfColumnsToShow'                 : '{{sectionsListDatatableTableNoOfColumnsToShow|default("3")}}',
                    'hasIdColumn'                       : '{{sectionsListDatatableTableHasIdColumn|default("true")}}',
                    'hideIdColumn'                      : '{{sectionsListDatatableTableHideIdColumn|default("true")}}',
                    'showHideColumnsButton'             : '{{sectionsListDatatableTableShowHideColumnsButton|default("true")}}',
                    'showHideExportButton'              : '{{sectionsListDatatableTableShowHideExportButton|default("true")}}',
                    'colReorder'                        : '{{sectionsListDatatableTableColReorder|default("false")}}',
                    'stateSave'                         : '{{sectionsListDatatableTableStateSave|default("false")}}',
                    'fixedHeader'                       : '{{sectionsListDatatableTableFixedHeader|default("true")}}',
                    'searching'                         : '{{sectionsListDatatableTableSearching|default("true")}}',
                    'responsive'                        : '{{sectionsListDatatableTableResponsive|default("true")}}',
                    'paging'                            : '{{sectionsListDatatableTablePaging|default("false")}}',
                    'lengthMenu'                        : JSON.parse('{{sectionsListDatatableTableLengthMenu|json_encode|e("js")}}'),
                    'select'                            : '{{sectionsListDatatableTableSelect|default("false")}}',
                    'selectAll'                         : '{{sectionsListDatatableTableSelectAll|default("false")}}',
                    'selectStyle'                       : '{{sectionsListDatatableTableSelectStyle|default("single")}}',
                    'disableColumnsOrdering'            : '{{sectionsListDatatableTableDisableColumnsOrdering|json_encode|e("js")}}',
                    'zeroRecords'                       : '{{sectionsListDatatableTableZeroRecords|default("missing sectionsListDatatableTableZeroRecords")}}',
                    'notificationTextFromColumn'        : '{{sectionsListDatatableTableNotificationTextFromColumn|default("name")}}',//What text to show on delete message
                    'sendConfirmRemove'                 : '{{sectionsListDatatableTableSendConfirmRemove|default("false")}}'
                }
            },
            'customFunctions' : {
                'beforeTableInit'   : function() { 'use strict'; },
                'afterTableInit'    : function() { 'use strict'; },
                'beforeRedraw'      : function() { 'use strict'; },
                'afterRedraw'       : function() { 'use strict'; }
            }
        }
    });
window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}']['{{componentId}}-{{sectionId}}-table']['listOptions']['postParams'] =
    $.extend(window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}']['{{componentId}}-{{sectionId}}-table']['listOptions']['postParams'],
        JSON.parse('{{table.ajax.params|json_encode|raw}}')
    );