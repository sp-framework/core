/* globals define exports BazContentFieldsValidator PNotify Pace BazCore BazContentLoader */
/*
* @title                    : BazContentSectionWithForm
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionWithForm;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    'use strict';
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {
    'use strict';

    var BazContentSectionWithForm = function ($) {

        var NAME                    = 'BazContentSectionWithForm';
        var DATA_KEY                = 'baz.contentsectionwithform';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
            task                    : null
        };
        var dataCollection,
            componentId,
            sectionId,
            extractComponentId,
            that,
            thatV;

        var BazContentSectionWithForm = function () {
            function BazContentSectionWithForm(element, settings) {
                that = this;
                this._element = element;
                this._settings = settings;
            }

            var _proto = BazContentSectionWithForm.prototype;

            _proto._error = function _error(message) {
                throw new Error(message);
            };

            _proto._init = function _init(options) {
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;

                dataCollection = window['dataCollection'];

                if (!dataCollection[componentId]) {
                    dataCollection[componentId] = { };
                }
                if (!dataCollection[componentId][sectionId]) {
                    dataCollection[componentId][sectionId] = { };
                }

                $(this._element).BazContentFields();
                BazContentFieldsValidator.initValidator({
                    'componentId'   : componentId,
                    'sectionId'     : sectionId,
                    'on'            : 'section'
                });
                this._initSectionButtonsAndActions();
                if (options.task === 'validateForm') {
                    this._validateForm(options.buttonId);
                }
                if (options.task === 'sectionToObj') {
                    this._sectionToObj();
                }
            };

            _proto._validateForm = function _validateForm(thisButtonId) {
                var validated = BazContentFieldsValidator.validateForm({
                    'componentId'     : $(thisButtonId).parents('.component')[0].id,
                    'sectionId'       : $(thisButtonId).parents('.sectionWithForm')[0].id,
                    'task'            : 'validateForm',
                    'onSuccess'       : false,
                    'type'            : 'sections',
                    'preValidated'    : false,
                    'formId'          : null
                });
                return validated;
            };

            _proto._initSectionButtonsAndActions = function _initSectionButtonsAndActions() {
                $('#' + sectionId + ' .card-footer button.methodPost').each(function(index,button) {
                    $(button).click(function(e) {
                        e.preventDefault();
                        if (that._validateForm(this)) {
                            $(this).attr('disabled', true);
                            that._runAjax(this, $(this).attr('actionurl'), $.param(that._sectionToObj()));
                        }
                    });
                });
            };

            _proto._runAjax = function _runAjax(thisButtonId, url, dataToSubmit) {
                $.ajax({
                    'url'           : url,
                    'data'          : dataToSubmit,
                    'method'        : 'post',
                    'dataType'      : 'json',
                    'success'       : function(data) {
                                        if (data.status === 0) {
                                            PNotify.removeAll();
                                            PNotify.success({
                                                title   : decodeURI($(thisButtonId).data('notificationtitle')),
                                                text    : decodeURI($(thisButtonId).data('notificationmessage'))
                                            });
                                            if ($(thisButtonId).data('actiontarget') === 'mainContent') {
                                                BazContentLoader.loadAjax($(thisButtonId), {
                                                    ajaxBefore                      : function () {
                                                                                        Pace.restart();
                                                                                        $("#baz-content").empty();
                                                                                        $("#loader").attr('hidden', false);
                                                                                    },
                                                    ajaxFinished                    : function () {
                                                                                        BazCore.updateBreadcrumb();
                                                                                        $("#loader").attr('hidden', true);
                                                                                    },
                                                    ajaxError                       : function () {
                                                                                        $("#loader").attr('hidden', true);
                                                                                        BazCore.updateBreadcrumb();
                                                                                    }
                                                });
                                            } else if ($(thisButtonId).data('actiontarget') === 'cardBody') {
                                                $(thisButtonId).parent().siblings('.card-body').empty().append(
                                                    '<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>'
                                                    );
                                                $(thisButtonId).parent().siblings('.card-body').load($(thisButtonId).attr('href'),data);
                                                $(thisButtonId).attr('disabled', false);
                                            } else if (!$(thisButtonId).data('actiontarget') || $(thisButtonId).data('actiontarget') === '') {
                                                $(thisButtonId).attr('disabled', false);
                                            }
                                        } else {
                                            PNotify.removeAll();
                                            // Instead of error, something like contact BAZ link can be shown which can be diverted to form.
                                            PNotify.error({
                                                title   : 'Error!',
                                                text    : 'Contact Administrator'
                                            });
                                        }
                                    }
                });
            }

            _proto._sectionToObj = function _sectionToObj() {

                if (!dataCollection[componentId][sectionId]['data']) {
                    dataCollection[componentId][sectionId]['data'] = { };
                }
                if (!dataCollection[componentId][sectionId]['dataToSubmit']) {
                    dataCollection[componentId][sectionId]['dataToSubmit'] = { };
                }
                var stripComponentId;

                $('#' + sectionId).find('[data-bazscantype]').each(function(index,bazScanField) {
                    extractComponentId = $(bazScanField)[0].id.split('-');
                    extractComponentId = extractComponentId[extractComponentId.length - 1];
                    if (bazScanField.tagName !== 'FIELDSET' && $(bazScanField).parents('fieldset').data('bazscantype') !== 'datatable') {
                        if (bazScanField.tagName === 'INPUT' && bazScanField.type === 'checkbox') {
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if ($(bazScanField)[0].checked === true) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = '1';
                                } else {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = '0';
                                }
                            }
                        } else if (bazScanField.tagName === 'INPUT' || bazScanField.tagName === "TEXTAREA") {
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if ($(bazScanField)[0].value === 'undefined') {//kill if incorrect Data
                                    that._error('data is undefined!');
                                    return;
                                } else {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = $(bazScanField)[0].value;
                                }
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'select2') {
                            thatV = bazScanField;
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if ($(thatV).data('multiple')) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = [];
                                    $($(bazScanField)[0].selectedOptions).each(function(i,v){
                                        var thisSelectId = $(v)[0].value;
                                        var thisSelectName = $(v)[0].text;
                                        var thisSelectObject = { };
                                        thisSelectObject[thisSelectId] = thisSelectName;
                                        dataCollection[componentId][sectionId]['data'][extractComponentId].push(thisSelectObject);
                                    });
                                } else {
                                    if ($(thatV).val() === '') {
                                        dataCollection[componentId][sectionId]['data'][extractComponentId] = 0;
                                    } else {
                                        dataCollection[componentId][sectionId]['data'][extractComponentId] = $(thatV).val();
                                    }
                                }
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'radio' || $(bazScanField).data('bazscantype') === 'radio-button-group') {// icheck-radio
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                dataCollection[componentId][sectionId]['data'][extractComponentId] =
                                $(bazScanField).find('input[type=radio]:checked').data('value');
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'trumbowyg') {//trumbowyg
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                dataCollection[componentId][sectionId]['data'][extractComponentId] = $(bazScanField).trumbowyg('html');
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'counters') {//counters
                            thatV = bazScanField;
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                dataCollection[componentId][sectionId]['data'][extractComponentId] = [];
                                $(bazScanField).find('span').each(function(i,v) {
                                    var thisCounterId = $(v).parent('div')[0].id;
                                    var counterObject = { };
                                    counterObject[thisCounterId] = $(v).html();
                                    dataCollection[componentId][sectionId]['data'][extractComponentId].push(counterObject);
                                });
                            }
                        }
                    }
                });
                // Add tables data to dataCollection
                // for (var section in tableData) {
                //  for (var data in tableData[section]) {
                //      var excludeActions = false;
                //      var excludeSeqAndSort = false;
                //      var currentTableDataLength = 0;
                //      if ((sectionsOptions[data].bazdatatable.rowButtons.canDelete === true) || (sectionsOptions[data].bazdatatable.rowButtons.canEdit === true)) {
                //          excludeActions = true;
                //      }
                //      if (sectionsOptions[data].datatable.rowReorder === true) {
                //          excludeSeqAndSort = true;
                //      }
                //      dataCollection[componentId][section][data] = [];
                //      $.each(tableData[section][data].rows().data(), function(i,v) {
                //          var startAt = 0;
                //          if (excludeSeqAndSort && excludeActions) {
                //              currentTableDataLength = v.length - 3;
                //              startAt = 2;
                //          } else if (!excludeSeqAndSort && excludeActions) {
                //              currentTableDataLength = v.length - 1;
                //          } else if (excludeSeqAndSort && !excludeActions) {
                //              currentTableDataLength = v.length - 2;
                //              startAt = 2;
                //          }
                //          var thatI = i;
                //          dataCollection[componentId][section][data][i] = { };
                //          for (var j = 0; j < currentTableDataLength; j++) {
                //              var columnData;
                //              var columnDataHasId = v[startAt].match(/id="(.*?)"/g)
                //              if (columnDataHasId) {
                //                  columnData = (columnDataHasId.toString().match(/"(.*?)"/g)).toString().replace(/"/g, '');
                //              } else {
                //                  columnData = v[startAt];
                //              }
                //              dataCollection[componentId][section][data][thatI][dataTableFields[componentId][section][data][startAt]] = columnData;
                //              startAt++;
                //          }
                //      });
                // }
                if (dataCollection[componentId][sectionId].data.id === '') {//Create
                    $('#' + sectionId).find('[data-bazpostoncreate=true]').each(function() {
                        stripComponentId = $(this)[0].id.split('-');
                        stripComponentId = stripComponentId[stripComponentId.length - 1];
                        dataCollection[componentId][sectionId]['dataToSubmit'][stripComponentId] = dataCollection[componentId][sectionId].data[stripComponentId];
                    });
                } else {//Edit
                    $('#' + sectionId).find('[data-bazpostonupdate=true]').each(function() {
                        stripComponentId = $(this)[0].id.split('-');
                        stripComponentId = stripComponentId[stripComponentId.length - 1];
                        dataCollection[componentId][sectionId]['dataToSubmit'][stripComponentId] = dataCollection[componentId][sectionId].data[stripComponentId];
                    });
                }
                return dataCollection[componentId][sectionId]['dataToSubmit'];
            };

            BazContentSectionWithForm._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;
                dataCollection[componentId][sectionId]['BazContentSectionWithForm'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentSectionWithForm']) {
                    dataCollection[componentId][sectionId]['BazContentSectionWithForm'] = new BazContentSectionWithForm($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithForm']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentSectionWithForm'];
                    dataCollection[componentId][sectionId]['BazContentSectionWithForm'] = new BazContentSectionWithForm($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithForm']._init(options);
                }
            };

        return BazContentSectionWithForm;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
        $('body').find('.sectionWithForm').each(function() {
            // if ($(this).data('bazdevmodetools') === 'false' ||
            //     $(this).data('bazdevmodetools') === false) {
                BazContentSectionWithForm._jQueryInterface.call($(this));
            // }
        });
    });

    $.fn[NAME] = BazContentSectionWithForm._jQueryInterface;
    $.fn[NAME].Constructor = BazContentSectionWithForm;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentSectionWithForm._jQueryInterface;
    };

    return BazContentSectionWithForm;
}(jQuery);

exports.BazContentSectionWithForm = BazContentSectionWithForm;

Object.defineProperty(exports, '__esModule', { value: true });

}));