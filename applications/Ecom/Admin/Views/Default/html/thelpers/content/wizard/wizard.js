if (!window['bazDataCollection']['{{componentId}}']) {
    window['bazDataCollection']['{{componentId}}'] = { };
}
window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'] = 
    $.extend(window['bazDataCollection']['{{componentId}}']['{{componentId}}-{{sectionId}}'], {
        'showReview'                    : '{{wizardShowReview}}',
        'canCancel'                     : '{{wizardCanCancel}}',
        'steps'                         : JSON.parse('{{wizardSteps|json_encode|e("js")}}'),
    });