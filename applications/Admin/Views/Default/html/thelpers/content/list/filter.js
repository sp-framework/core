if (!window['bazDataCollection']['{{componentId}}']) {
    window['bazDataCollection']['{{componentId}}'] = { };
}
window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'] = 
    $.extend(window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'], {
        '{{componentId}}-{{sectionId}}-columns' : {
            placeholder: "SELECT COLUMN TO FILTER",
        },
        '{{componentId}}-{{sectionId}}-operator' : {
            placeholder: "SELECT FILTER OPERATOR",
        },
        '{{componentId}}-{{sectionId}}-value' : {
            placeholder: "FILTER VALUE",
        },
        '{{componentId}}-{{sectionId}}-condition' : {
            placeholder: "FILTER CONDITION",
        },        
        '{{componentId}}-{{sectionId}}-saved-filters' : {
            placeholder: "SELECT SAVED FILTERS AND APPLY",
        },
        '{{componentId}}-{{sectionId}}-name' : {
            placeholder: "FILTER NAME",
        },
        '{{componentId}}-{{sectionId}}-form' : {
            rules: {
                '{{componentId}}-{{sectionId}}-columns' : 'required',
            },
            messages: {
                '{{componentId}}-{{sectionId}}-columns' : 'Please select a column',
            }
        },
        '{{componentId}}-{{sectionId}}-datatable' : {
            'dataTables' : ['{{componentId}}-{{sectionId}}-table']
        },
        '{{componentId}}-{{sectionId}}-table' :{
            'tableTitle' : 'Filters',
            'datatable' : {
                'responsive': true,
                'rowReorder': false,
                'searching': false,
                'paging': false
            },
            'bazdatatable' : {
                'rowButtons': {
                    'canEdit': true,
                    'canDelete': true,
                }
            },
            'postSuccess': function(datatable, extractedData) {
                'use strict';
            }
        }
    }
);