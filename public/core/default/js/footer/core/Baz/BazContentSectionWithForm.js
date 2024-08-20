/* globals define exports BazContentFieldsValidator BazContentFields paginatedPNotify Pace BazCore BazContentLoader */
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
            thatV,
            formJsTreeSelector;
        var types = { };
        var tabIds = [];
        var validationObject = { };

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

                if (options.task === 'validateForm') {
                    this._validateForm();
                } else if (options.task === 'sectionToObj') {
                    this._sectionToObj();
                } else {
                    validationObject = {
                        'componentId'   : componentId,
                        'sectionId'     : sectionId,
                        'on'            : 'section'
                    }

                    if ($('#' + sectionId + '-form-fields').length > 0) {
                        this._buildFormJsTree();
                        validationObject['formJsTreeSelector'] = formJsTreeSelector;
                    }

                    BazContentFieldsValidator.initValidator(validationObject);

                    BazContentFields.init({
                        'componentId'   : componentId,
                        'sectionId'     : sectionId
                    });
                    this._initSectionButtonsAndActions();
                }

                if ($('.btn-tool-reset-cache').length === 1) {
                    if (dataCollection.env.currentId == '0') {
                        $('.btn-tool-reset-cache').hide();
                    } else {
                        $('.btn-tool-reset-cache').addClass('contentAjaxLink');

                        var url = window.location.href;

                        if (!url.includes('resetcache')) {
                            $('.btn-tool-reset-cache').attr('href', url + '/q/resetcache/true');
                        } else {
                            $('.btn-tool-reset-cache').attr('href', url);
                        }

                        BazCore.bazContent();
                    }
                }
            };

            _proto._validateForm = function _validateForm() {
                validationObject = {
                    'componentId'     : componentId,
                    'sectionId'       : sectionId,
                    'onSuccess'       : false,
                    'type'            : 'section',
                    'preValidated'    : false,
                    'formId'          : null,
                    'on'              : 'section'
                }
                if ($('#' + sectionId + '-form-fields').length > 0) {
                    validationObject['formJsTreeSelector'] = formJsTreeSelector;
                }
                var validated = BazContentFieldsValidator.validateForm(validationObject);

                return validated;
            };

            _proto._redoFormJsTree = function _redoFormJsTree() {
                $(formJsTreeSelector).jstree('destroy');
                that._buildFormJsTree();
                BazContentFields.init({'fieldId':formJsTreeSelector[0].id});

                validationObject = {
                    'on'              : 'section',
                    'type'            : 'section',
                    'formLocation'    : sectionId
                }
                if ($('#' + sectionId + '-form-fields').length > 0) {
                    validationObject['formJsTreeSelector'] = formJsTreeSelector;
                }
                BazContentFieldsValidator.cancelValidatingForm(validationObject);//cancel any form validation as jstree has changed
            }

            _proto._buildFormJsTree = function _buildFormJsTree() {
                formJsTreeSelector = $('#' + sectionId + '-form-fields');

                $(formJsTreeSelector).empty();

                // Create Tree
                $(formJsTreeSelector).append('<ul></ul>');

                // Grab Icons from Controller
                types.item = {"icon" : "fas fa-fw fa-angle-right"};

                // Grab Fields from Tabs Note: attr "jstree-search" is used to populate tree
                $('#' + sectionId + '-form .nav-tabs li a').each(function() {
                    if ($(this).data('jstree') == false) {
                        return;
                    }

                    var tabId = $(this).attr('href').replace('#', '');
                    tabIds.push(tabId);
                    var tabName = $(this).html().toUpperCase();

                    types[tabId] = {"icon" : "fas fa-fw fa-chevron-right"};

                    $(formJsTreeSelector).
                        find('ul').
                        first().
                        append(
                            '<li data-tabid="' + tabId + '" data-jstree=' + '{"type":"' + tabId + '"} class="text-uppercase">' + tabName +
                            '<ul data-tabid="' + tabId + '-ul"></ul>' +
                            '</li>'
                        );

                    // $('#' + sectionId + '-form-fields li:contains("' + tabName + '")').first().append('<ul></ul>');

                    $('#' + tabId).find("[jstree-search]").each(function() {
                        var fieldId = this.id.replace('-jstreesearch', '');

                        if ($(this).attr('jstree-search') !== '') {
                            if (!$('#' + fieldId).parents('.form-group').hasClass('d-none') &&
                                $('#' + fieldId).attr('disabled') !== 'disabled'
                            ) {
                                    // $('#' + sectionId + '-form-fields li:contains("' + tabName + '")').
                                    // find('ul').
                                    // first().
                                    $('[data-tabid="' + tabId + '-ul"]').append(
                                        '<li data-tabid="' + tabId +
                                            '" data-jstreeid="' + $(this)[0].id +
                                            '" data-jstree=' + '{"type":' + '"item"}>' +
                                            $(this).attr('jstree-search') +
                                        '</li>'
                                );
                            }
                        }
                    });
                });

                dataCollection[componentId][sectionId][sectionId + '-form-fields'] =
                    $.extend(dataCollection[componentId][sectionId][sectionId + '-form-fields'], {
                        "types" : types,
                        "plugins": ["search","types"],
                        "search": {
                            'show_only_matches': true,
                            'search_callback'  : function(str, node) {
                                var word, words = [];
                                var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
                                if (searchFor.indexOf(',') >= 0) {
                                    words = searchFor.split(',');
                                } else {
                                    words = [searchFor];
                                }
                                for (var i = 0; i < words.length; i++) {
                                    word = words[i];
                                    if ((node.text || "").indexOf(word) >= 0) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                        }
                    });

                // Init jstree selection process
                $(formJsTreeSelector).on('select_node.jstree', function() {
                    var selfId = $(this).jstree('get_selected',true)[0];
                    // if (selfId.parent === '#') {
                        $(formJsTreeSelector).jstree('open_node', selfId);
                    // } else {
                        $(tabIds).each(function(index,tabId) {
                            var tab = $('#' + sectionId + '-form').find('[href="#' + tabId + '"]');

                            $(tab).removeClass('active');
                            $(tab).attr('area-selected', false);
                            $('#' + tabId).removeClass('active show');
                        })

                        var activateTab = $('#' + sectionId + '-form').find('[href="#' + selfId.data.tabid + '"]');
                        $(activateTab).addClass('active');
                        $(activateTab).attr('area-selected', true);

                        $('#' + selfId.data.tabid).addClass('active show');

                        $('#' + selfId.data.jstreeid).parent().addClass('bg-info disabled animated fadeIn');
                        setTimeout(function() {
                            $('#' + selfId.data.jstreeid).parent().removeClass('bg-info disabled animated fadeIn');
                        }, 2000);
                    // }
                });
            }

            _proto._initSectionButtonsAndActions = function _initSectionButtonsAndActions() {
                if ($('#' + sectionId + '-id').val() === '' || $('#' + sectionId + '-id').val() === '0') {
                    $('#' + sectionId + ' .card-footer button.addData').attr('hidden', false);
                    if ($('#' + sectionId + ' .card-footer button.addData').length === 1) {
                        $('#' + sectionId + ' .card-footer button.cancelForm').attr('hidden', false);
                    } else {
                        $('#' + sectionId + ' .card-footer button.closeForm').attr('hidden', false);
                    }
                } else if ('#' + sectionId + ' .card-footer button.updateData') {
                    $('#' + sectionId + ' .card-footer button.updateData').attr('hidden', false);
                    if ($('#' + sectionId + ' .card-footer button.updateData').length === 1) {
                        $('#' + sectionId + ' .card-footer button.cancelForm').attr('hidden', false);
                    } else {
                        $('#' + sectionId + ' .card-footer button.closeForm').attr('hidden', false);
                    }
                }

                $('#' + sectionId + ' .card-footer button.closeForm, #' + sectionId + ' .card-footer button.cancelForm').click(function(e) {
                    e.preventDefault();

                    if ($(this).is('.closeForm')) {
                        $('body').trigger('sectionWithFormCloseForm');
                    }
                    if ($(this).is('.cancelForm')) {
                        $('body').trigger('sectionWithFormCancelForm');
                    }
                });

                $('#' + sectionId + ' .card-footer button.addData, #' + sectionId + ' .card-footer button.updateData').click(function(e) {
                    e.preventDefault();

                    var mainButton = this;

                    if (that._validateForm()) {
                        if (dataCollection.env.devmode && dataCollection.env.devmode == '1') {
                            $(this).children('i').attr('hidden', true);
                            $(this).attr('disabled', false);
                        } else {
                            $(this).children('i').attr('hidden', false);
                            $(this).attr('disabled', true);
                        }
                        // if ($('.dz-clickable').length > 0) {
                        //     $('.dz-clickable').each(function(index, dzButton) {
                        //         var dzButtonId = $(dzButton)[0].id;
                        //         var extractedButtonId = dzButtonId.replace("-dropzone-upload", "");

                        //         if (dataCollection[componentId][sectionId][extractedButtonId]["dropzone"].files.length > 0) {
                        //             dataCollection[componentId][sectionId][extractedButtonId].save();

                        //             dataCollection[componentId][sectionId][extractedButtonId]["dropzone"].on("queuecomplete", function() {
                        //                 that._runAjax(mainButton, $(mainButton).attr('actionurl'), $.param(that._sectionToObj()));
                        //             });
                        //         } else {
                        //             that._runAjax(mainButton, $(mainButton).attr('actionurl'), $.param(that._sectionToObj()));
                        //         }
                        //     });
                        // } else {
                            var sectionToObj = that._sectionToObj();

                            if (dataCollection[componentId][sectionId][sectionId + '-form']['onSubmit']) {
                                if (dataCollection[componentId][sectionId][sectionId + '-form']['onSubmit']()) {
                                    that._runAjax(mainButton, $(mainButton).attr('actionurl'), $.param(sectionToObj));
                                } else {
                                    $(mainButton).attr('disabled', false);
                                    $(mainButton).children('i').attr('hidden', true);
                                }
                            } else {
                                that._runAjax(mainButton, $(mainButton).attr('actionurl'), $.param(sectionToObj));
                            }

                        // }
                    }
                });
            }

            _proto._runAjax = function _runAjax(thisButtonId, url, dataToSubmit) {
                $.ajax({
                    'url'           : url,
                    'data'          : dataToSubmit,
                    'method'        : 'post',
                    'dataType'      : 'json',
                    'success'       : function(response) {
                                        if (dataCollection[componentId][sectionId][sectionId + '-form']['onSubmitResponse']) {
                                            if (!dataCollection[componentId][sectionId][sectionId + '-form']['onSubmitResponse'](response)) {
                                                return;
                                            }
                                        }

                                        if (response.responseCode == '0') {
                                            if ($(thisButtonId).data('successnotify') === true) {
                                                paginatedPNotify('success', {
                                                    title   : response.responseMessage,
                                                });
                                            }

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
                                                $(thisButtonId).parent().siblings('.card-body').load($(thisButtonId).attr('href'), response);
                                                $(thisButtonId).attr('disabled', false);
                                            } else if (!$(thisButtonId).data('actiontarget') || $(thisButtonId).data('actiontarget') === '') {
                                                $(thisButtonId).attr('disabled', false);
                                            }

                                            if ($(thisButtonId).is('.addData')) {
                                                $('body').trigger('sectionWithFormDataAdded');
                                            }
                                            if ($(thisButtonId).is('.updateData')) {
                                                $('body').trigger('sectionWithFormDataUpdated');
                                            }
                                            if (dataCollection[componentId] && dataCollection[componentId][sectionId][sectionId + '-form']['onSuccessResponse']) {
                                                dataCollection[componentId] && dataCollection[componentId][sectionId][sectionId + '-form']['onSuccessResponse'](response);
                                            }
                                        } else {
                                            $(thisButtonId).attr('disabled', false);
                                            paginatedPNotify('error', {
                                                title   : response.responseMessage
                                            });
                                            dataCollection[componentId][sectionId]['dataToSubmit'] = { };
                                            if (response.tokenKey && response.token) {
                                                $('#security-token').attr('name', response.tokenKey);
                                                $('#security-token').val(response.token);
                                            }

                                            if (dataCollection[componentId] && dataCollection[componentId][sectionId][sectionId + '-form']['onErrorResponse']) {
                                                dataCollection[componentId] && dataCollection[componentId][sectionId][sectionId + '-form']['onErrorResponse'](response);
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

                                if ($(thatV)[0]['multiple'] || $(thatV).data('create') == true) {
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
                                dataCollection[componentId][sectionId]['data'][extractComponentId] = { };
                                $(bazScanField).find('span').each(function(i,v) {
                                    var thisCounterId = $(v).parent('div')[0].id;
                                    dataCollection[componentId][sectionId]['data'][extractComponentId][thisCounterId] = $(v).html();
                                });
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'html') {//html
                            thatV = bazScanField;
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if (!dataCollection[componentId][sectionId]['data'][extractComponentId]) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = $(bazScanField).html();
                                }
                            }
                        } else if ($(bazScanField).data('bazscantype') === 'jstree') {//jstree, only object is created, data is generated in the html file.
                            thatV = bazScanField;
                            if ($(bazScanField).data('bazpostoncreate') === true ||
                                $(bazScanField).data('bazpostonupdate') === true ||
                                $(bazScanField).data('bazdevpost') === true) {
                                if (!dataCollection[componentId][sectionId]['data'][extractComponentId]) {
                                    dataCollection[componentId][sectionId]['data'][extractComponentId] = { };
                                }
                            }
                        }

                        if (dataCollection[componentId][sectionId][$(bazScanField)[0].id] &&
                            dataCollection[componentId][sectionId][$(bazScanField)[0].id].afterExtract) {
                            dataCollection[componentId][sectionId][$(bazScanField)[0].id].afterExtract();
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
                if (dataCollection[componentId][sectionId].data.id === '' || !dataCollection[componentId][sectionId].data.id) {//Create
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
                dataCollection[componentId][sectionId]['dataToSubmit'][$('#security-token').attr('name')] = $('#security-token').val();

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