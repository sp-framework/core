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
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {

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

                if (!dataCollection[componentId][sectionId]['data']) {
                    dataCollection[componentId][sectionId]['data'] = { };
                }
                if (!dataCollection[componentId][sectionId]['dataToSubmit']) {
                    dataCollection[componentId][sectionId]['dataToSubmit'] = { };
                }

                $(this._element).BazContentFields();

                BazContentFieldsValidator.initValidator({
                    'componentId'   : componentId,
                    'sectionId'     : sectionId,
                    'on'            : 'section'
                });

                if (options.task === 'validateForm') {
                    this._validateForm(options.buttonId);
                } else if (options.task === 'sectionToObj') {
                    this._sectionToObj();
                } else {
                    this._initSectionButtonsAndActions();
                }
            };

            _proto._validateForm = function _validateForm() {
                var validated = BazContentFieldsValidator.validateForm({
                    'componentId'     : componentId,
                    'sectionId'       : sectionId,
                    'onSuccess'       : false,
                    'type'            : 'section',
                    'preValidated'    : false,
                    'formId'          : null
                });
                return validated;
            };

            _proto._initSectionButtonsAndActions = function _initSectionButtonsAndActions() {

                if ($('#' + sectionId + '-id').val() === '') {
                    $('#' + sectionId + ' .card-footer button.addData').attr('hidden', false);
                    $('#' + sectionId + ' .card-footer button.cancelForm').attr('hidden', false);
                } else if ('#' + sectionId + ' .card-footer button.updateData') {
                    $('#' + sectionId + ' .card-footer button.updateData').attr('hidden', false);
                    $('#' + sectionId + ' .card-footer button.cancelForm').attr('hidden', false);
                }

                $('#' + sectionId + ' .card-footer button.addData, #' + sectionId + ' .card-footer button.updateData').click(function(e) {
                    e.preventDefault();
                    if (that._validateForm()) {
                        $(this).children('i').attr('hidden', false);
                        $(this).attr('disabled', true);
                        that._runAjax(this, $(this).attr('actionurl'), $.param(that._sectionToObj()));
                    }
                });
            }

            _proto._runAjax = function _runAjax(thisButtonId, url, dataToSubmit) {
                $.ajax({
                    'url'           : url,
                    'data'          : dataToSubmit,
                    'method'        : 'post',
                    'dataType'      : 'json',
                    'success'       : function(data) {
                                        if (data.responseCode == '0') {
                                            PNotify.success({
                                                title   : data.responseMessage,
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
                                            $(thisButtonId).attr('disabled', false);
                                            PNotify.error({
                                                title   : data.responseMessage
                                            });
                                            dataCollection[componentId][sectionId]['dataToSubmit'] = { };
                                            if ($('#security-token').length === 1) {
                                                $('#security-token').attr('name', data.tokenKey);
                                                $('#security-token').val(data.token);
                                            }
                                        }
                                        $(thisButtonId).children('i').attr('hidden', true);
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
                                if ($(thatV)[0]['multiple']) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = { };
                                    dataCollection[componentId][sectionId]['data'][extractComponentId]['data'] = [];
                                    var select2Data = $(bazScanField).select2('data');
                                    var newTags = [];

                                    $(select2Data).each(function(i,v){
                                        if (v.newTag) {
                                            newTags.push(v.text);
                                        } else {
                                            var thisSelectId = v.id;
                                            var thisSelectName = v.text;

                                            if ($(thatV)[0]['multiple-object']) {
                                                var thisSelectObject = { };
                                                thisSelectObject[thisSelectId] = thisSelectName;
                                                dataCollection[componentId][sectionId]['data'][extractComponentId]['data'].push(thisSelectObject);
                                            } else {
                                                dataCollection[componentId][sectionId]['data'][extractComponentId]['data'].push(thisSelectId);
                                            }
                                        }
                                    });
                                    if (newTags.length > 0) {
                                        dataCollection[componentId][sectionId]['data'][extractComponentId]['newTags'] = newTags;
                                    }
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
                    var dataToSubmit;

                    $('#' + sectionId).find('[data-bazpostoncreate=true]').each(function() {
                        stripComponentId = $(this)[0].id.split('-');
                        stripComponentId = stripComponentId[stripComponentId.length - 1];
                        if (typeof dataCollection[componentId][sectionId].data[stripComponentId] === 'object' ||
                            $.isArray(dataCollection[componentId][sectionId].data[stripComponentId])
                        ) {
                            dataToSubmit = JSON.stringify(dataCollection[componentId][sectionId].data[stripComponentId]);
                        } else {
                            dataToSubmit = dataCollection[componentId][sectionId].data[stripComponentId];
                        }
                        dataCollection[componentId][sectionId]['dataToSubmit'][stripComponentId] = dataToSubmit;
                    });
                } else {//Edit
                    $('#' + sectionId).find('[data-bazpostonupdate=true]').each(function() {
                        stripComponentId = $(this)[0].id.split('-');
                        stripComponentId = stripComponentId[stripComponentId.length - 1];
                        if (typeof dataCollection[componentId][sectionId].data[stripComponentId] === 'object' ||
                            $.isArray(dataCollection[componentId][sectionId].data[stripComponentId])
                        ) {
                            dataToSubmit = JSON.stringify(dataCollection[componentId][sectionId].data[stripComponentId]);
                        } else {
                            dataToSubmit = dataCollection[componentId][sectionId].data[stripComponentId];
                        }
                        dataCollection[componentId][sectionId]['dataToSubmit'][stripComponentId] = dataToSubmit;
                    });
                }
                //CSRF TOKEN
                if ($('#security-token').length === 1) {
                    dataCollection[componentId][sectionId]['dataToSubmit'][$('#security-token').attr('name')] =
                        $('#security-token').val();
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