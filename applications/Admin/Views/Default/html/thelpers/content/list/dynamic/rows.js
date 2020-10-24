window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}']['{{componentId}}-{{sectionId}}-table']['listOptions']['datatable'] =
    $.extend(window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}']['{{componentId}}-{{sectionId}}-table']['listOptions']['datatable'], {
        'data'                              : JSON.parse('{{rowsData|json_encode|e("js")}}'),
        'pagination'                        : JSON.parse('{{pagination|json_encode|e("js")}}'),//Received from Controller
        'rowControls'                       : '{{sectionsListDatatableTableRowControls}}',
        'replaceColumns'                    : JSON.parse('{{sectionsListDatatableTableReplaceColumns|json_encode|e("js")}}')
    });