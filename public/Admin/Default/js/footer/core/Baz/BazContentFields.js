/* globals define exports PNotify */
/*
* @title                    : BazContentFields
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ section/componentID).BazContentFields;
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

    var BazContentFields = function ($) {

        var NAME                    = 'BazContentFields';
        var DATA_KEY                = 'baz.contentfields';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
        };
        var componentId,
            pnotifySound,
            dataCollection,
            sectionId,
            that;

        var BazContentFields = function () {
            function BazContentFields(element, settings) {
                that = this;
                this._element = element;
                this._settings = $.extend({}, Default, settings);

                if ($('body').find('.flatpickr-calendar').length > 0) {
                    $('body').find('.flatpickr-calendar').remove();
                }
                if ($('body').find('.dz-hidden-input').length > 0) {
                    $('body').find('.dz-hidden-input').remove();
                }

                this._init(this._settings);
                this._bazInitFields(this._settings);
            }

            var _proto = BazContentFields.prototype;

            _proto._error = function(message) {
                throw new Error(message);
            };

            _proto._init = function _init() {
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;
                dataCollection = window['dataCollection'];
                pnotifySound = new Audio(dataCollection.env.soundPath + 'pnotify.mp3'); //Notification sound
                // Grab Components HTML Code (future use)
                // if (!dataCollection[componentId].html){
                //     dataCollection[componentId].html = $('#' + componentId).parents('.container-fluid').html();
                // }
                // dataCollection[componentId][sectionId].html = $('#' + sectionId).html();

                // TODO Decide what to do with section without any fields.
                // I can only think of tabs being made available via section, which needs to be initialized.
                // ALSO TABS CAN HAVE OPTION TO ENABLE A PARTICULAR TABID or FIRST TAB ID
            };

            _proto._bazInitFields = function _bazInitFields() {
                // tableData[sectionId] = { };//building object used during save
                var minValText, maxValText, minLengthText, maxLengthText, thisFieldId;

                // Iterate through the component
                $('#' + sectionId).find('[data-bazscantype]').each(function(index,bazScanField) {
                    // if (bazScanField.tagName !== 'FIELDSET' && $(bazScanField).parents('fieldset').data('bazscantype') !== 'datatable') {
                        if (dataCollection[componentId][sectionId][bazScanField.id]) {
                            dataCollection[componentId][sectionId][bazScanField.id].bazScanType = bazScanField.dataset.bazscantype;
                            if (bazScanField.dataset.bazscantype === 'input') {
                                initInput(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'select2') {
                                initSelect2(bazScanField.id, sectionId, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'checkbox') {
                                initCheckbox(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'radio' || bazScanField.dataset.bazscantype === 'radio-button-group') {
                                initRadio(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'flatpickr') {
                                initFlatpickr(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'textarea') {
                                initTextarea(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'trumbowyg') {
                                initTrumbowyg(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'counters') {
                                initCounters(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'jstree') {
                                initJstree(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'html') {
                                initHTML(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'dropzone') {
                                initDropzone(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            } else if (bazScanField.dataset.bazscantype === 'datatable') {
                                initDatatable(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                            }
                        } else {
                            that._error('Individual sections parameters missing for ' + bazScanField.id);
                        }
                    // }
                });
                dataCollection[componentId][sectionId]['initFields'] = true;

                function maxLength(fieldId, options) {
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    if (fieldId.hasAttribute('minlength') ||
                        fieldId.hasAttribute('maxlength') ||
                        fieldId.hasAttribute('max')) {
                        if (fieldId.hasAttribute('maxlength')) {
                            maxLengthText = ' UsedChar: %charsTyped% MaxChar: %charsTotal%';
                        } else {
                            maxLengthText = '';
                        }
                        if (fieldId.hasAttribute('minlength')) {
                            minLengthText = 'MinChar: ' + fieldId.attributes.minlength.value + ' ';
                        } else {
                            minLengthText = '';
                        }
                        if (fieldId.hasAttribute('min')) {
                            minValText = 'MinVal: ' + fieldId.attributes.min.value + ' ';
                            options.customMaxAttribute = 'min';
                        } else {
                            minValText = '';
                        }
                        if (fieldId.hasAttribute('max')) {
                            maxValText = 'MaxVal: ' + fieldId.attributes.max.value + ' ';
                            options.customMaxAttribute = 'max';
                        } else {
                            maxValText = '';
                        }
                        options = $.extend({
                            currentInput            : $(fieldId),
                            alwaysShow              : true,
                            allowOverMax            : false,
                            thresholdAmount         : 5,
                            thresholdPercent        : 20,
                            message                 : minValText + maxValText + minLengthText + maxLengthText,
                            placement               : 'top-right-inside'
                        }, options);
                        dataCollection[componentId][sectionId][thisFieldId]['maxlength'] = $(fieldId).maxlength(options);
                    }
                }

                // Restricts input for each element in the set of matched elements to the given fieldInputTypeTextFilter.
                function applyInputFilter(field, filter) {
                    if (!$.fn.inputFilter) {
                        (function($) {
                            $.fn.inputFilter = function(inputFilter) {
                                return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
                                    if (inputFilter(this.value)) {
                                        this.oldValue = this.value;
                                        this.oldSelectionStart = this.selectionStart;
                                        this.oldSelectionEnd = this.selectionEnd;
                                    } else if (this.hasOwnProperty("oldValue")) {
                                        this.value = this.oldValue;
                                        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                                    } else {
                                        this.value = "";
                                    }
                                });
                            };
                        }(jQuery));
                    }

                    if (filter === 'int') {
                        $(field).inputFilter(function(value) {
                          return /^-?\d*$/.test(value);
                        });
                    } else if (filter === 'positiveInt') {
                        $(field).inputFilter(function(value) {
                          return /^\d*$/.test(value);
                        });
                    } else if (filter === 'positiveIntMax') {
                        $(field).inputFilter(function(value) {
                          return /^\d*$/.test(value) && (value === "" || parseInt(value) <= $(field).attr('max'));
                        });
                    } else if (filter === 'float') {
                        $(field).inputFilter(function(value) {
                          return /^-?\d*[.]?\d*$/.test(value);
                        });
                    } else if (filter === 'positiveFloat') {
                        $(field).inputFilter(function(value) {
                          return /^\d*[.]?\d*$/.test(value);
                        });
                    } else if (filter === 'positiveFloatMax') {
                        $(field).inputFilter(function(value) {
                          return /^\d*[.]?\d*$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
                        });
                    } else if (filter === 'percent') {
                        $(field).inputFilter(function(value) {
                          return /^-?\d*[.]?\d{0,2}$/.test(value);
                        });
                    } else if (filter === 'positivePercent') {
                        $(field).inputFilter(function(value) {
                          return /^\d*[.]?\d{0,2}$/.test(value);
                        });
                    } else if (filter === 'positivePercentMax') {
                        $(field).inputFilter(function(value) {
                          return /^\d*[.]?\d{0,2}$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
                        });
                    } else if (filter === 'currency') {
                        $(field).inputFilter(function(value) {
                          return /^-?\d*[.,]?\d{0,2}$/.test(value);
                        });
                    } else if (filter === 'positiveCurrency') {
                        $(field).inputFilter(function(value) {
                          return /^\d*[.,]?\d{0,2}$/.test(value);
                        });
                    } else if (filter === 'positiveCurrencyMax') {
                        $(field).inputFilter(function(value) {
                          return /^\d*[.,]?\d{0,2}$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
                        });
                    } else if (filter === 'char') {
                        $(field).inputFilter(function(value) {
                          return /^[a-z]*$/i.test(value);
                        });
                    } else if (filter === 'hex') {
                        $(field).inputFilter(function(value) {
                          return /^[0-9a-f]*$/i.test(value);
                        });
                    }
                }

                function initInput(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    var buttonId, button, buttonArr;

                    if (fieldId.previousElementSibling && fieldId.previousElementSibling.children[0]) {
                        if (fieldId.previousElementSibling.children[0].classList.contains('dropdown-toggle')) {
                            buttonArr = fieldId.previousElementSibling.children[1].querySelectorAll('a');
                            for (button = buttonArr.length - 1; button >= 0; button--) {
                                buttonId = buttonArr[button].id;
                                if (options[buttonId]) {
                                    buttonArr[button].addEventListener('click', function(buttonId) {
                                        options[buttonId.target.id]();//call function
                                    }, false);
                                }
                            }
                        } else if (!fieldId.previousElementSibling.children[0].classList.contains('dropdown-toggle')) {
                            if (fieldId.previousElementSibling.children[0].tagName === 'BUTTON') {
                                buttonId = fieldId.previousElementSibling.children[0].id;
                                if (options[buttonId]) {
                                    buttonId.addEventListener('click', function(buttonId) {
                                        options[buttonId]();//call function
                                    }, false);
                                }
                            }
                        }
                    }
                    if (fieldId.nextElementSibling && fieldId.nextElementSibling.children[0]) {
                        if (fieldId.nextElementSibling.children[0].classList.contains('dropdown-toggle')) {
                            buttonArr = fieldId.nextElementSibling.children[1].querySelectorAll('a');
                            for (button = buttonArr.length - 1; button >= 0; button--) {
                                buttonId = buttonArr[button].id;
                                if (options[buttonId]) {
                                    buttonArr[button].addEventListener('click', function(buttonId) {
                                        options[buttonId.target.id]();//call function
                                    }, false);
                                }
                            }
                        } else if (!fieldId.nextElementSibling.children[0].classList.contains('dropdown-toggle')) {
                            if (fieldId.nextElementSibling.children[0].tagName === 'BUTTON') {
                                buttonId = fieldId.nextElementSibling.children[0].id;
                                if (options[buttonId]) {
                                    buttonId.addEventListener('click', function(buttonId) {
                                        options[buttonId]();//call function
                                    }, false);
                                }
                            }
                        }
                    }
                    if ($(fieldId).attr('type') === 'text' && $(fieldId).data('fieldinputfilter')) {
                        applyInputFilter($(fieldId), $(fieldId).data('fieldinputfilter'));
                    }
                    maxLength(thisFieldId, options);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initSelect2(fieldId, sectionId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    options = $.extend({
                        placeholder: 'MISSING PLACEHOLDER'
                    }, options);
                    dataCollection[componentId][sectionId][thisFieldId]['select2'] = $(fieldId).select2(options);
                    // validation
                    if (dataCollection[componentId][sectionId][sectionId + '-form'] &&
                        dataCollection[componentId][sectionId][sectionId + '-form'].rules[thisFieldId] === 'required') {
                        $(fieldId).on('change.select2', function() {
                            $(this).valid();
                        });
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initCheckbox(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initRadio(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    // Remove checked radio bg on toggle
                    if ($(fieldId).find('label.btn').length > 0) {
                        $(fieldId).find('label.btn').each(function() {
                            $(this).click(function() {
                                $(this).siblings('label.btn').each(function() {
                                    $(this).removeClass(function(index, css) {
                                        return (css.match(/\bbg-\S+/g) || []).join(' ');
                                    });
                                });
                            });
                        });
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initFlatpickr(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit();
                    }
                    thisFieldId = fieldId;
                    fieldId = $('#' + fieldId).parent();
                    options = $.extend({
                        wrap            : true,
                        enableTime      : true,
                        dateFormat      : 'd/m/Y h:i K',
                        minuteIncrement : 1
                    }, options);
                    if ($(fieldId).data('flatpickr-mode') === 'multiple') {
                        options = $.extend({
                            mode : 'multiple'
                        }, options);
                    }
                    if ($(fieldId).data('flatpickr-mode') === 'range') {
                        options = $.extend({
                            mode : 'range'
                        }, options);
                    }
                    dataCollection[componentId][sectionId][thisFieldId]['flatpickr'] = $(fieldId).flatpickr(options);
                    if ($(fieldId).find('#' + thisFieldId + '-clear').length > 0) {
                        $('#' + thisFieldId + '-clear').click(function() {
                            dataCollection[componentId][sectionId][thisFieldId]['flatpickr'].clear();
                            dataCollection[componentId][sectionId][thisFieldId]['flatpickr'].close();
                        });
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initTextarea(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    maxLength(thisFieldId, options);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initTrumbowyg(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    options = $.extend({
                        imageWidthModalEdit: true,
                        urlProtocol: true,
                        tagsToRemove: ['script', 'link'],
                        btnsDef: {
                            image: {
                                dropdown: ['insertImage', 'base64', 'upload', 'noembed'],
                                ico: 'insertImage'
                            }
                        },
                        btns: [
                            ['viewHTML', 'formatting', 'historyUndo', 'historyRedo'],
                            ['fontfamily', 'fontsize', 'superscript', 'subscript'],
                            ['strong', 'em', 'del', 'lineheight', 'preformatted', 'specialChars'],
                            ['foreColor', 'backColor', 'link', 'image'],
                            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                            ['unorderedList', 'orderedList', 'table', 'horizontalRule'],
                            ['removeformat', 'fullscreen']
                        ],
                        plugins: {
                            table: {
                                rows: 4,
                                columns: 4
                            }
                        }
                    }, options);
                    dataCollection[componentId][sectionId][thisFieldId]['trumbowyg'] =
                        $(fieldId).trumbowyg(options);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initCounters(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initJstree(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit();
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    options = $.extend({ }, options);
                    // Init
                    dataCollection[componentId][sectionId][thisFieldId]['jstree'] = $(fieldId).jstree(options);
                    // Search
                    if (options.bazJstreeOptions.search == null || options.bazJstreeOptions.search) {
                        $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                        $('#' + thisFieldId + '-tree-search-input').on('keyup', function() {
                            $(fieldId).jstree(true).search($(this).val());
                        });
                    }
                    var selectedNode;
                    // Add New Node
                    if (options.bazJstreeOptions.add == null || options.bazJstreeOptions.add) {
                        $('#' + thisFieldId + '-tools-add').attr('hidden', false);
                        $('#' + thisFieldId + '-tools-add').click(function(e) {
                            e.preventDefault();
                            selectedNode = $(fieldId).jstree('get_selected', true);
                            // Check if node are selected and only 1 is selected
                            if ($(selectedNode).length !== 1) {
                                PNotify.removeAll();
                                PNotify.notice({
                                    title: 'None or Multiple ' + options.bazJstreeOptions.treeName + ' selected!',
                                    text: 'Please select only 1 ' + options.bazJstreeOptions.treeName + ' to create a new node under it'
                                });
                                pnotifySound.play();
                                return false;
                            } else {
                                $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', true);
                                $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().attr('hidden', true);
                                $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().attr('hidden', false);
                                $('#' + thisFieldId + '-tree-add-input').focus();
                                $('#' + thisFieldId + '-tree-add-input-cancel').click(function() {
                                    $('#' + thisFieldId + '-tree-add-input').val(null);
                                    $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                                    $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().attr('hidden', true);
                                    $('#' + thisFieldId + '-tree-add-input-success').off();
                                });
                                $('#' + thisFieldId + '-tree-add-input-success').click(function() {
                                    modifyJsTree($(fieldId), thisFieldId, 'addNode', this, $('#' + thisFieldId + '-tree-add-input'), selectedNode, options.bazJstreeOptions.addFunction);
                                });
                                $('#' + thisFieldId + '-tree-add-input').keypress(function() {
                                    var keycode = (event.keyCode ? event.keyCode : event.which);
                                    if(keycode == '13'){
                                        modifyJsTree($(fieldId), thisFieldId, 'addNode', this, $('#' + thisFieldId + '-tree-add-input-success'), selectedNode, options.bazJstreeOptions.addFunction);
                                    }
                                });
                            }
                        });
                    }
                    // Edit Selected Node
                    if (options.bazJstreeOptions.edit == null || options.bazJstreeOptions.edit) {
                        $('#' + thisFieldId + '-tools-edit').attr('hidden', false);
                        $('#' + thisFieldId + '-tools-edit').click(function() {
                        selectedNode = $(fieldId).jstree('get_selected', true);
                        // Check if node are selected and only 1 is selected
                            if ($(selectedNode).length !== 1) {
                                PNotify.removeAll();
                                PNotify.notice({
                                    title: 'None or Multiple ' + options.bazJstreeOptions.treeName + ' selected!',
                                    text: 'Please select only 1 ' + options.bazJstreeOptions.treeName + ' to rename',
                                });
                                pnotifySound.play();
                                return false;
                            } else {
                                $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', true);
                                $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().attr('hidden', false);
                                $('#' + thisFieldId + '-tree-edit-input').val(selectedNode[0].text).focus();
                                $('#' + thisFieldId + '-tree-edit-input-cancel').click(function() {
                                    $('#' + thisFieldId + '-tree-edit-input').val(null);
                                    $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                                    $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().attr('hidden', true);
                                    $('#' + thisFieldId + '-tree-edit-input-success').off();
                                });
                                $('#' + thisFieldId + '-tree-edit-input-success').click(function() {
                                    modifyJsTree($(fieldId), thisFieldId, 'editNode', this, $('#' + thisFieldId + '-tree-edit-input'), selectedNode, options.bazJstreeOptions.editFunction);
                                });
                                $('#' + thisFieldId + '-tree-edit-input').keypress(function() {
                                    var keycode = (event.keyCode ? event.keyCode : event.which);
                                    if(keycode == '13'){
                                        modifyJsTree($(fieldId), thisFieldId, 'editNode', this, $('#' + thisFieldId + '-tree-edit--input-success'), selectedNode, options.bazJstreeOptions.editFunction);
                                    }
                                });
                            }
                        });
                    }
                    // Collapse all Nodes
                    if (options.bazJstreeOptions.collapse == null || options.bazJstreeOptions.collapse) {
                        $('#' + thisFieldId + '-tools-collapse').attr('hidden', false);
                        $('#' + thisFieldId + '-tools-collapse').click(function(e) {
                            e.preventDefault();
                            $(fieldId).jstree('deselect_all');
                            $(fieldId).jstree('close_all');
                        });
                    }
                    // Expand all Nodes
                    if (options.bazJstreeOptions.expand == null || options.bazJstreeOptions.expand) {
                        $('#' + thisFieldId + '-tools-expand').attr('hidden', false);
                        $('#' + thisFieldId + '-tools-expand').click(function(e) {
                            e.preventDefault();
                            $(fieldId).jstree('deselect_all');
                            $(fieldId).jstree('open_all');
                        });
                    }
                    // First Open
                    if (options.bazJstreeOptions.firstOpen == null || options.bazJstreeOptions.firstOpen) {
                        var firstId = $(fieldId)[0].children[0].children[0].id;
                        $(fieldId).jstree('open_node', firstId);
                    }
                    //All Open
                    if (options.bazJstreeOptions.allOpen == null || options.bazJstreeOptions.allOpen) {
                        $(fieldId).jstree('open_all');
                    }
                    // Show all children if root is clicked
                    if (options.bazJstreeOptions.toggleAllChildren == null || options.bazJstreeOptions.toggleAllChildren) {
                        $(fieldId).on('select_node.jstree', function(e, data) {
                            if (data.node.children.length > 0) {
                                $(fieldId).jstree('open_all', data.node.id);
                            }
                        });
                        $(fieldId).on('close_node.jstree', function(e, data) {
                            $(fieldId).jstree('deselect_node', data.node.id);
                        });
                    }
                    // Select only EndNode to perform actions
                    if (options.bazJstreeOptions.selectEndNodeOnly == null || options.bazJstreeOptions.selectEndNodeOnly) {
                        $(fieldId).on('select_node.jstree', function (e,data) {
                            if (data.node.children.length > 0) {
                                $(fieldId).jstree('deselect_node', data.node.id);
                            }
                        });
                    }
                    //HideAll Jstree default icons (only works if fieldJstreeDoubleClickToggle is set to true)
                    if (options.bazJstreeOptions.hideJstreeIcon == null || options.bazJstreeOptions.hideJstreeIcon) {
                        $(fieldId).find('.jstree-ocl').hide();
                        $(fieldId).on('open_node.jstree close_node.jstree', function() {
                            $(fieldId).find('.jstree-ocl').hide();
                        });
                    }
                    if ($(fieldId).parents('form').length !== 0) {
                        if (options[$(fieldId).parents('form')[0].id] && options[$(fieldId).parents('form')[0].id].rules[$(fieldId)[0].id + '-validate'] === 'required') {
                            $(fieldId).on('select_node.jstree', function() {
                                $('#' + $(this)[0].id + '-validate').val(null);
                                if ($(fieldId).jstree('get_selected', true).length > 0 ) {
                                    $('#' + $(this)[0].id + '-validate').val('selected');
                                    $('#' + $(this)[0].id + '-validate').valid();
                                    $(fieldId).removeClass('border-danger').addClass('border-default');
                                    $(fieldId).siblings('#' + $(this)[0].id + '-tree-search').find('.border-danger').removeClass('border-danger').addClass('border-default');
                                    $(fieldId).siblings('#' + $(this)[0].id + '-tree-search').find('.bg-danger').removeClass('bg-danger').addClass('bg-default');
                                }
                            });
                        }
                    }
                    // ModifyJsTree
                    function modifyJsTree(tree, optionsId, task, elthis, elthat, selectedNode, runFunction) {
                        if (task === 'addNode') {
                            tree.jstree('create_node',
                                $('#' + selectedNode[0].id),
                                $('#' + optionsId + '-tree-add-input').val(),
                                'last',
                                function() {
                                    tree.jstree('open_node', $('#' + selectedNode[0].id));
                                }
                            );
                            $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                            $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().attr('hidden', true);
                            $('#' + optionsId + '-tree-add-input').val(null);
                            $(elthis).off();
                            $(elthat).off();
                            runFunction();
                        } else if (task === 'editNode') {
                            tree.jstree('rename_node',
                                $('#' + selectedNode[0].id),
                                $('#' + optionsId + '-tree-edit-input').val()
                            );
                            $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().attr('hidden', false);
                            $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().attr('hidden', true);
                            $('#' + optionsId + '-tree-edit-input').val(null);
                            $(elthis).off();
                            $(elthat).off();
                            runFunction();
                        }
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initHTML(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit(dataCollection);
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initDropzone(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit();
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    dataCollection[componentId][sectionId][thisFieldId]['dropzone'] = $(fieldId).dropzone(options);
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }

                function initDatatable(fieldId, options) {
                    if (options.beforeInit) {
                        options.beforeInit();
                    }
                    thisFieldId = fieldId;
                    fieldId = document.getElementById(fieldId);
                    if (options.dataTables) {
                        for (var datatable in options.dataTables) {
                            var datatableTable = options.dataTables[datatable];
                            var datatableConfig = dataCollection[componentId][sectionId][datatableTable];
                            if (datatableConfig) {
                                if (datatableConfig.bazdatatable && datatableConfig.bazdatatable.compareData) {
                                    if (datatableConfig.bazdatatable.compareData.inclIds) {
                                        var datatableIncludes = datatableConfig.bazdatatable.compareData.inclIds;
                                        for (var datatableInclude in datatableIncludes) {
                                            var toolTipTitle = $('#' + datatableInclude).parents('.form-group').find('label').siblings('i').attr('title');
                                            toolTipTitle += '<br><span>NOTE: Field should be unique</span>';
                                            if (datatableIncludes[datatableInclude].length > 0) {
                                                toolTipTitle += '<br><span>UNIQUE KEYWORDS: ' + datatableIncludes[datatableInclude].toString() + '</span>';
                                            }
                                            $('#' + datatableInclude).parents('.form-group').find('label').siblings('i').attr('title', toolTipTitle).addClass('text-warning');
                                        }
                                    }
                                    // NOTE: exclude is very difficult to narrow. Avoid using excludes and use twig template {{fieldUnique}}
                                }
                            } else {
                                that._error('Datatable ' + datatableTable + ' is defined, but no configuration assigned to it!')
                            }
                        }
                        // this._fieldsToDatatable(fieldId);
                    } else {
                        that._error('Tables not assigned to ' + thisFieldId + '. They need to be assigned in an array, please see documentation');
                    }
                    if (options.afterInit) {
                        options.afterInit(dataCollection);
                    }
                }
            };

            BazContentFields._jQueryInterface = function _jQueryInterface(options) {
                var data = $(this).data(DATA_KEY);

                var _options = $.extend({}, Default, options);

                if (!data) {
                    data = new BazContentFields($(this), _options);
                    $(this).data(DATA_KEY, typeof _options === 'string' ? data : _options);
                }
            };

        return BazContentFields;

        }();

    $.fn[NAME] = BazContentFields._jQueryInterface;
    $.fn[NAME].Constructor = BazContentFields;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentFields._jQueryInterface;
    };

    return BazContentFields;
}(jQuery);

exports.BazContentFields = BazContentFields;

Object.defineProperty(exports, '__esModule', { value: true });

}));