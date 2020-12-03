if (!window['bazDataCollection']['{{componentId}}']) {
    window['bazDataCollection']['{{componentId}}'] = { };
}
window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'] = $.extend(window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'], {
    '{{componentId}}-{{sectionId}}-tree'   : {
        'bazJstreeOptions'  : {
            'treeName'              : 'Storage'
        },
        'core' : {
            'themes' : {
                'name'              : 'default',
                'dots'              : true
            },
            'dblclick_toggle'       : true,
            'multiple'              : false,
            'check_callback'        : true
        },
        'plugins'   : ["search", "types", "dnd"],
        'types'     : { },
    },
    '{{componentId}}-{{sectionId}}-table'   : {
        'listOptions'       : {
            'tableName'             : '{{componentId}}-{{sectionId}}-table',
            'datatable'     : {
                'columns'                           : ['id','entry','entry_size','changed','added','status','type','parent_id','mime'],
                'fixedHeader'                       : true,
                'responsive'                        : true,
                'paging'                            : true,
                'select'                            : true,
                'selectStyle'                       : 'multiple',
                'zeroRecords'                       : 'Please select a folder to view files',
                'notificationTextFromColumn'        : 'name'
            }
        }            
    },
    '{{componentId}}-{{sectionId}}-dropzone'   : {
        'url'   :   '/common/storage/upload'
    }
});