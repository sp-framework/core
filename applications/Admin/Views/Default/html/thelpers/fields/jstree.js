if (!window['bazDataCollection']['{{componentId}}']) {
    window['bazDataCollection']['{{componentId}}'] = { };
}
window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'] = $.extend(window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'], {
    '{{componentId}}-{{fieldId}}' : {
        'bazJstreeOptions': {
            'rootIcon': 'fa fa-folder-open-o',
            'treeName': '{{fieldLabel|capitalize}}',
            'treePathSeparator': ' <i class="fa fa-angle-right text-sm"></i> ',
            'add': '{{fieldJstreeAdd|default(false)}}',
            'addFunction': function() {
                'use strict';
            },
            'edit': '{{fieldJstreeEdit|default(false)}}',
            'editFunction': function() {
                'use strict';
            },
            'search' : '{{fieldJstreeSearch}}',
            'expand': '{{fieldJstreeExpand|default(false)}}',
            'collapse': '{{fieldJstreeCollapse|default(false)}}',
            'firstOpen': '{{fieldJstreeFirstOpen|default(false)}}',
            'allOpen' : '{{fieldJstreeAllOpen|default(false)}}',
            'toggleAllChildren': '{{fieldJstreeToggleAllChildren|default(false)}}',
            'inclRoot': '{{fieldJstreeIncludeRootInPath|default(false)}}',
            'selectEndNodeOnly': '{{fieldJstreeSelectEndNodeOnly|default(false)}}',
            'hideJstreeIcon': '{{fieldJstreeHideJstreeIcons|default(false)}}'
        },
        'core': {
            'themes': {
                'name': '{{fieldJstreeTheme|default("default")}}',
                'dots': '{{fieldJstreeShowDots|default(false)}}'
            },
            'dblclick_toggle': '{{fieldJstreeDoubleClickToggle|default(false)}}',
            'check_callback': true,
            'multiple': '{{fieldJstreeMultiple|default(false)}}',
        },
        'plugins': ["search", "types", "dnd"],
        'search': {
            'show_only_matches': '{{fieldJstreeSearchShowOnlyMatches|default(false)}}',
            'show_only_matches_children': '{{fieldJstreeSearchShowOnlyMatchesChildren|default(false)}}',
            'case_sensitive' : '{{fieldJstreeSearchCaseSensitive|default(false)}}'
        }
    }
});