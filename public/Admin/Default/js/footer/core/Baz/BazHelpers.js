/* exported BazHelpers */
/* globals */
/* 
* @title                    : BazHelpers
* @description              : Baz Helper Tools Lib (include Various helper tools)
* @developer                : guru@bazaari.com.au
* @usage                    : BazHelpers._function_(_options_);
* @functions                : 
* @options                  : 
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazHelpers = function() {
    'use strict';
    var BazHelpers = void 0;

    // Error
    function error(errorMsg) {
        throw new Error(errorMsg);
    }
    
    function bazModal(options) {
        var close, closeButton, createButton, updateButton, title, modalCentered, modalScrollable, modalSize, modalWidth;
        if (!options.modalId) {
            error('modalId not present!');
        } else {
            if (options.modalTitle) {
                title = options.modalTitle;
            } else {
                title = '';
            }
            if (options.modalCentered) {
                modalCentered = 'modal-dialog-centered';
            } else {
                modalCentered = '';
            }
            if (options.modalScrollable) {
                modalScrollable = 'modal-dialog-scrollable';
            } else {
                modalScrollable = '';
            }
            if (options.modalSize) {
                modalSize = 'modal-' + options.modalSize;
            } else {
                modalSize = '';
            }

            if (options.modalWidth) {
                modalWidth = 'style="max-width:' + options.modalWidth + ';"';
            } else {
                modalWidth = '';
            }
            if (options.modalButtons.close) {
                closeButton = '<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>';
                close = '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>'
            } else {
                closeButton = '';
                close = '';
            }
            if (options.modalButtons.create) {
                createButton = '<button type="button" class="btn btn-sm btn-' + options.modalButtons.create.type + '">' + options.modalButtons.create.title + '</button>'
            } else {
                createButton = '';
            }
            if (options.modalButtons.update) {
                updateButton = '<button type="button" class="btn btn-sm btn-' + options.modalButtons.update.type + '">' + options.modalButtons.update.title + '</button>'
            } else {
                updateButton = '';
            }
        }
        var modalHTML = 
            '<div class="modal fadeIn ' + options.modalAdditionalClasses + '" id="' + options.modalId + '-modal" tabindex="-1"  aria-labelledby="' + 
            options.modalId + '-label" aria-hidden="true" data-backdrop="' + options.modalBackdrop + '" data-keyboard="' + options.modalEscClose + '">' +
            '<div ' + modalWidth + ' class="modal-dialog ' + modalCentered + ' ' + modalScrollable + ' ' + modalSize + '" role="document">' +
            '<div class="modal-content rounded-0 ' + options.modalContentAdditionalClasses + '">';
        
        if (options.modalHeader) {
            modalHTML += 
                '<div class="modal-header border-bottom-0 rounded-0 bg-' + options.modalType + ' ' + options.modalHeaderAdditionalClasses + '"><h5 class="modal-title" id="' + 
                options.modalId + '-label">' + title + '</h5>' + close + '</div>';
        }

        modalHTML += '<div class="modal-body ' + options.modalBodyAdditionalClasses + '"></div>';            

        if (options.modalFooter) {
            modalHTML += '<div class="modal-footer ' + options.modalFooterAdditionalClasses + '">' +
                                closeButton +
                                createButton +
                                updateButton +
                            '</div>';
        }

        modalHTML += '</div></div></div>';
        
        $(options.modalAppendOn).append(modalHTML);
    }

    function bazCreateHtmlList(obj) {
        var output = '';
        output += '<ul>';
        Object.keys(obj).forEach(function(k) {
            if (typeof obj[k] == "object" && obj[k] !== null){
                output += '<li class="text-uppercase">' + k + ' => ';
                output += bazCreateHtmlList(obj[k]);
                output += '</li>';
            } else {
                output += '<li>' + k + ' => ' + obj[k] + '</li>'; 
            }
        });
        output += '</ul>';
        return output;
    }    

    function bazHelpersConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazHelpersConstructor) {
        BazHelpers = BazHelpersConstructor;
        BazHelpers.defaults = {
            'modalId'                               : '',
            'modalTitle'                            : '',
            'modalCentered'                         : false,
            'modalScrollable'                       : false,
            'modalSize'                             : 'lg',
            'modalWidth'                            : '',
            'modalAdditionalClasses'                : '',
            'modalBackdrop'                         : 'static',
            'modalEscClose'                         : true,
            'modalContentAdditionalClasses'         : '',
            'modalHeader'                           : true,
            'modalType'                             : 'primary',
            'modalHeaderAdditionalClasses'          : '',
            'modalBodyAdditionalClasses'            : '',
            'modalFooter'                           : true,
            'modalFooterAdditionalClasses'          : '',            
            'modalAppendOn'                         : 'body',
            'modalButtons'                          : {
                'close'                             : false,
                'create'                            : {
                    'id'                            : 'add',
                    'title'                         : 'Add',
                    'type'                          : 'primary',
                    'action'                        : 'post',
                    'actionUrl'                     : '{{createActionUrl}}',
                    'createSuccessRedirectUrl'      : '{{createSuccessRedirectUrl}}',
                    'createSuccessNotifyMessage'    : '{{createSuccessNotifyMessage}}'
                },
                'update'                            : {
                    'id'                            : 'update',
                    'title'                         : 'Update',
                    'type'                          : 'primary',
                    'action'                        : 'post',
                    'actionUrl'                     : '{{updateActionUrl}}',
                    'updateSuccessRedirectUrl'      : '{{updateSuccessRedirectUrl}}',
                    'createSuccessNotifyMessage'    : '{{createSuccessNotifyMessage}}'
                }
            }
        }
        BazHelpers.modal = function(options) {
            bazModal(_extends(BazHelpers.defaults, options));
        }
        BazHelpers.createHtmlList = function(options) {
            var objToHtml = bazCreateHtmlList(options.obj);
            return objToHtml;   
        }
    }

    setup(bazHelpersConstructor);

    return bazHelpersConstructor;
}();